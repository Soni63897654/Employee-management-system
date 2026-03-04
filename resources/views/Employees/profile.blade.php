@extends('layouts.app')
@section('title', 'Employee Profile')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/employee-profile.css') }}">
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">

            <div class="profile-card">
                <div class="profile-header d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-1 fw-bold">{{ $employee->full_name }}</h3>
                        <span class="emp-code">Employee ID: {{ $employee->employee_code }}</span>
                    </div>
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                            <i class="fa fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>

                <div class="details-container">

                    <div class="section-header">
                        <span class="section-title">Employment Details</span>
                        <div class="section-line"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 info-box">
                            <div class="info-label"><i class="fa-solid fa-briefcase"></i> Department</div>
                            <div class="info-value">{{ $employee->department->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-3 info-box">
                            <div class="info-label"><i class="fa-solid fa-user-tie"></i> Reporting Manager</div>
                            <div class="info-value">{{ $employee->manager->full_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-3 info-box">
                            <div class="info-label"><i class="fa-solid fa-calendar"></i> Joining Date</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($employee->joining_date)->format('d M, Y') }}</div>
                        </div>
                        <div class="col-md-3 info-box">
                            <div class="info-label"><i class="fa-solid fa-envelope"></i> Email</div>
                            <div class="info-value">{{ $employee->email }}</div>
                        </div>
                    </div>

                    <div class="section-header mt-4">
                        <span class="section-title">Address Information</span>
                        <div class="section-line"></div>
                    </div>

                    <div class="row">
                        <div class="col-12 info-box">
                            <div class="info-label"><i class="fa-solid fa-location-dot"></i> Residential Address</div>
                            <div class="info-value fw-normal text-muted">
                                {{ $employee->address ?? 'No address found on record.' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer-info d-flex justify-content-between mt-3">
                    <span>Account created: {{ $employee->created_at->format('M Y') }}</span>
                    <span><i class="fa-solid fa-check-double text-primary me-1"></i> Official Record</span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('scripts')
@endsection