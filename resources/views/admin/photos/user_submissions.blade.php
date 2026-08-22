@extends("admin.admin_app")

@section("content")

<style>
    .custom-dark-table {
        background-color: #1a2234 !important;
        border: 1px solid #2a3447 !important;
        color: #ffffff !important;
    }
    .custom-dark-table thead th {
        background-color: #121824 !important;
        border-color: #2a3447 !important;
        color: #94a3b8 !important;
        font-weight: 600 !important;
    }
    .custom-dark-table tbody tr {
        background-color: #1a2234 !important;
        border-color: #2a3447 !important;
        color: #ffffff !important;
    }
    .custom-dark-table tbody tr:hover {
        background-color: #222c42 !important;
    }
    .custom-dark-table tbody tr td {
        background-color: transparent !important;
        border-color: #2a3447 !important;
        color: #ffffff !important;
        vertical-align: middle !important;
    }
    .card-box {
        background-color: #1a2234 !important;
        border: 1px solid #2a3447 !important;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">

                        <div class="row mb-3 align-items-center">
                            <div class="col-md-6">
                                <h4 class="m-t-0 header-title"><i class="fa fa-camera text-info"></i> <b>Photos Submitted by Users</b></h4>
                                <p class="text-muted font-13 m-b-10">Review and approve user-submitted Google Drive and uploaded photos.</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <span class="badge badge-warning p-2 font-13 mr-2"><i class="fa fa-clock-o"></i> Pending: {{ $pendingCount }}</span>
                                <span class="badge badge-success p-2 font-13"><i class="fa fa-check"></i> Approved: {{ $approvedCount }}</span>
                            </div>
                        </div>

                        <!-- Filter and Search Bar -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.photos.user-submissions') }}" class="form-inline">
                                    <div class="form-group mr-2">
                                        <label class="mr-2 text-white">Status:</label>
                                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                                            <option value="pending" {{ request('status') == 'pending' || !request()->has('status') ? 'selected' : '' }}>Pending Review</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Approved & Active</option>
                                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" name="s" class="form-control form-control-sm" placeholder="Search photo or user..." value="{{ request('s') }}" style="background: #121824; border: 1px solid #2a3447; color: #fff;">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered custom-dark-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="width: 80px;">Thumbnail</th>
                                        <th>Photo Title</th>
                                        <th>Submitted By</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-center" style="min-width: 150px;">Approval Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($photos as $i => $photo)
                                        <tr>
                                            <td>{{ $photos->firstItem() + $i }}</td>
                                            <td class="text-center">
                                                @if($photo->drive_file_id)
                                                    <img src="https://drive.google.com/thumbnail?id={{ $photo->drive_file_id }}&sz=w100" style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 1px solid #2a3447;" onclick="previewPhoto('{{ $photo->image_url }}', '{{ $photo->drive_file_id }}', '{{ addslashes($photo->title) }}')" alt="thumb">
                                                @elseif($photo->image_path)
                                                    <img src="{{ $photo->image_url }}" style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 1px solid #2a3447;" onclick="previewPhoto('{{ $photo->image_url }}', null, '{{ addslashes($photo->title) }}')" alt="thumb">
                                                @else
                                                    <i class="fa fa-image text-muted" style="font-size: 24px;"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-white">{{ $photo->title }}</strong>
                                                @if($photo->description)
                                                    <small class="text-muted d-block" style="max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $photo->description }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($photo->user)
                                                    <strong class="text-info">{{ $photo->user->name }}</strong>
                                                    <small class="text-muted d-block">{{ $photo->user->email }}</small>
                                                @else
                                                    <span class="text-muted">User #{{ $photo->added_by }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $photo->category ?: 'General' }}</td>
                                            <td>
                                                @if($photo->license_price > 0)
                                                    <span class="badge badge-warning">${{ number_format($photo->license_price, 2) }}</span>
                                                @else
                                                    <span class="badge badge-success">Free</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($photo->status === 'pending')
                                                    <span class="badge badge-warning" style="padding: 5px 10px; font-size: 11px;"><i class="fa fa-clock-o"></i> Pending</span>
                                                @elseif($photo->status === 'rejected')
                                                    <span class="badge badge-danger" style="padding: 5px 10px; font-size: 11px;"><i class="fa fa-times-circle"></i> Rejected</span>
                                                @else
                                                    <span class="badge badge-success" style="padding: 5px 10px; font-size: 11px;"><i class="fa fa-check-circle"></i> Approved</span>
                                                @endif
                                            </td>
                                            <td>{{ $photo->created_at->format('M d, Y') }}</td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                <button type="button" class="btn btn-sm btn-info m-r-5" onclick="previewPhoto('{{ $photo->image_url }}', '{{ $photo->drive_file_id }}', '{{ addslashes($photo->title) }}')" data-toggle="tooltip" title="View Full Photo">
                                                    <i class="fa fa-eye"></i>
                                                </button>

                                                @if($photo->status !== 'active')
                                                    <button type="button" class="btn btn-sm btn-success m-r-5" data-toggle="tooltip" title="Approve & Publish Live"
                                                        onclick="confirmApprove('{{ route('admin.photos.user-submissions.approve', $photo->id) }}', '{{ addslashes($photo->title) }}')">
                                                        <i class="fa fa-check"></i> Approve
                                                    </button>
                                                @endif

                                                @if($photo->status !== 'rejected')
                                                    <button type="button" class="btn btn-sm btn-warning m-r-5" data-toggle="tooltip" title="Reject Submission"
                                                        onclick="confirmReject('{{ route('admin.photos.user-submissions.reject', $photo->id) }}', '{{ addslashes($photo->title) }}')">
                                                        <i class="fa fa-ban"></i> Reject
                                                    </button>
                                                @endif

                                                <a href="{{ route('admin.photos.edit', $photo->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit Photo Details">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">No user-submitted photos found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <nav class="paging_simple_numbers">
                            @include('admin.pagination', ['paginator' => $photos])
                        </nav>

                    </div>
                </div>
            </div>
        </div>
        @include("admin.copyright")
    </div>
</div>

<!-- Photo Preview Modal -->
<div id="photoPreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: #1a2234; border: 1px solid #32383e; color: #fff; border-radius: 8px;">
            <div class="modal-header" style="border-bottom: 1px solid #32383e;">
                <h5 class="modal-title mt-0" id="photoModalTitle"><i class="fa fa-camera text-info mr-1"></i> Photo Preview</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3 text-center" style="background: #0d1117;">
                <img id="modalPhotoImage" src="" style="max-height: 500px; max-width: 100%; border-radius: 6px; object-fit: contain; box-shadow: 0 4px 15px rgba(0,0,0,0.5);" alt="photo preview">
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
    function previewPhoto(imageUrl, driveFileId, title) {
        document.getElementById('photoModalTitle').innerText = title ? title : 'Photo Preview';
        var img = document.getElementById('modalPhotoImage');
        if (driveFileId && driveFileId !== '') {
            img.src = 'https://drive.google.com/thumbnail?id=' + driveFileId + '&sz=w1200';
        } else if (imageUrl && imageUrl !== '') {
            img.src = imageUrl;
        }
        $('#photoPreviewModal').modal('show');
    }

    function confirmApprove(url, title) {
        Swal.fire({
            title: 'Approve Photo?',
            text: 'Are you sure you want to approve "' + title + '" and publish it to the library?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10c469',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-check"></i> Yes, Approve',
            cancelButtonText: 'Cancel',
            background: '#1a2234',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    function confirmReject(url, title) {
        Swal.fire({
            title: 'Reject Submission?',
            text: 'Are you sure you want to reject "' + title + '"?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f34943',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-ban"></i> Yes, Reject',
            cancelButtonText: 'Cancel',
            background: '#1a2234',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    @if(Session::has('flash_message'))
        Swal.fire({
            position: 'center',
            icon: 'success',
            title: "{{ Session::get('flash_message') }}",
            showConfirmButton: true,
            confirmButtonColor: '#10c469',
            background: '#1a2234',
            color: '#fff'
        });
    @endif
</script>
@endsection
