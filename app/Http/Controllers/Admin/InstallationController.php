<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Installation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class InstallationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:installations.view', only: ['index', 'show']),
            new Middleware('permission:installations.verify', only: ['updateStatus']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $points_earn_amount = (float) (\App\Models\Setting::where('key', 'points_earn_amount')->value('value') ?? 100);
            $points_earn_reward = (float) (\App\Models\Setting::where('key', 'points_earn_reward')->value('value') ?? 1);

            $data = Installation::with(['user'])->whereHas('user', function($q) {
                $q->where('user_type', 'Installer');
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
                    $showUrl = route('admin.installations.show', $row->id);
                    return '<a href="'.$showUrl.'" class="btn btn-info btn-sm text-white"><i class="bi bi-eye"></i> View</a>';
                })
                ->rawColumns(['action', 'status', 'points_earned'])
                ->make(true);
        }
        return view('admin.transactions.installations.index');
    }

    public function show($id)
    {
        $installation = Installation::with(['user', 'verifier'])->findOrFail($id);
        $points_earn_amount = \App\Models\Setting::where('key', 'points_earn_amount')->value('value') ?? 100;
        $points_earn_reward = \App\Models\Setting::where('key', 'points_earn_reward')->value('value') ?? 1;
        
        return view('admin.transactions.installations.show', compact('installation', 'points_earn_amount', 'points_earn_reward'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Verified,Rejected',
            'points_earned' => 'required_if:status,Verified|numeric|min:0',
            'rejection_reason' => 'required_if:status,Rejected|nullable|string',
            'verification_remark' => 'nullable|string'
        ]);

        $installation = Installation::findOrFail($id);

        if ($installation->status == 'Verified') {
            return redirect()->back()->with('error', 'This installation is already verified and cannot be changed.');
        }

        DB::beginTransaction();
        try {
            $installation->status = $request->status;
            $installation->verification_remark = $request->verification_remark;
            
            if ($request->status == 'Verified') {
                $installation->points_earned = $request->points_earned;
                $installation->verified_by = Auth::guard('admin')->id();
                $installation->verified_at = now();
                
                // Add points to user's wallet
                if ($installation->user) {
                    $installation->user->wallet_balance += $request->points_earned;
                    $installation->user->save();
                    
                    // Note: Here we would ideally also insert a record into a `PointLedger` or `WalletTransaction` table
                }
            } elseif ($request->status == 'Rejected') {
                $installation->rejection_reason = $request->rejection_reason;
                $installation->verified_by = Auth::guard('admin')->id();
                $installation->verified_at = now();
                $installation->points_earned = 0;
            }

            $installation->save();
            DB::commit();
            return redirect()->back()->with('success', 'Installation status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update installation: ' . $e->getMessage());
        }
    }
}
