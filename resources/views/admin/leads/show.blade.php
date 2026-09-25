@extends('admin.layouts.app')
@section('title', 'Lead Details')
@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lead Details</h5>
                <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Leads</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th width="200">Submitted By</th><td>{{ $lead->user->name ?? 'N/A' }}</td></tr>
                    <tr><th>Customer Name</th><td>{{ $lead->customer_name }}</td></tr>
                    <tr><th>Customer Mobile</th><td><a href="tel:{{ $lead->customer_mobile }}">{{ $lead->customer_mobile }}</a></td></tr>
                    <tr><th>Product Interest</th><td>{{ $lead->product_interest }}</td></tr>
                    <tr><th>User Remarks</th><td>{{ $lead->remarks ?? 'No remarks provided.' }}</td></tr>

                    <tr><th>Current Status</th>
                        <td>
                            @if($lead->status == 'In Progress')
                                <span class="badge bg-warning text-dark">In Progress</span>
                            @elseif($lead->status == 'Converted')
                                <span class="badge bg-success">Converted</span>
                            @elseif($lead->status == 'Closed')
                                <span class="badge bg-danger">Closed</span>
                            @else
                                <span class="badge bg-secondary">New</span>
                            @endif
                        </td>
                    </tr>
                    @if($lead->admin_remarks)
                        <tr><th>Admin Remarks</th><td>{{ $lead->admin_remarks }}</td></tr>
                    @endif
                    @if($lead->invoice_file)
                        <tr><th>Invoice</th><td><a href="{{ asset($lead->invoice_file) }}" target="_blank" class="btn btn-sm btn-info text-white"><i class="bi bi-file-earmark-text"></i> View Invoice</a></td></tr>
                    @endif
                </table>
                
                @can('leads.edit')
                <div class="mt-4 p-4 bg-light rounded border">
                    <h6 class="text-primary mb-3"><i class="bi bi-pencil-square"></i> Update Lead Status & Assignment</h6>
                    <form action="{{ route('admin.leads.status', $lead->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-info-circle"></i> Note: Changing the status to <strong>In Progress</strong> or <strong>Converted</strong> may automatically award points to the User based on your Global Settings, if they haven't been awarded already.
                        </div>
                        @php
                            $isSuperAdmin = auth()->guard('admin')->user()->hasRole('Super Admin');
                            $showInvoiceUpload = $isSuperAdmin || $lead->status !== 'Converted';
                        @endphp
                        <div class="row">
                            <div class="{{ $showInvoiceUpload ? 'col-md-4' : 'col-md-12' }} mb-3">
                                <label class="form-label fw-bold">Lead Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="New" {{ $lead->status == 'New' ? 'selected' : '' }}>New</option>
                                    <option value="In Progress" {{ $lead->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Converted" {{ $lead->status == 'Converted' ? 'selected' : '' }}>Converted (Sale Made)</option>
                                    <option value="Closed" {{ $lead->status == 'Closed' ? 'selected' : '' }}>Closed (Lost/No Interest)</option>
                                </select>
                            </div>
                            @if($showInvoiceUpload)
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Upload Invoice (Optional)</label>
                                <input type="file" name="invoice_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            @endif
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Remarks (Optional)</label>
                                <textarea name="admin_remarks" class="form-control" rows="3" placeholder="Enter remarks regarding this lead...">{{ $lead->admin_remarks }}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-2">Update Lead</button>
                    </form>
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection

