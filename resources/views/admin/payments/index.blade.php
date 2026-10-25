@extends('admin.layouts.app')

@section('title', 'Payment Management - Sheetu Food')
@section('page_title', 'Payment Management')

@push('styles')
<style>
    .payment-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .payment-stat-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 22px;
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
        position: relative;
        overflow: hidden;
        transition: all 0.25s ease;
    }

    .payment-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-card);
    }

    .payment-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .payment-stat-card.total::before { background: var(--gradient-primary); }
    .payment-stat-card.paid::before { background: var(--gradient-success); }
    .payment-stat-card.pending::before { background: var(--gradient-danger); }

    .payment-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 12px;
    }

    .payment-stat-card.total .payment-stat-icon { background: #eef2ff; color: #4f46e5; }
    .payment-stat-card.paid .payment-stat-icon { background: #ecfdf5; color: #10b981; }
    .payment-stat-card.pending .payment-stat-icon { background: #fef2f2; color: #ef4444; }

    .payment-stat-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .payment-stat-value {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.5px;
    }

    /* Filter Card */
    .filter-box {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
    }

    /* Payments Accordion */
    .payments-panel {
        background: #ffffff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
        padding: 24px;
    }

    .accordion-header-row {
        display: grid;
        grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr 40px;
        padding: 12px 20px;
        background: #f8fafc;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .payment-accordion .accordion-item {
        border: 1px solid var(--border-subtle);
        border-radius: 12px !important;
        margin-bottom: 12px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }

    .payment-accordion .accordion-button {
        display: grid;
        grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr 40px;
        align-items: center;
        padding: 16px 20px;
        background: #ffffff;
        border: none;
        box-shadow: none;
        font-size: 0.9rem;
    }

    .payment-accordion .accordion-button::after {
        margin-left: auto;
    }

    .payment-accordion .accordion-button:not(.collapsed) {
        background: #f8fafc;
        color: var(--primary);
    }

    .payment-grid-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
        background: #f8fafc;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 16px;
    }

    .detail-item-title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .detail-item-val {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .timeline-wrap {
        border-top: 1px dashed var(--border-subtle);
        padding-top: 14px;
    }

    .timeline-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        border-radius: 8px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        margin-bottom: 6px;
        font-size: 0.85rem;
    }

    @media (max-width: 768px) {
        .accordion-header-row { display: none; }
        .payment-accordion .accordion-button {
            grid-template-columns: 1fr;
            gap: 6px;
        }
    }
</style>
@endpush

@section('content')

<!-- Statistics Cards -->
<div class="payment-stats-grid">
    <div class="payment-stat-card total">
        <div class="payment-stat-icon"><i class="bi bi-cash-stack"></i></div>
        <div class="payment-stat-label">Total Amount Due</div>
        <div class="payment-stat-value">₹{{ number_format($totalPrice, 2) }}</div>
    </div>
    <div class="payment-stat-card paid">
        <div class="payment-stat-icon"><i class="bi bi-check2-circle"></i></div>
        <div class="payment-stat-label">Total Paid</div>
        <div class="payment-stat-value text-success">₹{{ number_format($totalPaid, 2) }}</div>
    </div>
    <div class="payment-stat-card pending">
        <div class="payment-stat-icon"><i class="bi bi-clock-history"></i></div>
        <div class="payment-stat-label">Total Pending</div>
        <div class="payment-stat-value text-danger">₹{{ number_format($totalPending, 2) }}</div>
    </div>
</div>

<!-- Filter Card -->
<div class="filter-box">
    <h5 class="fw-bold mb-3" style="font-size: 1.05rem;">
        <i class="bi bi-funnel-fill text-primary me-2"></i>Filter Payments
    </h5>
    <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
        <div class="col-md-3 col-12">
            <label class="form-label fw-bold small text-muted">Mobile Number</label>
            <input type="text" class="form-control" name="mobile_search" value="{{ $mobileSearch }}" placeholder="Search by mobile...">
        </div>
        <div class="col-md-3 col-6">
            <label class="form-label fw-bold small text-muted">From Date</label>
            <input type="date" class="form-control" name="from_date" value="{{ $fromDate }}">
        </div>
        <div class="col-md-3 col-6">
            <label class="form-label fw-bold small text-muted">To Date</label>
            <input type="date" class="form-control" name="to_date" value="{{ $toDate }}">
        </div>
        <div class="col-md-3 col-12">
            <label class="form-label fw-bold small text-muted">Package</label>
            <select class="form-select" name="pkg">
                <option value="all" {{ ($packageFilter == 'all' || !$packageFilter) ? 'selected' : '' }}>All Packages</option>
                @foreach ($packages as $pkg)
                    <option value="{{ $pkg }}" {{ ($packageFilter == $pkg) ? 'selected' : '' }}>
                        Package {{ $pkg }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4 fw-bold">
                <i class="bi bi-search me-1"></i> Apply Filters
            </button>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary px-3">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Payments Accordion Card -->
<div class="payments-panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0" style="font-size: 1.05rem;">
            <i class="bi bi-receipt text-primary me-2"></i>All Grouped Payments
            @if($hasFilters)
                <span class="badge bg-primary ms-2" style="font-size: 0.75rem;">Filtered</span>
            @endif
        </h5>
        <span class="badge bg-light text-dark border">{{ count($paymentsGrouped) }} Records</span>
    </div>

    @if(empty($paymentsGrouped))
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
            <p>No payments found matching the filters.</p>
        </div>
    @else
        <div class="accordion-header-row">
            <div>👤 Name</div>
            <div>📱 Mobile</div>
            <div>📅 Month</div>
            <div>📦 Package</div>
            <div></div>
        </div>

        <div class="accordion payment-accordion" id="paymentsAccordion">
            @foreach($paymentsGrouped as $index => $group)
                @php
                    $uniqueId = 'payment_' . $index;
                    if ($group['remain'] <= 0) {
                        $statusClass = 'bg-success';
                        $statusText = 'Paid';
                    } elseif ($group['paid'] > 0) {
                        $statusClass = 'bg-warning text-dark';
                        $statusText = 'Partial';
                    } else {
                        $statusClass = 'bg-danger';
                        $statusText = 'Pending';
                    }
                @endphp
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading_{{ $uniqueId }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapse_{{ $uniqueId }}" aria-expanded="false" aria-controls="collapse_{{ $uniqueId }}">
                            <div class="fw-bold text-dark">{{ $group['name'] }}</div>
                            <div class="text-muted"><i class="bi bi-phone me-1"></i>{{ $group['mobile'] }}</div>
                            <div><span class="badge bg-light text-dark border">{{ $group['month_display'] }}</span></div>
                            <div><span class="badge bg-light text-primary border">Pkg: {{ $group['pkg'] }}</span></div>
                            <div></div>
                        </button>
                    </h2>
                    <div id="collapse_{{ $uniqueId }}" class="accordion-collapse collapse" 
                         aria-labelledby="heading_{{ $uniqueId }}" data-bs-parent="#paymentsAccordion">
                        <div class="accordion-body">
                            <div class="payment-grid-details">
                                <div>
                                    <div class="detail-item-title">Latest Date</div>
                                    <div class="detail-item-val">
                                        {{ \Carbon\Carbon::parse($group['latest_date'])->format('d M, Y') }}
                                    </div>
                                </div>
                                <div>
                                    <div class="detail-item-title">Package</div>
                                    <div class="detail-item-val">Package {{ $group['pkg'] }}</div>
                                </div>
                                <div>
                                    <div class="detail-item-title">Total Price</div>
                                    <div class="detail-item-val text-primary">₹{{ number_format($group['price'], 2) }}</div>
                                </div>
                                <div>
                                    <div class="detail-item-title">Total Paid</div>
                                    <div class="detail-item-val text-success">₹{{ number_format($group['paid'], 2) }}</div>
                                </div>
                                <div>
                                    <div class="detail-item-title">Remaining Due</div>
                                    <div class="detail-item-val {{ $group['remain'] > 0 ? 'text-danger' : 'text-success' }}">
                                        ₹{{ number_format($group['remain'], 2) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="detail-item-title">Status</div>
                                    <div><span class="badge {{ $statusClass }}">{{ $statusText }}</span></div>
                                </div>
                            </div>

                            @if(count($group['payment_dates']) > 1)
                                <div class="timeline-wrap">
                                    <div class="fw-bold small text-muted mb-2"><i class="bi bi-clock-history me-1"></i>Payment History Log</div>
                                    @php
                                        $dates = $group['payment_dates'];
                                        usort($dates, function($a, $b) {
                                            return strtotime($b['date']) - strtotime($a['date']);
                                        });
                                    @endphp
                                    @foreach($dates as $pd)
                                        <div class="timeline-row">
                                            <span><i class="bi bi-calendar3 me-1 text-muted"></i>{{ \Carbon\Carbon::parse($pd['date'])->format('d M, Y h:i A') }}</span>
                                            <div>
                                                <span class="me-3 text-muted">Price: ₹{{ number_format($pd['price'], 2) }}</span>
                                                <strong class="text-success">Paid: ₹{{ number_format($pd['paid'], 2) }}</strong>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
