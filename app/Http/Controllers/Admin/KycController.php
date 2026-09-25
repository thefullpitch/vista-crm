<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class KycController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:kyc.view', only: ['index', 'show']),
            new Middleware('permission:kyc.verify', only: ['updateStatus']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::selectRaw('users.*, (
                aadhar_file IS NULL OR pan_file IS NULL OR bank_passbook_file IS NULL OR 
                aadhar_number IS NULL OR pan_number IS NULL OR bank_account_number IS NULL OR 
                address IS NULL OR state_id IS NULL OR city_id IS NULL OR pincode_id IS NULL
            ) as is_incomplete')
            ->orderBy('is_incomplete', 'desc')
            ->orderBy('created_at', 'desc');
                        
            return DataTables::of($data)
                ->addIndexColumn()
                ->setRowClass(function($row){
                    return $row->is_incomplete ? 'table-danger' : '';
                })
                ->addColumn('user_name', function($row){ 
                    if($row->is_incomplete) {
                        return $row->name . ' <span class="badge bg-danger ms-1" title="Incomplete Profile/KYC"><i class="bi bi-exclamation-triangle"></i></span>';
                    }
                    return $row->name; 
                })
                ->addColumn('user_type', function($row){ return $row->user_type ?? '-'; })
                ->editColumn('kyc_status', function($row){
                    $status = $row->kyc_status ?? 'Pending';
                    $class = 'secondary';
                    if($status == 'Approved' || $status == 'Verified') $class = 'success';
                    if($status == 'Rejected') $class = 'danger';
                    return '<span class="badge bg-'.$class.'">'.$status.'</span>';
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="'.route('admin.kyc.show', $row->id).'" class="btn btn-info btn-sm text-white"><i class="bi bi-eye"></i> View</a>';
                    return $btn;
                })
                ->rawColumns(['action', 'kyc_status', 'user_name'])
                ->make(true);
        }
        
        return view('admin.kyc.index');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.kyc.show', compact('user'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'kyc_status' => 'required|in:Pending,Approved,Rejected',
            'kyc_review_details' => 'nullable|string'
        ]);
        
        $user = User::findOrFail($id);
        
        $user->kyc_status = $request->kyc_status;
        $user->kyc_review_details = $request->kyc_review_details;
        $user->save();

        return redirect()->back()->with('success', 'KYC status updated successfully.');
    }
}
