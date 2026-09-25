@extends('admin.layouts.app')

@section('title', 'Manage Subcategories')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Subcategories</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-box-seam text-primary me-2"></i>Subcategories</h2>
    </div>
    @can('subcategories.create')
    <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Subcategory</a>
    @endcan
</div>

<style>
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    .table th { background-color: #f8f9fa !important; color: #495057; font-weight: 600; }
</style>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="50px">No</th>
                        <th>Category</th>
                        <th>Subcategory Name</th>
                        <th>Status</th>
                        @canany(['subcategories.edit', 'subcategories.delete'])
                        <th width="120px">Action</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($subcategories as $subcategory)
                    <tr>
                        <td>{{ $loop->iteration + $subcategories->firstItem() - 1 }}</td>
                        <td>{{ $subcategory->category->name ?? 'N/A' }}</td>
                        <td>{{ $subcategory->name }}</td>
                        <td>
                            @if($subcategory->status == 'Active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        @canany(['subcategories.edit', 'subcategories.delete'])
                        <td>
                            <div class="d-flex gap-2">
                                @can('subcategories.edit')
                                <a href="{{ route('admin.subcategories.edit', $subcategory->id) }}" class="btn btn-sm btn-info text-white"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('subcategories.delete')
                                <form action="{{ route('admin.subcategories.destroy', $subcategory->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this subcategory?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                        @endcanany
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No subcategories found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="mt-3">
                {{ $subcategories->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
