<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:roles.view|roles.create|roles.edit|roles.delete', only: ['index', 'show']),
            new Middleware('permission:roles.create', only: ['create', 'store']),
            new Middleware('permission:roles.edit', only: ['edit', 'update']),
            new Middleware('permission:roles.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::with('permissions')->where('name', '!=', 'Super Admin')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('permissions', function($row){
                    $badges = '';
                    foreach($row->permissions->take(5) as $perm) {
                        $badges .= '<span class="badge bg-secondary me-1">'.$perm->name.'</span>';
                    }
                    if($row->permissions->count() > 5) {
                        $badges .= '<span class="badge bg-info">+'.($row->permissions->count() - 5).' more</span>';
                    }
                    return $badges;
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group">';
                    if(auth()->guard('admin')->user()->can('roles.edit')) {
                        $btn .= '<a href="'.route('admin.roles.edit', $row->id).'" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>';
                    }
                    if(auth()->guard('admin')->user()->can('roles.delete')) {
                        $btn .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteRecord('.$row->id.')"><i class="bi bi-trash"></i></button>';
                        $btn .= '<form id="delete-form-'.$row->id.'" action="'.route('admin.roles.destroy', $row->id).'" method="POST" style="display: none;">'.csrf_field().method_field('DELETE').'</form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'permissions'])
                ->make(true);
        }
        
        return view('admin.roles.index');
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', 'admin')->get();
        $groupedPermissions = $permissions->groupBy(function ($perm) {
            return ucfirst(explode('.', $perm->name)[0]);
        });
        return view('admin.roles.create', compact('permissions', 'groupedPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
    
        $role = Role::create(['name' => $request->name, 'guard_name' => 'admin']);
        $role->syncPermissions($request->permission);
    
        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        if($role->name == 'Super Admin') abort(403);
        $permissions = Permission::where('guard_name', 'admin')->get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        
        $groupedPermissions = $permissions->groupBy(function ($perm) {
            return ucfirst(explode('.', $perm->name)[0]);
        });
    
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions', 'groupedPermissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$id,
            'permission' => 'required',
        ]);
    
        $role = Role::findOrFail($id);
        if($role->name == 'Super Admin') abort(403);
        
        $role->name = $request->name;
        $role->save();
    
        $role->syncPermissions($request->permission);
    
        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if($role->name == 'Super Admin') return response()->json(['error' => 'Cannot delete Super Admin role']);
        
        $role->delete();
        return response()->json(['success' => 'Role deleted successfully']);
    }
}
