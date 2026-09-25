<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pincode;
use App\Models\City;
use Yajra\DataTables\Facades\DataTables;

class PincodeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pincode::with('city.state');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('city_name', function($row){ return $row->city->name ?? '-'; })
                ->addColumn('state_name', function($row){ return $row->city->state->name ?? '-'; })
                ->addColumn('action', function($row){
                    $editUrl = route('admin.pincodes.edit', $row->id);
                    $deleteUrl = route('admin.pincodes.destroy', $row->id);
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
        return view('admin.locations.pincodes.index');
    }

    public function create()
    {
        $cities = City::with('state')->get();
        return view('admin.locations.pincodes.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'pincode' => 'required|string|max:10|unique:pincodes,pincode'
        ]);
        Pincode::create($request->all());
        return redirect()->route('admin.pincodes.index')->with('success', 'Pincode created successfully.');
    }

    public function edit(Pincode $pincode)
    {
        $cities = City::with('state')->get();
        return view('admin.locations.pincodes.edit', compact('pincode', 'cities'));
    }

    public function update(Request $request, Pincode $pincode)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'pincode' => 'required|string|max:10|unique:pincodes,pincode,'.$pincode->id
        ]);
        $pincode->update($request->all());
        return redirect()->route('admin.pincodes.index')->with('success', 'Pincode updated successfully.');
    }

    public function destroy(Pincode $pincode)
    {
        $pincode->delete();
        return redirect()->route('admin.pincodes.index')->with('success', 'Pincode deleted successfully.');
    }
}
