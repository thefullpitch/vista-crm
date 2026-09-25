@extends('admin.layouts.app')

@section('title', 'Manage Products')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Products</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-cart text-primary me-2"></i>Products</h2>
    </div>
    <div class="d-flex gap-2">
        @can('products.create')
        <a href="{{ route('admin.products.export') }}" class="btn btn-info text-white">
            <i class="bi bi-download"></i> Export CSV
        </a>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-spreadsheet"></i> Import CSV
        </button>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Product</a>
        @endcan
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="importModalLabel">Import Products</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label for="csv_file" class="form-label">Upload CSV File</label>
                <input class="form-control" type="file" id="csv_file" name="csv_file" accept=".csv" required>
            </div>
            <div class="mb-3">
                <p class="small text-muted mb-1">Make sure your CSV follows the correct format. You can download a sample below.</p>
                <a href="{{ route('admin.products.download-sample') }}" class="btn btn-sm btn-outline-info"><i class="bi bi-download"></i> Download Sample CSV</a>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Upload & Process</button>
          </div>
      </form>
    </div>
  </div>
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
                        <th>Subcategory</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Status</th>
                        @canany(['products.edit', 'products.delete'])
                        <th width="120px">Action</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>{{ $loop->iteration + $products->firstItem() - 1 }}</td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                        <td>{{ $product->subcategory->name ?? 'N/A' }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ number_format($product->price, 2) }}</td>
                        <td>
                            @if($product->status == 'Active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        @canany(['products.edit', 'products.delete'])
                        <td>
                            <div class="d-flex gap-2">
                                @can('products.edit')
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-info text-white"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('products.delete')
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
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
                        <td colspan="7" class="text-center">No products found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="mt-3">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
