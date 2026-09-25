@extends('admin.layouts.app')
@section('title', 'Edit CMS Page')
@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.cms-pages.index') }}" class="text-decoration-none text-muted">Page: {{ $cmsPage->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Page: {{ $cmsPage->title }}</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Page: {{ $cmsPage->title }}</h2>
    </div>
    <a href="{{ route('admin.cms-pages.index') }}" class="btn btn-light border shadow-sm btn-sm px-3 py-2 fw-medium text-secondary hover-primary">
        <i class="bi bi-arrow-left me-1"></i> Back to Page: {{ $cmsPage->title }}
    </a>
</div>

<style>
    .hover-primary:hover { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
</style>

<div class="card shadow mb-4 border-0 rounded-3">
    <div class="card-body p-4">
        <form action="{{ route('admin.cms-pages.update', $cmsPage->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Page Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $cmsPage->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Custom Slug (Optional)</label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $cmsPage->slug) }}">
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="Published" {{ old('status', $cmsPage->status) == 'Published' ? 'selected' : '' }}>Published</option>
                        <option value="Draft" {{ old('status', $cmsPage->status) == 'Draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Page Content <span class="text-danger">*</span></label>
                    <textarea name="content" id="summernote" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $cmsPage->content) }}</textarea>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('admin.cms-pages.index') }}" class="btn btn-secondary me-2 px-4">Cancel</a>
                <hr>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Save Changes</button>
            </div></div>
        </form>
    </div>
</div>
@endsection
@push('styles')
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush
@push('scripts')
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Write the page content here...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
@endpush

