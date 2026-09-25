@extends('admin.layouts.app')
@section('title', 'System Settings')
@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">System Settings</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-gear text-primary me-2"></i>System Configuration</h2>
    </div>
</div>

<style>
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
</style>

<div class="row">
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white pt-3 pb-0 border-bottom-0">
                <ul class="nav nav-tabs border-bottom-0" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab"><i class="bi bi-sliders me-1"></i> General Settings</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="points-tab" data-bs-toggle="tab" data-bs-target="#points" type="button" role="tab"><i class="bi bi-star me-1"></i> Points & Rewards</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab"><i class="bi bi-share me-1"></i> Contact & Social</button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body p-0">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <div class="tab-content border-top p-4" id="settingsTabContent">
                        
                        <!-- General Tab -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold">App Name</label>
                                <div class="col-md-9">
                                    <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name']->value ?? 'Vista CRM' }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold">Support Email</label>
                                <div class="col-md-9">
                                    <input type="email" name="support_email" class="form-control" value="{{ $settings['support_email']->value ?? 'support@example.com' }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold">Support Phone</label>
                                <div class="col-md-9">
                                    <input type="text" name="support_phone" class="form-control" value="{{ $settings['support_phone']->value ?? '1800-123-4567' }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold">App Version</label>
                                <div class="col-md-9">
                                    <input type="text" name="app_version" class="form-control" value="{{ $settings['app_version']->value ?? '1.0.0' }}">
                                </div>
                            </div>
                        </div>

                        <!-- Points Tab -->
                        <div class="tab-pane fade" id="points" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> These settings control how points are processed in the mobile app.
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold">Points Conversion (1 Point = ₹)</label>
                                <div class="col-md-9">
                                    <input type="number" step="0.01" name="points_conversion_rate" class="form-control" value="{{ $settings['points_conversion_rate']->value ?? '1' }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold">Min Points to Redeem</label>
                                <div class="col-md-9">
                                    <input type="number" name="points_min_redemption" class="form-control" value="{{ $settings['points_min_redemption']->value ?? '500' }}">
                                </div>
                            </div>
                            <hr>
                            <h6 class="fw-bold mb-3">Earning Logic</h6>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold">Amount Spent (₹)</label>
                                <div class="col-md-9">
                                    <input type="number" step="0.01" name="points_earn_amount" class="form-control" value="{{ $settings['points_earn_amount']->value ?? '100' }}">
                                    <small class="text-muted">e.g. For every ₹100 spent (Invoices & Installations)</small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold">Points Rewarded</label>
                                <div class="col-md-9">
                                    <input type="number" step="0.01" name="points_earn_reward" class="form-control" value="{{ $settings['points_earn_reward']->value ?? '1' }}">
                                    <small class="text-muted">e.g. User earns 1 Point</small>
                                </div>
                            </div>
                            <hr>
                            <h6 class="fw-bold mb-3">Lead Generation Logic</h6>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold">Points for Verified Lead</label>
                                <div class="col-md-9">
                                    <input type="number" step="0.01" name="points_earn_lead_progress" class="form-control" value="{{ $settings['points_earn_lead_progress']->value ?? '50' }}">
                                    <small class="text-muted">Awarded when lead status changes to 'In Progress'</small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold">Points for Converted Lead</label>
                                <div class="col-md-9">
                                    <input type="number" step="0.01" name="points_earn_lead_converted" class="form-control" value="{{ $settings['points_earn_lead_converted']->value ?? '200' }}">
                                    <small class="text-muted">Awarded when lead status changes to 'Converted' (Sale Made)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Social Tab -->
                        <div class="tab-pane fade" id="social" role="tabpanel">
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold"><i class="bi bi-facebook text-primary"></i> Facebook URL</label>
                                <div class="col-md-9">
                                    <input type="url" name="social_facebook" class="form-control" value="{{ $settings['social_facebook']->value ?? '' }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold"><i class="bi bi-twitter text-info"></i> Twitter URL</label>
                                <div class="col-md-9">
                                    <input type="url" name="social_twitter" class="form-control" value="{{ $settings['social_twitter']->value ?? '' }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label fw-bold"><i class="bi bi-instagram text-danger"></i> Instagram URL</label>
                                <div class="col-md-9">
                                    <input type="url" name="social_instagram" class="form-control" value="{{ $settings['social_instagram']->value ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light text-end py-3">
                        @can('settings.edit')
                        <button type="submit" class="btn btn-primary px-5"><i class="bi bi-save"></i> Save All Settings</button>
                        @else
                        <button type="button" class="btn btn-secondary px-5" disabled><i class="bi bi-lock"></i> No Permission to Save</button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

