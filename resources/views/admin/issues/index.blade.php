@extends('admin.layouts.app')

@section('title', 'Issue Requests - Sheetu Food')
@section('page_title', 'Issue Requests')

@push('styles')
<style>
    .issues-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .issue-stat-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 18px 20px;
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform 0.2s ease;
    }

    .issue-stat-card:hover {
        transform: translateY(-2px);
    }

    .issue-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .icon-primary { background: #eef2ff; color: #4f46e5; }
    .icon-warning { background: #fffbeb; color: #f59e0b; }
    .icon-success { background: #ecfdf5; color: #10b981; }
    .icon-danger { background: #fef2f2; color: #ef4444; }

    .issue-stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
    }

    .issue-stat-val {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
    }

    /* Filter Card */
    .issues-filter-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
    }

    .issues-main-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
        overflow: hidden;
    }

    .issues-table {
        width: 100%;
        border-collapse: collapse;
    }

    .issues-table th {
        background: #f8fafc;
        padding: 14px 18px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .issues-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
        vertical-align: middle;
    }

    .issue-desc-box {
        max-width: 320px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #334155;
    }

    .status-badge-ticket {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-block;
    }

    .status-badge-ticket.pending { background: #fef3c7; color: #b45309; }
    .status-badge-ticket.approved { background: #d1fae5; color: #065f46; }
    .status-badge-ticket.rejected { background: #fee2e2; color: #991b1b; }

    .issues-mobile-cards {
        display: none;
        flex-direction: column;
        gap: 14px;
        padding: 16px;
    }

    .issue-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-lg);
        padding: 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }

    @media (max-width: 768px) {
        .issues-table-wrap { display: none; }
        .issues-mobile-cards { display: flex; }
    }
</style>
@endpush

@section('content')

<!-- Statistics Grid -->
<div class="issues-stats-grid">
    <div class="issue-stat-card">
        <div class="issue-stat-icon icon-primary"><i class="bi bi-ticket-detailed-fill"></i></div>
        <div>
            <div class="issue-stat-label">Total Issues</div>
            <div class="issue-stat-val">{{ number_format($stats['total_issues']) }}</div>
        </div>
    </div>
    <div class="issue-stat-card">
        <div class="issue-stat-icon icon-warning"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="issue-stat-label">Pending Review</div>
            <div class="issue-stat-val text-warning">{{ number_format($stats['pending_count']) }}</div>
        </div>
    </div>
    <div class="issue-stat-card">
        <div class="issue-stat-icon icon-success"><i class="bi bi-check-circle-fill"></i></div>
        <div>
            <div class="issue-stat-label">Approved</div>
            <div class="issue-stat-val text-success">{{ number_format($stats['approved_count']) }}</div>
        </div>
    </div>
    <div class="issue-stat-card">
        <div class="issue-stat-icon icon-danger"><i class="bi bi-x-circle-fill"></i></div>
        <div>
            <div class="issue-stat-label">Rejected</div>
            <div class="issue-stat-val text-danger">{{ number_format($stats['rejected_count']) }}</div>
        </div>
    </div>
</div>

<!-- Filter Box -->
<div class="issues-filter-card">
    <form method="GET" action="{{ route('admin.issues.index') }}">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <label class="form-label fw-bold small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label fw-bold small text-muted">Issue Type</label>
                <input type="text" name="type" class="form-control" placeholder="Search type..." value="{{ $typeFilter }}">
            </div>
            <div class="col-md-3 col-12">
                <label class="form-label fw-bold small text-muted">User Mobile</label>
                <input type="text" name="mobile" class="form-control" placeholder="Search mobile..." value="{{ $mobileFilter }}">
            </div>
            <div class="col-md-3 col-12 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 fw-bold">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="{{ route('admin.issues.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Main Records Card -->
<div class="issues-main-card">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
        <h5 class="fw-bold m-0" style="font-size: 1.05rem;">
            <i class="bi bi-exclamation-triangle-fill text-primary me-2"></i>Issue Requests
        </h5>
        <span class="badge bg-primary">{{ count($records) }} Tickets</span>
    </div>

    <!-- Desktop View Table -->
    <div class="issues-table-wrap table-responsive">
        <table class="issues-table">
            <thead>
                <tr>
                    <th class="ps-4">#ID</th>
                    <th>User & Mobile</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date & Time</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                    @php
                        $st = strtolower($r->status);
                    @endphp
                    <tr>
                        <td class="ps-4 text-muted"><strong>#{{ $r->id }}</strong></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $r->user_name ?: 'Unknown User' }}</div>
                            <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $r->mobile }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $r->type }}</span>
                        </td>
                        <td>
                            <div class="issue-desc-box" title="{{ $r->description }}">
                                {{ $r->description }}
                            </div>
                        </td>
                        <td>
                            <span class="status-badge-ticket {{ $st }}">{{ ucfirst($r->status) }}</span>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($r->created_at)->format('d M, Y h:i A') }}
                            </small>
                        </td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('admin.issues.update-status') }}" class="d-inline-flex gap-1 align-items-center">
                                @csrf
                                <input type="hidden" name="issue_id" value="{{ $r->id }}">
                                <select name="status" class="form-select form-select-sm" style="width: auto;">
                                    <option value="pending" {{ $r->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $r->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $r->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary fw-bold">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            <p class="m-0">No issue requests found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile View Cards -->
    <div class="issues-mobile-cards">
        @forelse($records as $r)
            @php
                $st = strtolower($r->status);
            @endphp
            <div class="issue-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <strong class="text-muted">#{{ $r->id }}</strong>
                        <span class="status-badge-ticket {{ $st }} ms-2">{{ ucfirst($r->status) }}</span>
                    </div>
                    <small class="text-muted">
                        {{ \Carbon\Carbon::parse($r->created_at)->format('d M, Y h:i A') }}
                    </small>
                </div>

                <div class="mb-2">
                    <div class="fw-bold text-dark"><i class="bi bi-person me-1 text-primary"></i>{{ $r->user_name ?: 'Unknown User' }}</div>
                    <small class="text-muted"><i class="bi bi-phone me-1"></i>{{ $r->mobile }}</small>
                </div>

                <div class="mb-3">
                    <div class="small fw-bold text-muted mb-1">Type: <span class="badge bg-light text-dark border">{{ $r->type }}</span></div>
                    <div class="p-2 bg-light rounded-3 small border" style="line-height: 1.5;">
                        {{ $r->description }}
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.issues.update-status') }}">
                    @csrf
                    <input type="hidden" name="issue_id" value="{{ $r->id }}">
                    <div class="d-flex gap-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="pending" {{ $r->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $r->status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $r->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">Update</button>
                    </div>
                </form>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                <p>No issue requests found.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
