@extends('admin.layouts.app')

@section('title', 'Food Gallery - Sheetu Food')
@section('page_title', 'Food Gallery')

@push('styles')
<style>
    .gallery-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        background: #ffffff;
        padding: 18px 24px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
    }

    .btn-add-gallery {
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

    .btn-add-gallery:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.4);
        color: #ffffff;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
    }

    .gallery-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-subtle);
        border: 1px solid var(--border-subtle);
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
    }

    .gallery-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-card);
        border-color: #cbd5e1;
    }

    .gallery-img-wrap {
        width: 100%;
        height: 200px;
        background: #f1f5f9;
        overflow: hidden;
        position: relative;
    }

    .gallery-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .gallery-card:hover .gallery-img-wrap img {
        transform: scale(1.05);
    }

    .gallery-content {
        padding: 16px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .gallery-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 6px;
        word-break: break-word;
    }

    .gallery-date {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 14px;
    }

    .gallery-card-actions {
        display: flex;
        gap: 8px;
        border-top: 1px solid #f1f5f9;
        padding-top: 12px;
    }

    .btn-gallery-action {
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

    .btn-gallery-edit {
        background: #eef2ff;
        color: #4f46e5;
    }
    .btn-gallery-edit:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    .btn-gallery-del {
        background: #fef2f2;
        color: #dc2626;
    }
    .btn-gallery-del:hover {
        background: #dc2626;
        color: #ffffff;
    }
</style>
@endpush

@section('content')

<!-- Top Action Bar -->
<div class="gallery-top-bar">
    <div>
        <h5 class="fw-bold m-0" style="font-size: 1.1rem;">
            <i class="bi bi-images text-primary me-2"></i>Food Images Gallery
        </h5>
        <small class="text-muted">Total: {{ count($items) }} images uploaded to Cloudinary</small>
    </div>
    <button class="btn-add-gallery" data-bs-toggle="modal" data-bs-target="#addImageModal">
        <i class="bi bi-cloud-arrow-up-fill"></i>
        <span>Upload Image</span>
    </button>
</div>

<!-- Gallery Grid -->
@if($items->isNotEmpty())
    <div class="gallery-grid">
        @foreach($items as $item)
            <div class="gallery-card">
                <div class="gallery-img-wrap">
                    <img src="{{ $item->image }}" alt="{{ $item->title }}" loading="lazy">
                </div>
                <div class="gallery-content">
                    <div>
                        <h4 class="gallery-title">{{ $item->title }}</h4>
                        <div class="gallery-date">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M, Y') : 'N/A' }}
                        </div>
                    </div>
                    <div class="gallery-card-actions">
                        <button type="button" class="btn-gallery-action btn-gallery-edit editGalleryBtn"
                                data-id="{{ $item->id }}"
                                data-title="{{ $item->title }}"
                                data-image="{{ $item->image }}"
                                data-bs-toggle="modal"
                                data-bs-target="#editImageModal">
                            <i class="bi bi-pencil-square"></i>
                            <span>Edit</span>
                        </button>
                        <form method="POST" action="{{ route('admin.gallery.destroy', $item->id) }}" class="m-0 flex-grow-1" onsubmit="return confirm('Are you sure you want to delete this gallery item?');">
                            @csrf
                            <button type="submit" class="btn-gallery-action btn-gallery-del w-100">
                                <i class="bi bi-trash"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5 bg-white rounded-3 shadow-sm border p-4">
        <i class="bi bi-image fs-1 text-secondary mb-2 d-block"></i>
        <p class="text-muted fw-semibold">No gallery images uploaded yet.</p>
        <button class="btn-add-gallery" data-bs-toggle="modal" data-bs-target="#addImageModal">
            <i class="bi bi-cloud-arrow-up-fill"></i> Upload First Image
        </button>
    </div>
@endif

<!-- ADD IMAGE MODAL -->
<div class="modal fade" id="addImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>Upload to Food Gallery
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., Special Thali" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Image File <span class="text-danger">*</span></label>
                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" required>
                    <small class="text-muted">Allowed: JPG, PNG, GIF, WEBP (Max 5MB)</small>
                </div>
            </div>
            <div class="modal-footer border-top bg-light p-3">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    <i class="bi bi-cloud-upload me-1"></i> Upload Image
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT IMAGE MODAL -->
<div class="modal fade" id="editImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.gallery.update') }}" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <input type="hidden" name="gallery_id" id="edit-gallery-id">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Gallery Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="edit-gallery-title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Replace Image (Optional)</label>
                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                    <small class="text-muted">Leave empty to keep existing image</small>
                </div>
            </div>
            <div class="modal-footer border-top bg-light p-3">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    <i class="bi bi-check2-circle me-1"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.editGalleryBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('edit-gallery-id').value = btn.dataset.id;
                document.getElementById('edit-gallery-title').value = btn.dataset.title;
            });
        });
    });
</script>
@endpush
