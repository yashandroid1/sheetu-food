@extends('admin.layouts.app')

@section('title', 'Weekly Food Menu - Sheetu Food')
@section('page_title', 'Weekly Food Menu')

@push('styles')
<style>
    .week-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }

    .week-stat-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease;
    }

    .week-stat-card:hover {
        transform: translateY(-2px);
    }

    .week-stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .stat-blue { background: #eef2ff; color: #4f46e5; }
    .stat-green { background: #ecfdf5; color: #10b981; }

    .week-stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
    }

    .week-stat-val {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
    }

    /* Action Bar */
    .action-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .btn-add-food {
        background: var(--gradient-primary);
        color: #ffffff;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-add-food:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.4);
        color: #ffffff;
    }

    /* Days Grid */
    .week-days-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    .day-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
    }

    .day-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-card);
        border-color: #cbd5e1;
    }

    .day-card-header {
        background: var(--gradient-primary);
        color: #ffffff;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .day-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .day-badge-count {
        background: rgba(255, 255, 255, 0.2);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        backdrop-filter: blur(4px);
    }

    .day-card-body {
        padding: 18px 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .meal-row {
        background: #f8fafc;
        border-radius: 10px;
        padding: 10px 14px;
        border: 1px solid #f1f5f9;
    }

    .meal-type-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
    }

    .meal-type-label.brkf { color: #d97706; }
    .meal-type-label.luh { color: #0284c7; }
    .meal-type-label.dinr { color: #dc2626; }

    .meal-food-text {
        color: var(--text-main);
        font-size: 0.92rem;
        font-weight: 600;
        padding-left: 20px;
    }

    .day-card-actions {
        padding: 12px 20px;
        background: #fafafa;
        border-top: 1px solid var(--border-subtle);
        display: flex;
        gap: 8px;
    }

    .btn-meal-action {
        flex: 1;
        padding: 8px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-meal-edit {
        background: #eef2ff;
        color: #4f46e5;
    }
    .btn-meal-edit:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    .btn-meal-delete {
        background: #fef2f2;
        color: #dc2626;
    }
    .btn-meal-delete:hover {
        background: #dc2626;
        color: #ffffff;
    }
</style>
@endpush

@section('content')

<!-- Statistics -->
<div class="week-stats-grid">
    <div class="week-stat-card">
        <div class="week-stat-icon stat-blue">
            <i class="bi bi-calendar3"></i>
        </div>
        <div>
            <div class="week-stat-label">Total Days Configured</div>
            <div class="week-stat-val">{{ $totalDays }} / 7</div>
        </div>
    </div>
    <div class="week-stat-card">
        <div class="week-stat-icon stat-green">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
            <div class="week-stat-label">Days with All Meals</div>
            <div class="week-stat-val text-success">{{ $daysWithAllMeals }} Days</div>
        </div>
    </div>
</div>

<!-- Action Bar -->
<div class="action-header-bar">
    <h5 class="fw-bold m-0" style="font-size: 1.1rem;">
        <i class="bi bi-egg-fried text-primary me-2"></i>Weekly Routine Schedule
    </h5>
    <button class="btn-add-food" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-circle-fill"></i>
        <span>Add Food Day</span>
    </button>
</div>

<!-- Weekly Days Grid -->
@if($records->isNotEmpty())
    <div class="week-days-grid">
        @foreach($records as $row)
            @php
                $mealsCount = 0;
                if (!empty($row->brkf)) $mealsCount++;
                if (!empty($row->luh)) $mealsCount++;
                if (!empty($row->dinr)) $mealsCount++;
            @endphp
            <div class="day-card">
                <div class="day-card-header">
                    <h3 class="day-card-title">
                        <i class="bi bi-calendar-event"></i>
                        <span>{{ $row->day }}</span>
                    </h3>
                    <span class="day-badge-count">{{ $mealsCount }} Meals</span>
                </div>

                <div class="day-card-body">
                    <div class="meal-row">
                        <div class="meal-type-label brkf">
                            <i class="fas fa-coffee"></i>
                            <span>Breakfast</span>
                        </div>
                        <div class="meal-food-text">{{ $row->brkf ?: 'Not set' }}</div>
                    </div>

                    <div class="meal-row">
                        <div class="meal-type-label luh">
                            <i class="fas fa-utensils"></i>
                            <span>Lunch</span>
                        </div>
                        <div class="meal-food-text">{{ $row->luh ?: 'Not set' }}</div>
                    </div>

                    <div class="meal-row">
                        <div class="meal-type-label dinr">
                            <i class="fas fa-moon"></i>
                            <span>Dinner</span>
                        </div>
                        <div class="meal-food-text">{{ $row->dinr ?: 'Not set' }}</div>
                    </div>
                </div>

                <div class="day-card-actions">
                    <button type="button" class="btn-meal-action btn-meal-edit editBtn" 
                            data-id="{{ $row->id }}" 
                            data-day="{{ $row->day }}" 
                            data-brkf="{{ $row->brkf }}" 
                            data-luh="{{ $row->luh }}" 
                            data-dinr="{{ $row->dinr }}" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal">
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit</span>
                    </button>
                    <button type="button" class="btn-meal-action btn-meal-delete deleteBtn" 
                            data-id="{{ $row->id }}" 
                            data-day="{{ $row->day }}" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteModal">
                        <i class="bi bi-trash"></i>
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5 bg-white rounded-3 shadow-sm border p-4">
        <i class="bi bi-calendar-x fs-1 text-secondary mb-2 d-block"></i>
        <p class="text-muted fw-semibold">No weekly meal schedule found.</p>
        <button class="btn-add-food" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle-fill"></i> Add First Day
        </button>
    </div>
@endif

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.weekfoods.action') }}" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <input type="hidden" name="action" value="add">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle-fill text-primary me-2"></i>Add Week Food
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Day <span class="text-danger">*</span></label>
                    <select name="day" class="form-select" required>
                        <option value="">Select Day</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">
                        <i class="fas fa-coffee text-warning me-1"></i>Breakfast <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="brkf" class="form-control" placeholder="e.g., Poha, Tea" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">
                        <i class="fas fa-utensils text-info me-1"></i>Lunch <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="luh" class="form-control" placeholder="e.g., Dal, Rice, Roti" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">
                        <i class="fas fa-moon text-danger me-1"></i>Dinner <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="dinr" class="form-control" placeholder="e.g., Paneer, Roti" required>
                </div>
            </div>
            <div class="modal-footer border-top bg-light p-3">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    <i class="bi bi-check2-circle me-1"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.weekfoods.action') }}" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit-id">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Week Food
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Day <span class="text-danger">*</span></label>
                    <select name="day" id="edit-day" class="form-select" required>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">
                        <i class="fas fa-coffee text-warning me-1"></i>Breakfast <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="brkf" id="edit-brkf" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">
                        <i class="fas fa-utensils text-info me-1"></i>Lunch <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="luh" id="edit-luh" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">
                        <i class="fas fa-moon text-danger me-1"></i>Dinner <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="dinr" id="edit-dinr" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer border-top bg-light p-3">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    <i class="bi bi-save me-1"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.weekfoods.action') }}" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="delete-id">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Week Food
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-trash3-fill text-danger fs-1 mb-2 d-block"></i>
                <h5 class="fw-bold mt-2">Are you sure?</h5>
                <p class="text-muted">You want to delete the schedule for <strong id="delete-day"></strong>?</p>
                <p class="text-danger small mb-0"><i class="bi bi-info-circle me-1"></i>This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-top bg-light p-3">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger px-4 fw-bold">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Populate edit modal
        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('edit-id').value = btn.dataset.id;
                document.getElementById('edit-day').value = btn.dataset.day;
                document.getElementById('edit-brkf').value = btn.dataset.brkf;
                document.getElementById('edit-luh').value = btn.dataset.luh;
                document.getElementById('edit-dinr').value = btn.dataset.dinr;
            });
        });

        // Populate delete modal
        document.querySelectorAll('.deleteBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('delete-id').value = btn.dataset.id;
                document.getElementById('delete-day').innerText = btn.dataset.day;
            });
        });
    });
</script>
@endpush
