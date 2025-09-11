@extends('layouts.mantis')

@section('title', 'Site Settings')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Site Settings</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item">Site Settings</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Site Configuration</h5>
            </div>
            <div class="card-body">
                <form class="ajax-form" action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Site Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="site_name" name="site_name" 
                                       value="{{ $settings['site_name'] ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_phone" class="form-label">Contact Phone</label>
                                <input type="text" class="form-control" id="contact_phone" name="contact_phone" 
                                       value="{{ $settings['contact_phone'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                       value="{{ $settings['contact_email'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_address" class="form-label">Contact Address</label>
                                <input type="text" class="form-control" id="contact_address" name="contact_address" 
                                       value="{{ $settings['contact_address'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="timezone" class="form-label">Timezone</label>
                                <select class="form-select" id="timezone" name="timezone">
                                    <option value="UTC" {{ ($settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ ($settings['timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                    <option value="America/Chicago" {{ ($settings['timezone'] ?? '') == 'America/Chicago' ? 'selected' : '' }}>America/Chicago</option>
                                    <option value="America/Denver" {{ ($settings['timezone'] ?? '') == 'America/Denver' ? 'selected' : '' }}>America/Denver</option>
                                    <option value="America/Los_Angeles" {{ ($settings['timezone'] ?? '') == 'America/Los_Angeles' ? 'selected' : '' }}>America/Los_Angeles</option>
                                    <option value="Europe/London" {{ ($settings['timezone'] ?? '') == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                    <option value="Europe/Paris" {{ ($settings['timezone'] ?? '') == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                                    <option value="Asia/Tokyo" {{ ($settings['timezone'] ?? '') == 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo</option>
                                    <option value="Asia/Shanghai" {{ ($settings['timezone'] ?? '') == 'Asia/Shanghai' ? 'selected' : '' }}>Asia/Shanghai</option>
                                    <option value="Asia/Kolkata" {{ ($settings['timezone'] ?? '') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_format" class="form-label">Date Format</label>
                                <select class="form-select" id="date_format" name="date_format">
                                    <option value="Y-m-d" {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    <option value="d-m-Y" {{ ($settings['date_format'] ?? '') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                                    <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                    <option value="d/m/Y" {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency</label>
                                <select class="form-select" id="currency" name="currency">
                                    <option value="USD" {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="GBP" {{ ($settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                    <option value="INR" {{ ($settings['currency'] ?? '') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                                    <option value="JPY" {{ ($settings['currency'] ?? '') == 'JPY' ? 'selected' : '' }}>JPY (¥)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="items_per_page" class="form-label">Items Per Page</label>
                                <select class="form-select" id="items_per_page" name="items_per_page">
                                    <option value="10" {{ ($settings['items_per_page'] ?? '') == '10' ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ ($settings['items_per_page'] ?? '') == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ ($settings['items_per_page'] ?? '') == '50' ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($settings['items_per_page'] ?? '') == '100' ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="site_description" class="form-label">Site Description</label>
                                <textarea class="form-control" id="site_description" name="site_description" rows="3">{{ $settings['site_description'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                       {{ ($settings['maintenance_mode'] ?? '') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="maintenance_mode">
                                    Maintenance Mode
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy"></i> Save Settings
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="location.reload()">
                                <i class="ti ti-refresh"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection
