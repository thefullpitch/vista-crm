<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsPage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CmsPageController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:cms.view|cms.edit', only: ['index']),
            new Middleware('permission:cms.edit', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = CmsPage::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function($row){
                    $class = $row->status == 'Published' ? 'success' : 'secondary';
                    return '<span class="badge bg-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function($row){
                    $editUrl = route('admin.cms-pages.edit', $row->id);
                    $deleteUrl = route('admin.cms-pages.destroy', $row->id);
                    $btn = '<a href="'.$editUrl.'" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>';
                    $btn .= ' <form action="'.$deleteUrl.'" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this page?\')">
                                '.csrf_field().method_field("DELETE").'
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                              </form>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('admin.cms.index');
    }

    public function create()
    {
        return view('admin.cms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug',
            'content' => 'required|string',
            'status' => 'required|in:Draft,Published'
        ]);

        $data = $request->all();
        $data['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        CmsPage::create($data);
        return redirect()->route('admin.cms-pages.index')->with('success', 'CMS Page created successfully.');
    }

    public function edit(CmsPage $cmsPage)
    {
        return view('admin.cms.edit', compact('cmsPage'));
    }

    public function update(Request $request, CmsPage $cmsPage)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug,'.$cmsPage->id,
            'content' => 'required|string',
            'status' => 'required|in:Draft,Published'
        ]);

        $data = $request->all();
        $data['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        $cmsPage->update($data);
        return redirect()->route('admin.cms-pages.index')->with('success', 'CMS Page updated successfully.');
    }

    public function destroy(CmsPage $cmsPage)
    {
        $cmsPage->delete();
        return redirect()->route('admin.cms-pages.index')->with('success', 'CMS Page deleted successfully.');
    }
}
