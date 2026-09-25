<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zone;
use App\Models\State;
use Yajra\DataTables\Facades\DataTables;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Zone::withCount('states')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function($row){
                    if($row->status == 1){
                        return '<span class="badge bg-success">Active</span>';
                    }else{
                        return '<span class="badge bg-danger">Inactive</span>';
                    }
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="btn-group">';
                    $btn .= '<a href="'.route('admin.zones.edit', $row->id).'" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>';
                    $btn .= '<form action="'.route('admin.zones.destroy', $row->id).'" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this zone?\');">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.locations.zones.index');
    }

    public function create()
    {
        return view('admin.locations.zones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:zones,name',
            'status' => 'required|boolean'
        ]);

        Zone::create($request->all());

        return redirect()->route('admin.zones.index')->with('success', 'Zone created successfully.');
    }

    public function edit($id)
    {
        $zone = Zone::findOrFail($id);
        $states = State::orderBy('name')->get();
        return view('admin.locations.zones.edit', compact('zone', 'states'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:zones,name,'.$id,
            'status' => 'required|boolean',
            'states' => 'nullable|array'
        ]);

        $zone = Zone::findOrFail($id);
        $zone->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        // Unassign all states from this zone first
        State::where('zone_id', $zone->id)->update(['zone_id' => null]);
        
        // Re-assign selected states
        if($request->has('states')) {
            State::whereIn('id', $request->states)->update(['zone_id' => $zone->id]);
        }

        return redirect()->route('admin.zones.index')->with('success', 'Zone updated successfully.');
    }

    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        State::where('zone_id', $zone->id)->update(['zone_id' => null]);
        $zone->delete();

        return redirect()->route('admin.zones.index')->with('success', 'Zone deleted successfully.');
    }
}
