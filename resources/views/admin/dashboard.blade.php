@extends('admin.layouts.app')

@section('title', 'Dashboard Overview - Sheetu Food')
@section('page_title', 'Dashboard Overview')

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card-modern {
        background: #ffffff;
        padding: 24px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-card);
        border-color: #cbd5e1;
    }

    .stat-card-modern::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    .stat-card-modern.blue::after { background: var(--gradient-primary); }
    .stat-card-modern.green::after { background: var(--gradient-success); }
    .stat-card-modern.orange::after { background: var(--gradient-warning); }
    .stat-card-modern.cyan::after { background: var(--gradient-info); }

    .stat-info-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .stat-info-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }

    .stat-info-meta {
        font-size: 0.82rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .stat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .stat-card-modern.blue .stat-icon-box { background: #eef2ff; color: #4f46e5; }
    .stat-card-modern.green .stat-icon-box { background: #ecfdf5; color: #10b981; }
    .stat-card-modern.orange .stat-icon-box { background: #fffbeb; color: #f59e0b; }
    .stat-card-modern.cyan .stat-icon-box { background: #ecfeff; color: #06b6d4; }

    /* Tables Grid */
    .dashboard-tables-grid {
        display: grid;
        grid-template-columns: 1.25fr 1fr;
        gap: 24px;
    }

    .table-panel {
        background: #ffffff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
        overflow: hidden;
    }

    .table-panel-header {
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border-subtle);
        background: #ffffff;
    }

    .table-panel-header h2 {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-view-all {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--primary);
        padding: 6px 12px;
        border-radius: 8px;
        background: #eef2ff;
        transition: all 0.2s ease;
    }

    .btn-view-all:hover {
        background: var(--primary);
        color: #ffffff;
    }

    .table-modern {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .table-modern thead th {
        background: #f8fafc;
        padding: 12px 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border-subtle);
    }

    .table-modern tbody td {
        padding: 14px 20px;
        font-size: 0.88rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    .table-modern tbody tr:hover {
        background: #fafafa;
    }

    .status-badge-soft {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-badge-soft.active { background: #d1fae5; color: #065f46; }
    .status-badge-soft.inactive { background: #fee2e2; color: #991b1b; }
    .status-badge-soft.payment { background: #fef3c7; color: #b45309; }

    .btn-table-action {
        padding: 5px 12px;
        background: #f1f5f9;
        color: #334155;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .btn-table-action:hover {
        background: var(--primary);
        color: #ffffff;
    }

    @media (max-width: 991px) {
        .dashboard-tables-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<!-- Statistics Cards -->
<div class="stats-grid">
    <!-- Total Users -->
    <div class="stat-card-modern blue">
        <div>
            <div class="stat-info-title">Total Users</div>
            <div class="stat-info-value">{{ number_format($counts['users']) }}</div>
            <div class="stat-info-meta">
                <i class="bi bi-person-check-fill text-success me-1"></i>
                <strong>{{ number_format($counts['active']) }}</strong> active users
            </div>
        </div>
        <div class="stat-icon-box">
            <i class="bi bi-people-fill"></i>
        </div>
    </div>

    <!-- Payments -->
    <div class="stat-card-modern green">
        <div>
            <div class="stat-info-title">Payments Logged</div>
            <div class="stat-info-value">{{ number_format($counts['payments']) }}</div>
            <div class="stat-info-meta">
                <i class="bi bi-cash-stack text-success me-1"></i>
                Total: <strong>₹{{ number_format($counts['payments_sum'], 2) }}</strong>
            </div>
        </div>
        <div class="stat-icon-box">
            <i class="bi bi-wallet2"></i>
        </div>
    </div>

    <!-- Distributions -->
    <div class="stat-card-modern orange">
        <div>
            <div class="stat-info-title">Distributions</div>
            <div class="stat-info-value">{{ number_format($counts['distributes']) }}</div>
            <div class="stat-info-meta">
                <i class="bi bi-clock-history text-warning me-1"></i>
                Today: <strong>{{ number_format($counts['today_distributes']) }}</strong> distributions
            </div>
        </div>
        <div class="stat-icon-box">
            <i class="bi bi-box-seam-fill"></i>
        </div>
    </div>

    <!-- Issues -->
    <div class="stat-card-modern cyan">
        <div>
            <div class="stat-info-title">Issue Tickets</div>
            <div class="stat-info-value">{{ number_format($counts['issues']) }}</div>
            <div class="stat-info-meta">
                <i class="bi bi-exclamation-circle text-info me-1"></i>
                Registered support requests
            </div>
        </div>
        <div class="stat-icon-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
    </div>
</div>

<!-- Tables Grid -->
<div class="dashboard-tables-grid">
    <!-- Recent Users -->
    <div class="table-panel">
        <div class="table-panel-header">
            <h2><i class="bi bi-person-lines-fill text-primary"></i> Recent Users</h2>
            <a href="{{ route('admin.users.index') }}" class="btn-view-all">View All →</a>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $u)
                        <tr>
                            <td><strong class="text-muted">#{{ $u->id }}</strong></td>
                            <td class="fw-bold">{{ $u->name }}</td>
                            <td>{{ $u->mobile ?: 'N/A' }}</td>
                            <td>
                                @php
                                    $st = strtolower($u->status);
                                    $cls = $st === 'active' ? 'active' : ($st === 'payment' ? 'payment' : 'inactive');
                                @endphp
                                <span class="status-badge-soft {{ $cls }}">{{ $u->status }}</span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.users.index', ['action' => 'edit', 'id' => $u->id]) }}" class="btn-table-action">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No users registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="table-panel">
        <div class="table-panel-header">
            <h2><i class="bi bi-credit-card-2-front-fill text-success"></i> Recent Payments</h2>
            <a href="{{ route('admin.payments.index') }}" class="btn-view-all">View All →</a>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Mobile</th>
                        <th>Package</th>
                        <th>Paid</th>
                        <th style="text-align: right;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $p)
                        <tr>
                            <td class="fw-bold">{{ $p->mobile }}</td>
                            <td><span class="badge bg-light text-dark border">Pkg: {{ $p->pkg }}</span></td>
                            <td class="fw-bold text-success">₹{{ number_format($p->paid, 2) }}</td>
                            <td style="text-align: right;" class="text-muted">
                                {{ \Carbon\Carbon::parse($p->date_time)->format('d M, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No payments logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
