<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class InvoiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:invoice.view', only: ['index', 'show']),
            new Middleware('permission:invoice.verify', only: ['updateStatus']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $points_earn_amount = (float) (\App\Models\Setting::where('key', 'points_earn_amount')->value('value') ?? 100);
            $points_earn_reward = (float) (\App\Models\Setting::where('key', 'points_earn_reward')->value('value') ?? 1);

            $data = Invoice::with(['user', 'shop'])->whereHas('user', function($q) {
                $q->whereIn('user_type', ['Shop Boy', 'Installer']);
            })->orderByRaw("CASE status WHEN 'Pending' THEN 1 WHEN 'Verified' THEN 2 WHEN 'Rejected' THEN 3 ELSE 4 END")
              ->orderBy('created_at', 'desc');

            $admin = auth()->guard('admin')->user();
            if (!$admin->hasRole('Super Admin')) {
                if (!empty($admin->user_types) && count($admin->user_types) > 0) {
                    $data->whereHas('user', function($q) use ($admin) {
                        $q->whereIn('user_type', $admin->user_types);
                    });
                }
                if (!empty($admin->state_ids) && count($admin->state_ids) > 0) {
                    $data->whereHas('user', function($q) use ($admin) {
                        $q->whereIn('state_id', $admin->state_ids);
                    });
                } elseif ($admin->zone_id) {
                    $data->whereHas('user.state', function($q) use ($admin) {
                        $q->where('zone_id', $admin->zone_id);
                    });
                }
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', function($row){ return $row->user->name ?? '-'; })
                ->addColumn('shop_name', function($row){ return $row->shop->name ?? '-'; })
                ->editColumn('points_earned', function($row) use ($points_earn_amount, $points_earn_reward) {
                    if ($row->status == 'Pending') {
                        if ($points_earn_amount > 0) {
                            $expected = floor($row->amount / $points_earn_amount) * $points_earn_reward;
                            return '<span class="text-muted fst-italic" title="Expected Points">~ '.$expected.' pts</span>';
                        }
                        return '<span class="text-muted">-</span>';
                    }
                    return '<span class="fw-bold text-success">'.$row->points_earned.' pts</span>';
                })
                ->editColumn('status', function($row){
                    $class = 'warning text-dark';
                    if($row->status == 'Verified') $class = 'success';
                    if($row->status == 'Rejected') $class = 'danger';
                    return '<span class="badge bg-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function($row){
                    $showUrl = route('admin.invoices.show', $row->id);
                    return '<a href="'.$showUrl.'" class="btn btn-info btn-sm text-white"><i class="bi bi-eye"></i> View</a>';
                })
                ->rawColumns(['action', 'status', 'points_earned'])
                ->make(true);
        }
        return view('admin.transactions.invoices.index');
    }

    public function show($id)
    {
        $invoice = Invoice::with(['user', 'shop', 'verifier', 'items'])->findOrFail($id);
        $points_earn_amount = \App\Models\Setting::where('key', 'points_earn_amount')->value('value') ?? 100;
        $points_earn_reward = \App\Models\Setting::where('key', 'points_earn_reward')->value('value') ?? 1;
        
        return view('admin.transactions.invoices.show', compact('invoice', 'points_earn_amount', 'points_earn_reward'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Verified,Rejected',
            'points_earned' => 'required_if:status,Verified|numeric|min:0',
            'rejection_reason' => 'required_if:status,Rejected|nullable|string',
            'verification_remark' => 'nullable|string'
        ]);

        $invoice = Invoice::findOrFail($id);

        if ($invoice->status == 'Verified') {
            return redirect()->back()->with('error', 'This invoice is already verified and cannot be changed.');
        }

        DB::beginTransaction();
        try {
            $invoice->status = $request->status;
            $invoice->verification_remark = $request->verification_remark;
            
            if ($request->status == 'Verified') {
                $invoice->points_earned = $request->points_earned;
                $invoice->verified_by = Auth::guard('admin')->id();
                $invoice->verified_at = now();
                
                // Add points to user's wallet
                if ($invoice->user) {
                    $invoice->user->wallet_balance += $request->points_earned;
                    $invoice->user->save();
                    
                    // Note: Here we would ideally also insert a record into a `PointLedger` or `WalletTransaction` table
                }
            } elseif ($request->status == 'Rejected') {
                $invoice->rejection_reason = $request->rejection_reason;
                $invoice->verified_by = Auth::guard('admin')->id();
                $invoice->verified_at = now();
                $invoice->points_earned = 0;
            }

            $invoice->save();
            DB::commit();
            return redirect()->back()->with('success', 'Invoice status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }
}
