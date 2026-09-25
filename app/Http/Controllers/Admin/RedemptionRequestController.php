<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RedemptionRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RedemptionRequestController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:redemptions.view|redemptions.process', only: ['index', 'show']),
            new Middleware('permission:redemptions.process', only: ['updateStatus']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = RedemptionRequest::with(['user']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', function($row){ return $row->user->name ?? '-'; })
                ->editColumn('status', function($row){
                    $class = 'secondary';
                    if($row->status == 'Approved') $class = 'primary';
                    if($row->status == 'Shipped') $class = 'info text-dark';
                    if($row->status == 'Delivered') $class = 'success';
                    if($row->status == 'Rejected') $class = 'danger';
                    return '<span class="badge bg-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function($row){
                    $showUrl = route('admin.redemptions.show', $row->id);
                    return '<a href="'.$showUrl.'" class="btn btn-info btn-sm text-white"><i class="bi bi-box-seam"></i> Manage</a>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('admin.rewards.redemptions.index');
    }

    public function show($id)
    {
        $redemption = RedemptionRequest::with(['user', 'processor'])->findOrFail($id);
        return view('admin.rewards.redemptions.show', compact('redemption'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Approved,Shipped,Delivered,Rejected',
            'remarks' => 'nullable|string'
        ]);

        $redemption = RedemptionRequest::with('user')->findOrFail($id);

        if ($redemption->status == 'Rejected' || $redemption->status == 'Delivered') {
            return redirect()->back()->with('error', 'This request is already finalized and cannot be changed.');
        }

        DB::beginTransaction();
        try {
            // If it's being rejected, we must refund the points
            if ($request->status == 'Rejected' && $redemption->status != 'Rejected') {
                if ($redemption->user) {
                    $redemption->user->wallet_balance += $redemption->points_redeemed;
                    $redemption->user->save();
                    // Ideally log refund in PointLedger
                }
            }

            $redemption->status = $request->status;
            $redemption->remarks = $request->remarks;
            $redemption->processed_by = Auth::guard('admin')->id();
            $redemption->processed_at = now();
            $redemption->save();

            DB::commit();
            return redirect()->back()->with('success', 'Redemption request status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process request: ' . $e->getMessage());
        }
    }
}
