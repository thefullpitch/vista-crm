<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\State;
use Yajra\DataTables\Facades\DataTables;

class CityController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = City::with('state');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('state_name', function($row){ return $row->state->name ?? '-'; })
                ->addColumn('action', function($row){
                    $editUrl = route('admin.cities.edit', $row->id);
                    $deleteUrl = route('admin.cities.destroy', $row->id);
                    $btn = '<a href="'.$editUrl.'" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>';
                    $btn .= ' <form action="'.$deleteUrl.'" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure?\')">
                                '.csrf_field().method_field("DELETE").'
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                              </form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.locations.cities.index');
    }

    public function create()
    {
        $states = State::all();
        return view('admin.locations.cities.create', compact('states'));
    }

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required|string|max:255'
        ]);
        City::create($request->all());
        return redirect()->route('admin.cities.index')->with('success', 'City created successfully.');
    }

    public function edit(City $city)
    {
        $states = State::all();
        return view('admin.locations.cities.edit', compact('city', 'states'));
    }

    public function update(Request $request, City $city)
    {
        $request->validate([

            'name' => 'required|string|max:255'
        ]);
        $city->update($request->all());
        return redirect()->route('admin.cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return redirect()->route('admin.cities.index')->with('success', 'City deleted successfully.');
    }
}
