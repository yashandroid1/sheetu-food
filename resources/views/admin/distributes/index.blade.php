@extends('admin.layouts.app')

@section('title', 'Food Distributions - Sheetu Food')
@section('page_title', 'Food Distributions')

@push('styles')
<style>
    .dist-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .dist-stat-card {
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

    .dist-stat-card:hover {
        transform: translateY(-2px);
    }

    .dist-stat-icon {
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
    .icon-success { background: #ecfdf5; color: #10b981; }
    .icon-warning { background: #fffbeb; color: #f59e0b; }
    .icon-info { background: #ecfeff; color: #06b6d4; }
    .icon-danger { background: #fef2f2; color: #ef4444; }

    .dist-stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
    }

    .dist-stat-val {
        font-size: 1.45rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
    }

    /* Filter Box */
    .dist-filter-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
    }

    .dist-main-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
        overflow: hidden;
    }

    .dist-table {
        width: 100%;
        border-collapse: collapse;
    }

    .dist-table th {
        background: #f8fafc;
        padding: 14px 18px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .dist-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
        vertical-align: middle;
    }

    .dist-mobile-cards {
        display: none;
        flex-direction: column;
        gap: 12px;
        padding: 16px;
    }

    .distribution-card {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-lg);
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }

    @media (max-width: 768px) {
        .dist-table-wrap { display: none; }
        .dist-mobile-cards { display: flex; }
    }
</style>
@endpush

@section('content')

<!-- Statistics Cards -->
<div class="dist-stats-grid">
    <div class="dist-stat-card">
        <div class="dist-stat-icon icon-primary"><i class="bi bi-box-seam-fill"></i></div>
        <div>
            <div class="dist-stat-label">Total Distributions</div>
            <div class="dist-stat-val">{{ number_format($stats['total_distributions']) }}</div>
        </div>
    </div>
    <div class="dist-stat-card">
        <div class="dist-stat-icon icon-success"><i class="bi bi-currency-rupee"></i></div>
        <div>
            <div class="dist-stat-label">Total Amount</div>
            <div class="dist-stat-val text-success">₹{{ number_format($stats['total_amount'], 2) }}</div>
        </div>
    </div>
    <div class="dist-stat-card">
        <div class="dist-stat-icon icon-warning"><i class="bi bi-cup-hot-fill"></i></div>
        <div>
            <div class="dist-stat-label">Breakfasts</div>
            <div class="dist-stat-val">{{ number_format($stats['total_breakfast']) }}</div>
        </div>
    </div>
    <div class="dist-stat-card">
        <div class="dist-stat-icon icon-info"><i class="bi bi-sun-fill"></i></div>
        <div>
            <div class="dist-stat-label">Lunches</div>
            <div class="dist-stat-val">{{ number_format($stats['total_lunch']) }}</div>
        </div>
    </div>
    <div class="dist-stat-card">
        <div class="dist-stat-icon icon-danger"><i class="bi bi-moon-stars-fill"></i></div>
        <div>
            <div class="dist-stat-label">Dinners</div>
            <div class="dist-stat-val">{{ number_format($stats['total_dinner']) }}</div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="dist-filter-card">
    <form method="GET" action="{{ route('admin.distributes.index') }}">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <label class="form-label fw-bold small text-muted">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label fw-bold small text-muted">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label fw-bold small text-muted">Mobile Number</label>
                <input type="text" name="mobile" class="form-control" placeholder="Search mobile" value="{{ $mobile }}">
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label fw-bold small text-muted">Meal Type</label>
                <select name="meal_type" class="form-select">
                    <option value="">All Meals</option>
                    <option value="brkf" {{ $mealType === 'brkf' ? 'selected' : '' }}>Breakfast</option>
                    <option value="luh" {{ $mealType === 'luh' ? 'selected' : '' }}>Lunch</option>
                    <option value="dinr" {{ $mealType === 'dinr' ? 'selected' : '' }}>Dinner</option>
                </select>
            </div>
            <div class="col-md-2 col-12 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 fw-bold">
                    <i class="bi bi-search me-1"></i> Search
                </button>
                <a href="{{ route('admin.distributes.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Main Records Card -->
<div class="dist-main-card">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
        <h5 class="fw-bold m-0" style="font-size: 1.05rem;">
            <i class="bi bi-calendar-check text-primary me-2"></i>Distribution Records
        </h5>
        <span class="badge bg-primary">{{ number_format($numRecords) }} Records</span>
    </div>

    <!-- Desktop View Table -->
    <div class="dist-table-wrap table-responsive">
        <table class="dist-table">
            <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>Mobile</th>
                    <th class="text-center">Breakfast</th>
                    <th class="text-center">Lunch</th>
                    <th class="text-center">Dinner</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                    <tr>
                        <td class="ps-4 text-muted"><strong>#{{ $r->id }}</strong></td>
                        <td><strong>{{ $r->mobile }}</strong></td>
                        <td class="text-center">
                            @if($r->brkf)
                                <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($r->luh)
                                <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($r->dinr)
                                <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td><i class="bi bi-calendar3 text-muted me-1"></i>{{ \Carbon\Carbon::parse($r->date)->format('d M, Y') }}</td>
                        <td><i class="bi bi-clock text-muted me-1"></i>{{ \Carbon\Carbon::parse($r->time)->format('h:i A') }}</td>
                        <td><strong class="text-success">₹{{ number_format($r->amount, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            <p class="m-0">{{ $emptyMessage }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($records->isNotEmpty())
                <tfoot>
                    <tr class="bg-light">
                        <td colspan="7" class="text-end pe-4 fw-bold">Total Amount:</td>
                        <td><strong class="text-success fs-5">₹{{ number_format($stats['total_amount'], 2) }}</strong></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <!-- Mobile View Cards -->
    <div class="dist-mobile-cards">
        @forelse($records as $r)
            <div class="distribution-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="fw-bold"><i class="bi bi-telephone text-primary me-2"></i>{{ $r->mobile }}</div>
                    <span class="badge bg-light text-dark border">#{{ $r->id }}</span>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($r->date)->format('d M, Y') }}</span>
                    <span><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($r->time)->format('h:i A') }}</span>
                </div>
                <div class="d-flex gap-2 flex-wrap mb-2">
                    @if($r->brkf)
                        <span class="badge bg-success"><i class="bi bi-cup-hot me-1"></i>Breakfast</span>
                    @endif
                    @if($r->luh)
                        <span class="badge bg-success"><i class="bi bi-sun me-1"></i>Lunch</span>
                    @endif
                    @if($r->dinr)
                        <span class="badge bg-success"><i class="bi bi-moon me-1"></i>Dinner</span>
                    @endif
                    @if(!$r->brkf && !$r->luh && !$r->dinr)
                        <span class="badge bg-secondary">No Meals</span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="small fw-bold text-muted">Amount</span>
                    <strong class="text-success fs-6">₹{{ number_format($r->amount, 2) }}</strong>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                <p>{{ $emptyMessage }}</p>
            </div>
        @endforelse

        @if($records->isNotEmpty())
            <div class="distribution-card text-white" style="background: var(--gradient-primary);">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Grand Total</span>
                    <span class="fs-4 fw-bold">₹{{ number_format($stats['total_amount'], 2) }}</span>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
