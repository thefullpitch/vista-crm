<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:admin_users.view|admin_users.create|admin_users.edit|admin_users.delete', only: ['index', 'show']),
            new Middleware('permission:admin_users.create', only: ['create', 'store']),
            new Middleware('permission:admin_users.edit', only: ['edit', 'update']),
            new Middleware('permission:admin_users.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Admin::with('roles')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('roles', function($row){
                    $badges = '';
                    foreach($row->roles as $role) {
                        $badgeClass = $role->name == 'Super Admin' ? 'bg-danger' : 'bg-primary';
                        $badges .= '<span class="badge '.$badgeClass.' me-1">'.$role->name.'</span>';
                    }
                    return $badges;
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group">';
                    if(auth()->guard('admin')->user()->can('admin_users.edit')) {
                        $btn .= '<a href="'.route('admin.admin-users.edit', $row->id).'" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>';
                    }
                    if(auth()->guard('admin')->user()->can('admin_users.delete') && $row->id != 1) {
                        $btn .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteRecord('.$row->id.')"><i class="bi bi-trash"></i></button>';
                        $btn .= '<form id="delete-form-'.$row->id.'" action="'.route('admin.admin-users.destroy', $row->id).'" method="POST" style="display: none;">'.csrf_field().method_field('DELETE').'</form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'roles'])
                ->make(true);
        }
        
        return view('admin.admin_users.index');
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->pluck('name', 'name')->all();
        if(auth()->guard('admin')->user()->id != 1) {
            unset($roles['Super Admin']);
        }
        $zones = \App\Models\Zone::where('status', 1)->get();
        $userTypes = ['Shop Boy', 'Installer', 'Women Entrepreneurs'];
        return view('admin.admin_users.create', compact('roles', 'zones', 'userTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admin_users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
            'zone_id' => 'nullable|exists:zones,id',
            'state_ids' => 'nullable|array',
            'state_ids.*' => 'exists:states,id',
            'user_types' => 'nullable|array',
        ]);
    
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
    
        $user = Admin::create($input);
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('admin.admin-users.index')->with('success', 'Admin created successfully');
    }

    public function edit($id)
    {
        $user = Admin::findOrFail($id);
        $roles = Role::where('guard_name', 'admin')->pluck('name', 'name')->all();
        if(auth()->guard('admin')->user()->id != 1 && $id == 1) {
            abort(403);
        }
        $userRole = $user->roles->pluck('name','name')->all();
        $zones = \App\Models\Zone::where('status', 1)->get();
        
        $states = collect();
        if ($user->zone_id) {
            $states = \App\Models\State::where('zone_id', $user->zone_id)->where('status', 1)->get();
        }
        $userTypes = ['Shop Boy', 'Installer', 'Women Entrepreneurs'];
    
        return view('admin.admin_users.edit', compact('user', 'roles', 'userRole', 'zones', 'states', 'userTypes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('admin_users')->ignore($id)],
            'password' => 'nullable|same:confirm-password',
            'roles' => 'required',
            'zone_id' => 'nullable|exists:zones,id',
            'state_ids' => 'nullable|array',
            'state_ids.*' => 'exists:states,id',
            'user_types' => 'nullable|array',
        ]);
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = collect($input)->except('password')->toArray();    
        }
        
        if (!isset($input['state_ids'])) {
            $input['state_ids'] = null;
        }
        if (!isset($input['user_types'])) {
            $input['user_types'] = null;
        }
    
        $user = Admin::findOrFail($id);
        if(auth()->guard('admin')->user()->id != 1 && $id == 1) {
            abort(403);
        }
        
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->where('model_type', Admin::class)->delete();
    
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('admin.admin-users.index')->with('success', 'Admin updated successfully');
    }

    public function destroy($id)
    {
        if($id == 1) {
            return response()->json(['error' => 'Cannot delete primary Super Admin']);
        }
        Admin::findOrFail($id)->delete();
        return response()->json(['success' => 'Admin deleted successfully']);
    }
}
