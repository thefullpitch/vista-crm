<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;
use Yajra\DataTables\Facades\DataTables;

class StateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = State::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $editUrl = route('admin.states.edit', $row->id);
                    $deleteUrl = route('admin.states.destroy', $row->id);
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
        return view('admin.locations.states.index');
    }

    public function create()
    {
        return view('admin.locations.states.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:states,name']);
        State::create($request->all());
        return redirect()->route('admin.states.index')->with('success', 'State created successfully.');
    }

    public function edit(State $state)
    {
        return view('admin.locations.states.edit', compact('state'));
    }

    public function update(Request $request, State $state)
    {
        $request->validate(['name' => 'required|string|max:255|unique:states,name,'.$state->id]);
        $state->update($request->all());
        return redirect()->route('admin.states.index')->with('success', 'State updated successfully.');
    }

    public function destroy(State $state)
    {
        $state->delete();
        return redirect()->route('admin.states.index')->with('success', 'State deleted successfully.');
    }
}
