<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\State;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:users.view|users.create|users.edit|users.delete', only: ['index', 'show']),
            new Middleware('permission:users.create', only: ['create', 'store']),
            new Middleware('permission:users.edit', only: ['edit', 'update']),
            new Middleware('permission:users.delete', only: ['destroy']),
            new Middleware('permission:users.approve|users.reject|users.suspend', only: ['updateStatus']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with('state.zone')->selectRaw('users.*, (
                pan_number IS NULL OR aadhar_number IS NULL OR 
                bank_account_number IS NULL OR ifsc_code IS NULL OR 
                profile_photo IS NULL OR invoice_sample_file IS NULL
            ) as is_incomplete')
            ->orderBy('is_incomplete', 'desc')
            ->orderBy('created_at', 'desc');

            if ($request->has('user_type') && !empty($request->user_type)) {
                $data->where('user_type', $request->user_type);
            }

            $admin = auth()->guard('admin')->user();
            if (!$admin->hasRole('Super Admin')) {
                if (!empty($admin->user_types) && count($admin->user_types) > 0) {
                    $data->whereIn('user_type', $admin->user_types);
                }
                if (!empty($admin->state_ids) && count($admin->state_ids) > 0) {
                    $data->whereIn('state_id', $admin->state_ids);
                } elseif ($admin->zone_id) {
                    $data->whereHas('state', function($q) use ($admin) {
                        $q->where('zone_id', $admin->zone_id);
                    });
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('name', function($row){
                    if($row->is_incomplete) {
                        return $row->name . ' <span class="badge bg-danger ms-1" title="Action Required"><i class="bi bi-exclamation-triangle"></i></span>';
                    }
                    return $row->name . ' <span class="badge bg-success ms-1" title="Profile Complete"><i class="bi bi-check-circle"></i></span>';
                })
                ->editColumn('status', function($row){
                    $status = $row->status;
                    if ($status === 1 || $status === '1' || $status === 'Active') $status = 'Approved';
                    if ($status === 0 || $status === '0') $status = 'Inactive';
                    
                    $class = 'secondary';
                    if($status == 'Approved') $class = 'success';
                    if($status == 'Rejected') $class = 'danger';
                    if($status == 'Suspended') $class = 'dark';
                    if($status == 'Pending') $class = 'warning text-dark';
                    return '<span class="badge bg-'.$class.'">'.$status.'</span>';
                })
                ->addColumn('zone', function($row) {
                    return $row->state->zone->name ?? '<span class="text-muted">N/A</span>';
                })
                ->editColumn('user_type', function($row){
                    return $row->user_type ?? '<span class="text-muted">N/A</span>';
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group">';
                    if(auth()->guard('admin')->user()->can('users.view')) {
                        $btn .= '<a href="'.route('admin.users.show', $row->id).'" class="btn btn-info btn-sm text-white"><i class="bi bi-eye"></i></a>';
                    }
                    if(auth()->guard('admin')->user()->can('users.edit')) {
                        $btn .= '<a href="'.route('admin.users.edit', $row->id).'" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>';
                    }
                    if(auth()->guard('admin')->user()->hasRole('Super Admin')) {
                        $btn .= '<form action="'.route('admin.users.destroy', $row->id).'" method="POST" class="d-inline" onsubmit="return confirm(\'WARNING: Are you sure you want to delete this user? This will also delete ALL related data (invoices, installations, leads) permanently!\');">'
                             . csrf_field() . method_field('DELETE')
                             . '<button type="submit" class="btn btn-danger btn-sm text-white ms-1" title="Delete User"><i class="bi bi-trash"></i></button></form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'status', 'zone', 'user_type', 'name'])
                ->make(true);
        }
        
        return view('admin.users.index');
    }

    public function show($id)
    {
        $user = User::with(['state.zone', 'city', 'pincode'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::with('shops')->findOrFail($id);
        $states = State::all();
        $shops = collect();
        if ($user->pincode_id) {
            $shops = \App\Models\Shop::where('pincode_id', $user->pincode_id)->where('status', 'Active')->get();
        }
        // Always include currently assigned shops, even if they are from a different pincode
        if ($user->shops->isNotEmpty()) {
            $shops = $shops->merge($user->shops)->unique('id');
        }
        return view('admin.users.edit', compact('user', 'states', 'shops'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'mobile' => 'required|string|unique:users,mobile,'.$user->id,
            'wallet_balance' => 'nullable|numeric',
            'status' => 'required',
            'status_remark' => 'required|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'aadhar_number' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'aadhar_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'pan_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'bank_passbook_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'invoice_sample_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'shop_ids' => 'nullable|array',
            'shop_ids.*' => 'exists:shops,id'
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
                \Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = $request->file('profile_photo')->store('users', 'public');
        }

        if ($request->hasFile('aadhar_file')) {
            if ($user->aadhar_file && \Storage::disk('public')->exists($user->aadhar_file)) {
                \Storage::disk('public')->delete($user->aadhar_file);
            }
            $user->aadhar_file = $request->file('aadhar_file')->store('kyc_documents', 'public');
        }

        if ($request->hasFile('pan_file')) {
            if ($user->pan_file && \Storage::disk('public')->exists($user->pan_file)) {
                \Storage::disk('public')->delete($user->pan_file);
            }
            $user->pan_file = $request->file('pan_file')->store('kyc_documents', 'public');
        }

        if ($request->hasFile('bank_passbook_file')) {
            if ($user->bank_passbook_file && \Storage::disk('public')->exists($user->bank_passbook_file)) {
                \Storage::disk('public')->delete($user->bank_passbook_file);
            }
            $user->bank_passbook_file = $request->file('bank_passbook_file')->store('kyc_documents', 'public');
        }

        if ($request->hasFile('invoice_sample_file')) {
            if ($user->invoice_sample_file && \Storage::disk('public')->exists($user->invoice_sample_file)) {
                \Storage::disk('public')->delete($user->invoice_sample_file);
            }
            $user->invoice_sample_file = $request->file('invoice_sample_file')->store('kyc_documents', 'public');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->user_type = $request->user_type;
        $user->gender = $request->gender;
        $user->dob = $request->dob;
        $user->address = $request->address;
        $user->state_id = $request->state_id;
        $user->city_id = $request->city_id;
        $user->pincode_id = $request->pincode_id;

        $user->aadhar_number = $request->aadhar_number;
        $user->pan_number = $request->pan_number;
        $user->bank_account_number = $request->bank_account_number;
        $user->ifsc_code = $request->ifsc_code;

        $isSuperAdmin = auth()->guard('admin')->user()->hasRole('Super Admin');
        
        $user->is_invoice_verified = $request->has('is_invoice_verified');
        $user->is_photo_verified = $request->has('is_photo_verified');
        
        if ($isSuperAdmin) {
            $user->is_aadhar_verified = $request->has('is_aadhar_verified');
            $user->is_pan_verified = $request->has('is_pan_verified');
            $user->is_bank_verified = $request->has('is_bank_verified');
        } else {
            if (!$user->is_aadhar_verified) $user->is_aadhar_verified = $request->has('is_aadhar_verified');
            if (!$user->is_pan_verified) $user->is_pan_verified = $request->has('is_pan_verified');
            if (!$user->is_bank_verified) $user->is_bank_verified = $request->has('is_bank_verified');
        }

        if($request->wallet_balance !== null) {
            $user->wallet_balance = $request->wallet_balance;
        }
        $user->status = $request->status;
        $user->status_remark = $request->status_remark;
        
        if($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if (in_array($request->user_type, ['Shop Boy', 'Installer', 'Women Entrepreneurs'])) {
            $shopIds = $request->shop_ids ?? [];
            if (count($shopIds) < 1) {
                return redirect()->back()->with('error', 'Each User of this type must belong to a minimum of one shop.')->withInput();
            }
            if ($request->user_type == 'Shop Boy' && count($shopIds) > 1) {
                return redirect()->back()->with('error', 'Shop Boy can only belong to 1 shop.')->withInput();
            }
            if ($request->user_type == 'Installer' && count($shopIds) > 5) {
                return redirect()->back()->with('error', 'Installer can belong to a maximum of 5 shops.')->withInput();
            }
            if ($request->user_type == 'Women Entrepreneurs' && count($shopIds) > 3) {
                return redirect()->back()->with('error', 'Women Entrepreneurs can belong to a maximum of 3 shops.')->withInput();
            }
            $user->shops()->sync($shopIds);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'status' => 'required',
            'status_remark' => 'required|string|max:1000',
            'is_photo_verified' => 'required_if:status,Approved,Active|accepted'
        ];
        $messages = [
            'is_photo_verified.required_if' => 'You must verify the profile photo to approve the user.',
            'is_photo_verified.accepted' => 'You must verify the profile photo to approve the user.',
            'is_invoice_verified.required_if' => 'You must verify the invoice sample to approve the user.',
            'is_invoice_verified.accepted' => 'You must verify the invoice sample to approve the user.'
        ];

        if ($user->user_type == 'Shop Boy') {
            $rules['is_invoice_verified'] = 'required_if:status,Approved,Active|accepted';
        }

        $request->validate($rules, $messages);

        $user->status = $request->status;
        $user->status_remark = $request->status_remark;
        
        $isSuperAdmin = auth()->guard('admin')->user()->hasRole('Super Admin');
        
        $user->is_invoice_verified = $request->has('is_invoice_verified');
        $user->is_photo_verified = $request->has('is_photo_verified');
        
        if ($isSuperAdmin) {
            $user->is_aadhar_verified = $request->has('is_aadhar_verified');
            $user->is_pan_verified = $request->has('is_pan_verified');
            $user->is_bank_verified = $request->has('is_bank_verified');
        } else {
            if (!$user->is_aadhar_verified) $user->is_aadhar_verified = $request->has('is_aadhar_verified');
            if (!$user->is_pan_verified) $user->is_pan_verified = $request->has('is_pan_verified');
            if (!$user->is_bank_verified) $user->is_bank_verified = $request->has('is_bank_verified');
        }

        $user->save();
        return redirect()->back()->with('success', 'User status and verifications updated successfully.');
    }

    public function updateKyc(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $isSuperAdmin = auth()->guard('admin')->user()->hasRole('Super Admin');
        
        $user->is_invoice_verified = $request->has('is_invoice_verified');
        $user->is_photo_verified = $request->has('is_photo_verified');
        
        if ($isSuperAdmin) {
            $user->is_aadhar_verified = $request->has('is_aadhar_verified');
            $user->is_pan_verified = $request->has('is_pan_verified');
            $user->is_bank_verified = $request->has('is_bank_verified');
        } else {
            if (!$user->is_aadhar_verified) $user->is_aadhar_verified = $request->has('is_aadhar_verified');
            if (!$user->is_pan_verified) $user->is_pan_verified = $request->has('is_pan_verified');
            if (!$user->is_bank_verified) $user->is_bank_verified = $request->has('is_bank_verified');
        }
        
        $user->save();
        
        return redirect()->back()->with('success', 'KYC verification status updated successfully.');
    }

    public function destroy($id)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'Only Super Admin can delete users.');
        }

        $user = User::findOrFail($id);
        
        // Delete related data
        \App\Models\Invoice::where('user_id', $user->id)->delete();
        \App\Models\Installation::where('user_id', $user->id)->delete();
        \App\Models\Lead::where('user_id', $user->id)->delete();
        
        // Detach from shops
        $user->shops()->detach();
        
        // Delete files
        $filesToDelete = [
            $user->profile_photo,
            $user->aadhar_file,
            $user->pan_file,
            $user->bank_passbook_file,
            $user->invoice_sample_file
        ];
        
        foreach($filesToDelete as $file) {
            if ($file && \Storage::disk('public')->exists($file)) {
                \Storage::disk('public')->delete($file);
            }
        }

        $user->delete();
        
        return redirect()->route('admin.users.index')->with('success', 'User and all related data deleted successfully.');
    }
}
