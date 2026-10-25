@extends('admin.layouts.app')

@section('title', 'User Management - Sheetu Food')
@section('page_title', 'User Management')

@push('styles')
<style>
    .stats-scroll-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-pill-card {
        background: #ffffff;
        padding: 16px 20px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.2s ease;
    }

    .stat-pill-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-card);
    }

    .stat-pill-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-pill-card.total .stat-pill-icon { background: #eef2ff; color: #4f46e5; }
    .stat-pill-card.active .stat-pill-icon { background: #ecfdf5; color: #10b981; }
    .stat-pill-card.inactive .stat-pill-icon { background: #fef2f2; color: #ef4444; }
    .stat-pill-card.blocked .stat-pill-icon { background: #f1f5f9; color: #64748b; }

    .stat-pill-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--text-muted);
        letter-spacing: 0.5px;
    }

    .stat-pill-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
    }

    /* Filter Panel */
    .filter-panel {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 18px 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
    }

    .filter-form-grid {
        display: grid;
        grid-template-columns: 2fr 1fr auto auto;
        gap: 12px;
        align-items: center;
    }

    .app-input {
        width: 100%;
        padding: 10px 16px;
        border: 1.5px solid var(--border-subtle);
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-main);
        background: #f8fafc;
        transition: all 0.2s ease;
    }

    .app-input:focus {
        outline: none;
        border-color: var(--primary);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .btn-action-primary {
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        background: var(--gradient-primary);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    .btn-action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        color: #ffffff;
    }

    .btn-action-reset {
        padding: 10px 14px;
        border-radius: 10px;
        font-weight: 600;
        border: 1.5px solid var(--border-subtle);
        background: #f8fafc;
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-action-reset:hover {
        background: #e2e8f0;
        color: var(--text-main);
    }

    /* Form Container */
    .form-panel {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-card);
        border: 1px solid var(--border-subtle);
    }

    .form-panel-title {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 18px;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Desktop Table */
    .table-container-desktop {
        display: block;
        background: #ffffff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
        overflow: hidden;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table th {
        background: #f8fafc;
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .users-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.88rem;
    }

    .users-table tbody tr:hover {
        background: #fafafa;
    }

    .status-pill {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-block;
    }

    .status-pill.active { background: #d1fae5; color: #047857; }
    .status-pill.inactive { background: #fee2e2; color: #b91c1c; }
    .status-pill.blocked { background: #e2e8f0; color: #334155; }
    .status-pill.payment { background: #fef3c7; color: #b45309; }

    /* Mobile Cards View */
    .mobile-cards-wrap {
        display: none;
        flex-direction: column;
        gap: 14px;
    }

    .user-app-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 16px;
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
    }

    .user-card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .user-card-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--gradient-primary);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.15rem;
    }

    .user-card-body {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding: 10px 0;
        border-top: 1px dashed var(--border-subtle);
        border-bottom: 1px dashed var(--border-subtle);
        margin-bottom: 12px;
        font-size: 0.84rem;
    }

    .user-card-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(65px, 1fr));
        gap: 6px;
    }

    .btn-grid-action {
        padding: 8px 4px;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 700;
        border: none;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        cursor: pointer;
        transition: transform 0.15s ease;
    }

    .btn-grid-action:hover {
        transform: scale(1.03);
        color: #ffffff;
    }

    .btn-grid-action.edit { background: #3b82f6; }
    .btn-grid-action.pkg { background: #8b5cf6; }
    .btn-grid-action.pay { background: #10b981; }
    .btn-grid-action.status { background: #f59e0b; }
    .btn-grid-action.del { background: #ef4444; }

    /* Floating Action Button (FAB) */
    .mobile-fab-btn {
        display: none;
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--gradient-primary);
        color: #ffffff;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
        z-index: 999;
    }

    /* Modal Styling */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
    }

    .custom-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .custom-modal-content {
        background: #ffffff;
        padding: 24px;
        border-radius: var(--radius-xl);
        max-width: 580px;
        width: 92%;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        max-height: 90vh;
        overflow-y: auto;
    }

    @media (max-width: 991px) {
        .table-container-desktop { display: none; }
        .mobile-cards-wrap { display: flex; }
        .mobile-fab-btn { display: flex; }
        .filter-form-grid { grid-template-columns: 1fr; }
        .stats-scroll-grid { grid-template-columns: repeat(2, 1fr); }

        .custom-modal.active {
            align-items: flex-end;
        }

        .custom-modal-content {
            width: 100%;
            max-width: 100%;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
            animation: slideSheetUp 0.25s ease-out;
        }

        @keyframes slideSheetUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
    }
</style>
@endpush

@section('content')

<!-- Mobile FAB -->
<a href="{{ route('admin.users.index', ['action' => 'add']) }}" class="mobile-fab-btn" title="Add User">
    <i class="bi bi-plus-lg"></i>
</a>

<!-- KPI Stat Pills -->
<div class="stats-scroll-grid">
    <div class="stat-pill-card total">
        <div class="stat-pill-icon"><i class="bi bi-people-fill"></i></div>
        <div>
            <div class="stat-pill-label">Total Users</div>
            <div class="stat-pill-value">{{ number_format($totalUsers) }}</div>
        </div>
    </div>
    <div class="stat-pill-card active">
        <div class="stat-pill-icon"><i class="bi bi-person-check-fill"></i></div>
        <div>
            <div class="stat-pill-label">Active Users</div>
            <div class="stat-pill-value">{{ number_format($activeUsers) }}</div>
        </div>
    </div>
    <div class="stat-pill-card inactive">
        <div class="stat-pill-icon"><i class="bi bi-person-dash-fill"></i></div>
        <div>
            <div class="stat-pill-label">Inactive</div>
            <div class="stat-pill-value">{{ number_format($inactiveUsers) }}</div>
        </div>
    </div>
    <div class="stat-pill-card blocked">
        <div class="stat-pill-icon"><i class="bi bi-person-x-fill"></i></div>
        <div>
            <div class="stat-pill-label">Blocked</div>
            <div class="stat-pill-value">{{ number_format($blockedUsers) }}</div>
        </div>
    </div>
</div>

<!-- Search & Filter Controls -->
<div class="filter-panel">
    <form method="GET" action="{{ route('admin.users.index') }}">
        <div class="filter-form-grid">
            <input type="text" name="search" class="app-input" placeholder="🔍 Search name, mobile or email..." value="{{ $search }}">
            
            <select name="status_filter" class="app-input">
                <option value="">All Statuses</option>
                <option value="Active" {{ $statusFilter === 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Deactive" {{ $statusFilter === 'Deactive' ? 'selected' : '' }}>Inactive (Deactive)</option>
                <option value="Block" {{ $statusFilter === 'Block' ? 'selected' : '' }}>Blocked</option>
            </select>

            <button type="submit" class="btn-action-primary">
                <i class="bi bi-search"></i>
                <span>Search</span>
            </button>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn-action-reset" title="Reset Filters">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
                <a href="{{ route('admin.users.index', ['action' => 'add']) }}" class="btn-action-primary d-none d-lg-inline-flex">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Add User</span>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Add/Edit User Form Section -->
@if($action === 'add' || $userToEdit)
<div class="form-panel">
    <div class="form-panel-title">
        <i class="bi bi-{{ $userToEdit ? 'pencil-square text-info' : 'person-plus-fill text-primary' }}"></i>
        <span>{{ $userToEdit ? 'Edit User Details' : 'Add New User' }}</span>
    </div>

    <form method="POST" action="{{ route('admin.users.action') }}">
        @csrf
        <input type="hidden" name="action" value="{{ $userToEdit ? 'edit' : 'add' }}">
        @if($userToEdit)
            <input type="hidden" name="id" value="{{ $userToEdit->id }}">
        @endif

        <div class="row g-3">
            <div class="col-md-6 col-12">
                <label class="form-label fw-bold small text-muted">Full Name *</label>
                <input name="name" class="app-input" placeholder="Enter full name" required value="{{ old('name', $userToEdit->name ?? '') }}">
            </div>
            <div class="col-md-6 col-12">
                <label class="form-label fw-bold small text-muted">Mobile Number</label>
                <input name="mobile" class="app-input" placeholder="Enter mobile number" value="{{ old('mobile', $userToEdit->mobile ?? '') }}">
            </div>
            <div class="col-md-6 col-12">
                <label class="form-label fw-bold small text-muted">Email Address</label>
                <input name="email" type="email" class="app-input" placeholder="Enter email address" value="{{ old('email', $userToEdit->email ?? '') }}">
            </div>
            <div class="col-md-6 col-12">
                <label class="form-label fw-bold small text-muted">Password</label>
                <input name="password" type="password" class="app-input" placeholder="{{ $userToEdit ? 'Leave blank to retain current password' : 'Default: 123456' }}">
            </div>
            <div class="col-md-6 col-12">
                <label class="form-label fw-bold small text-muted">Status</label>
                <select name="status" class="app-input" {{ ($userToEdit->id ?? null) == 1 ? 'disabled' : '' }}>
                    <option value="Active" {{ old('status', $userToEdit->status ?? 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Deactive" {{ old('status', $userToEdit->status ?? '') === 'Deactive' ? 'selected' : '' }}>Deactive</option>
                    <option value="Block" {{ old('status', $userToEdit->status ?? '') === 'Block' ? 'selected' : '' }}>Blocked</option>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn-action-primary">
                <i class="bi bi-check2-circle"></i>
                <span>{{ $userToEdit ? 'Update User' : 'Create User' }}</span>
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn-action-reset">Cancel</a>
        </div>
    </form>
</div>
@endif

<!-- 1. DESKTOP VIEW TABLE (>= 992px) -->
<div class="table-container-desktop">
    <table class="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact Details</th>
                <th>Package</th>
                <th>Status</th>
                <th>Created Date</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
                <tr>
                    <td><strong>#{{ $u->id }}</strong></td>
                    <td>
                        <div class="fw-bold text-dark">
                            {{ $u->name }}
                            @if($u->id == 1)
                                <span class="badge bg-primary ms-1" style="font-size: 0.65rem;">Admin</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="small">
                            <div><i class="bi bi-telephone text-muted me-1"></i>{{ $u->mobile ?: 'N/A' }}</div>
                            <div><i class="bi bi-envelope text-muted me-1"></i>{{ $u->email ?: 'N/A' }}</div>
                        </div>
                    </td>
                    <td>
                        @if(!empty($u->pkg))
                            <span class="badge bg-light text-dark border"><i class="bi bi-box-seam me-1"></i>Pkg: {{ $u->pkg }}</span>
                        @else
                            <span class="text-muted small">None</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $st = strtolower($u->status);
                            $badgeClass = $st === 'active' ? 'active' : ($st === 'block' ? 'blocked' : ($st === 'payment' ? 'payment' : 'inactive'));
                        @endphp
                        <span class="status-pill {{ $badgeClass }}">{{ $u->status }}</span>
                    </td>
                    <td class="small text-muted">
                        {{ $u->created_at ? \Carbon\Carbon::parse($u->created_at)->format('d M, Y') : 'N/A' }}
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.users.index', ['action' => 'edit', 'id' => $u->id]) }}" 
                               class="btn btn-sm btn-outline-secondary py-1 px-2" title="Edit User">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="openPackageModal({{ $u->id }}, '{{ addslashes($u->name) }}')" 
                                    class="btn btn-sm text-white py-1 px-2" style="background:#8b5cf6;" title="Assign Package">
                                <i class="bi bi-box-seam"></i>
                            </button>
                            <button onclick="openPaymentModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->token }}')" 
                                    class="btn btn-sm text-white py-1 px-2" style="background:#10b981;" title="Record Payment">
                                <i class="bi bi-cash-stack"></i>
                            </button>
                            @if($u->id != 1)
                                <form method="POST" action="{{ route('admin.users.action') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="id" value="{{ $u->id }}">
                                    <button type="submit" class="btn btn-sm text-white py-1 px-2" style="background:#f59e0b;" title="Toggle Status">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.action') }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="id" value="{{ $u->id }}">
                                    <button type="submit" class="btn btn-sm btn-danger py-1 px-2" title="Delete User">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">No users found matching your criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- 2. MOBILE APP CARDS VIEW (< 992px) -->
<div class="mobile-cards-wrap">
    @forelse($users as $u)
        @php
            $st = strtolower($u->status);
            $badgeClass = $st === 'active' ? 'active' : ($st === 'block' ? 'blocked' : ($st === 'payment' ? 'payment' : 'inactive'));
            $initial = strtoupper(substr($u->name, 0, 1));
        @endphp
        <div class="user-app-card">
            <div class="user-card-top">
                <div class="user-card-profile">
                    <div class="user-avatar-circle">{{ $initial }}</div>
                    <div>
                        <div class="fw-bold text-dark d-flex align-items-center gap-1">
                            {{ $u->name }}
                            @if($u->id == 1)
                                <span class="badge bg-primary" style="font-size: 0.62rem;">Admin</span>
                            @endif
                        </div>
                        <div class="text-muted small">User ID: #{{ $u->id }}</div>
                    </div>
                </div>
                <span class="status-pill {{ $badgeClass }}">{{ $u->status }}</span>
            </div>

            <div class="user-card-body">
                <div>
                    <i class="bi bi-telephone text-primary me-1"></i>
                    <strong>{{ $u->mobile ?: 'N/A' }}</strong>
                </div>
                <div>
                    <i class="bi bi-box-seam text-primary me-1"></i>
                    <span>Pkg: <strong>{{ $u->pkg ?: 'None' }}</strong></span>
                </div>
                <div style="grid-column: span 2;">
                    <i class="bi bi-envelope text-primary me-1"></i>
                    <span style="word-break: break-all;">{{ $u->email ?: 'N/A' }}</span>
                </div>
            </div>

            <!-- Action Grid -->
            <div class="user-card-actions">
                <a href="{{ route('admin.users.index', ['action' => 'edit', 'id' => $u->id]) }}" class="btn-grid-action edit">
                    <i class="bi bi-pencil-square"></i>
                    <span>Edit</span>
                </a>
                <button onclick="openPackageModal({{ $u->id }}, '{{ addslashes($u->name) }}')" class="btn-grid-action pkg">
                    <i class="bi bi-box-seam"></i>
                    <span>Package</span>
                </button>
                <button onclick="openPaymentModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->token }}')" class="btn-grid-action pay">
                    <i class="bi bi-wallet2"></i>
                    <span>Pay</span>
                </button>
                @if($u->id != 1)
                    <form method="POST" action="{{ route('admin.users.action') }}" class="m-0">
                        @csrf
                        <input type="hidden" name="action" value="toggle_status">
                        <input type="hidden" name="id" value="{{ $u->id }}">
                        <button type="submit" class="btn-grid-action status w-100">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>Status</span>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.action') }}" class="m-0" onsubmit="return confirm('Delete this user?');">
                        @csrf
                        <input type="hidden" name="action" value="delete_user">
                        <input type="hidden" name="id" value="{{ $u->id }}">
                        <button type="submit" class="btn-grid-action del w-100">
                            <i class="bi bi-trash"></i>
                            <span>Delete</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="user-app-card text-center text-muted py-4">No records found.</div>
    @endforelse
</div>

<!-- Package Assignment Modal -->
<div id="packageModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0 fw-bold"><i class="bi bi-box-seam text-primary me-2"></i>Assign Package</h5>
            <button class="btn-close" onclick="closePackageModal()"></button>
        </div>

        <form method="POST" action="{{ route('admin.users.action') }}" id="packageForm">
            @csrf
            <input type="hidden" name="action" value="assign_package">
            <input type="hidden" name="id" id="package_user_id">

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Selected User</label>
                <input type="text" class="app-input" id="package_user_name" readonly style="background:#eef2ff; font-weight:700;">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Available Package Meals</label>
                <div style="max-height: 250px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px;">
                    @forelse($packages as $pkg)
                        <div class="p-2 rounded-3 border bg-light">
                            <div class="fw-bold small mb-1 text-dark">Package #{{ $pkg->id }}</div>
                            <div class="d-flex gap-3 flex-wrap small">
                                <label class="d-flex align-items-center gap-1 cursor-pointer">
                                    <input type="checkbox" name="selected_items[]" value="{{ $pkg->id }}_brkf" data-price="{{ $pkg->brkf }}" onchange="updatePaymentSummary()">
                                    <span>Breakfast (₹{{ number_format($pkg->brkf, 2) }})</span>
                                </label>
                                <label class="d-flex align-items-center gap-1 cursor-pointer">
                                    <input type="checkbox" name="selected_items[]" value="{{ $pkg->id }}_luh" data-price="{{ $pkg->luh }}" onchange="updatePaymentSummary()">
                                    <span>Lunch (₹{{ number_format($pkg->luh, 2) }})</span>
                                </label>
                                <label class="d-flex align-items-center gap-1 cursor-pointer">
                                    <input type="checkbox" name="selected_items[]" value="{{ $pkg->id }}_dinr" data-price="{{ $pkg->dinr }}" onchange="updatePaymentSummary()">
                                    <span>Dinner (₹{{ number_format($pkg->dinr, 2) }})</span>
                                </label>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small">No packages defined in fd_price table.</div>
                    @endforelse
                </div>
            </div>

            <div id="paymentSummary" style="display:none; background:#ecfdf5; padding:12px; border-radius:10px; margin-bottom:1rem; font-size:0.88rem;">
                <div>Total Selected: <strong id="selectedCount">0</strong></div>
                <div>Total Amount: <strong id="totalAmount">₹0.00</strong></div>
                <div>Pending: <strong id="pendingAmount" style="color:#ef4444;">₹0.00</strong></div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Paid Amount (₹)</label>
                <input type="number" name="paid_amount" id="paidAmountInput" class="app-input" placeholder="0.00" step="0.01" min="0" value="0" oninput="updatePaymentSummary()">
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-action-primary flex-grow-1" id="submitPkgBtn" disabled>Assign Package</button>
                <button type="button" class="btn-action-reset" onclick="closePackageModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Payment History & Record Modal -->
<div id="paymentModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0 fw-bold"><i class="bi bi-wallet2 text-success me-2"></i>Payment History & Add</h5>
            <button class="btn-close" onclick="closePaymentModal()"></button>
        </div>

        <form method="POST" action="{{ route('admin.users.action') }}" id="paymentForm">
            @csrf
            <input type="hidden" name="action" value="add_payment">
            <input type="hidden" name="id" id="payment_user_id">

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">User</label>
                <input type="text" class="app-input" id="payment_user_name" readonly style="background:#eef2ff; font-weight:700;">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Select Due Month</label>
                <select name="payment_month" id="payment_month" class="app-input" required disabled></select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">History Log</label>
                <div id="paymentHistory" style="max-height: 180px; overflow-y: auto; border:1.5px solid var(--border-subtle); border-radius:10px; padding:10px; font-size:0.84rem; background:#f8fafc;"></div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Payment Amount (₹)</label>
                <input type="number" name="paid_amount" class="app-input" placeholder="Enter amount" step="0.01" min="0" required>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-action-primary flex-grow-1" id="submitPaymentBtn" disabled>Record Payment</button>
                <button type="button" class="btn-action-reset" onclick="closePaymentModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const userPayments = @json($userPayments);

    function openPackageModal(userId, userName) {
        document.getElementById('package_user_id').value = userId;
        document.getElementById('package_user_name').value = userName;
        document.getElementById('packageModal').classList.add('active');
        document.querySelectorAll('input[name="selected_items[]"]').forEach(cb => cb.checked = false);
        document.getElementById('paidAmountInput').value = '0';
        updatePaymentSummary();
    }

    function closePackageModal() {
        document.getElementById('packageModal').classList.remove('active');
        document.getElementById('packageForm').reset();
        updatePaymentSummary();
    }

    function updatePaymentSummary() {
        const checkboxes = document.querySelectorAll('input[name="selected_items[]"]:checked');
        const paidInput  = document.getElementById('paidAmountInput');
        const submitBtn  = document.getElementById('submitPkgBtn');

        let totalPrice = 0;
        let selectedCount = 0;

        checkboxes.forEach(cb => {
            totalPrice += parseFloat(cb.dataset.price) || 0;
            selectedCount++;
        });

        const paidAmount = parseFloat(paidInput.value) || 0;
        const pendingAmount = totalPrice - paidAmount;

        document.getElementById('selectedCount').textContent = selectedCount;
        document.getElementById('totalAmount').textContent = '₹' + totalPrice.toFixed(2);
        document.getElementById('pendingAmount').textContent = '₹' + pendingAmount.toFixed(2);

        const summaryDiv = document.getElementById('paymentSummary');
        if (selectedCount > 0) {
            summaryDiv.style.display = 'block';
            submitBtn.disabled = false;
        } else {
            summaryDiv.style.display = 'none';
            submitBtn.disabled = true;
        }
    }

    function openPaymentModal(userId, userName, token) {
        document.getElementById('payment_user_id').value = userId;
        document.getElementById('payment_user_name').value = userName;
        document.getElementById('paymentModal').classList.add('active');

        const historyDiv = document.getElementById('paymentHistory');
        const select = document.getElementById('payment_month');
        const submitBtn = document.getElementById('submitPaymentBtn');
        historyDiv.innerHTML = '';
        select.innerHTML = '';

        const payments = userPayments[token] || [];
        const monthNames = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

        if (payments.length === 0) {
            historyDiv.innerHTML = '<div class="text-muted p-2">No payment history found.</div>';
            select.innerHTML = '<option value="">No package assigned</option>';
            select.disabled = true;
            submitBtn.disabled = true;
            return;
        }

        const groups = {};
        payments.forEach(p => {
            const date  = new Date(p.date_time);
            const year  = date.getFullYear();
            const month = date.getMonth() + 1;
            const key   = year + '-' + (month < 10 ? '0' + month : month);
            if (!groups[key]) {
                groups[key] = { payments: [], total_paid: 0, price: parseFloat(p.price), pending: 0 };
            }
            groups[key].payments.push(p);
            groups[key].total_paid += parseFloat(p.paid);
        });

        Object.keys(groups).forEach(key => {
            groups[key].pending = groups[key].price - groups[key].total_paid;
        });

        const keys = Object.keys(groups).sort((a, b) => b.localeCompare(a));

        let html = '';
        keys.forEach(key => {
            const [year, month] = key.split('-');
            const monthName     = monthNames[parseInt(month) - 1] + ' ' + year;
            const group         = groups[key];

            html += `<div style="margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:6px;">
                <strong>${monthName}</strong> - Price: ₹${group.price} | Paid: ₹${group.total_paid} | <span style="color:${group.pending > 0 ? '#ef4444' : '#10b981'}; font-weight:bold;">Due: ₹${group.pending.toFixed(2)}</span>
            </div>`;
        });

        historyDiv.innerHTML = html;

        const pendingKeys = keys.filter(key => groups[key].pending > 0);

        if (pendingKeys.length === 0) {
            select.innerHTML = '<option value="">All month dues completed!</option>';
            select.disabled = true;
            submitBtn.disabled = true;
        } else {
            pendingKeys.forEach(key => {
                const [year, month] = key.split('-');
                const monthName = monthNames[parseInt(month) - 1] + ' ' + year;
                const option = document.createElement('option');
                option.value = key;
                option.textContent = `${monthName} (Due: ₹${groups[key].pending.toFixed(2)})`;
                select.appendChild(option);
            });
            select.disabled = false;
            submitBtn.disabled = false;
        }
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.remove('active');
        document.getElementById('paymentForm').reset();
    }
</script>
@endpush
