@extends("admin.admin_app")

@section("content")

  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                <div class="alert alert-info">
                    <strong><i class="fa fa-info-circle"></i> Background Processing Cron Job</strong><br>
                    To ensure effects are processed continuously in the background, please add the following Cron Job in your Hostinger cPanel to run <strong>Once Per Minute (* * * * *)</strong>:
                    <div class="input-group mt-2 mb-1" style="max-width: 700px;">
                        <input type="text" class="form-control" id="cronCommand" value="/usr/bin/php /home/u273790872/domains/cineworm.org/public_html/stock/artisan schedule:run >> /dev/null 2>&1" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" onclick="copyCron()">Copy</button>
                        </div>
                    </div>
                </div>

                <script>
                    function copyCron() {
                        var copyText = document.getElementById("cronCommand");
                        copyText.select();
                        copyText.setSelectionRange(0, 99999);
                        document.execCommand("copy");
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Copied!',
                                text: 'Cron command copied to clipboard.',
                                showConfirmButton: false,
                                timer: 2000,
                                background: '#1a2234',
                                color: '#fff'
                            });
                        } else {
                            alert("Cron command copied to clipboard!");
                        }
                    }
                </script>

                <div class="row">
                  <div class="col-md-3">
                     <a href="{{ route('admin.effects.create') }}" class="btn btn-success btn-md waves-effect waves-light m-b-20" data-toggle="tooltip" title="Add Effect"><i class="fa fa-plus"></i> Add New Effect</a>
                  </div>
                  <div class="col-md-6">
                     {!! Form::open(array('url' => 'admin/effects','class'=>'app-search','id'=>'search','role'=>'form','method'=>'get')) !!}
                      <input type="text" name="s" placeholder="Search effects by title..." class="form-control">
                      <button type="submit"><i class="fa fa-search"></i></button>
                     {!! Form::close() !!}
                  </div>
                </div>

                @if(Session::has('flash_message'))
                    <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span></button>
                        {{ Session::get('flash_message') }}
                    </div>
                @endif

                <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th>Category</th>
                      <th>Effect URL / GD Link</th>
                      <th>Access Type</th>
                      <th>Active Status</th>
                      <th>Process Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                   @foreach($effects as $effect)
                    <tr id="card_box_id_{{$effect->id}}" data-effect-id="{{ $effect->id }}" data-status="{{ $effect->status }}">
                      <td><strong>{{ $effect->title }}</strong></td>
                      <td><span class="badge badge-info">{{ $effect->category ?? 'General' }}</span></td>
                      <td>
                        <div class="input-group input-group-sm" style="min-width: 380px;">
                          <input type="text" class="form-control form-control-sm" id="effect_url_{{ $effect->id }}" value="{{ $effect->effect_url }}" readonly>
                          <div class="input-group-append">
                            @if($effect->processed_url)
                              <button class="btn btn-sm btn-info btn-preview-processed" type="button" onclick="showPreview('{{ $effect->processed_url }}')" data-toggle="tooltip" title="Preview Processed Video"><i class="fa fa-play-circle"></i> Preview</button>
                            @endif
                            <button class="btn btn-sm btn-secondary" type="button" onclick="copyEffectUrl('effect_url_{{ $effect->id }}')" data-toggle="tooltip" title="Copy URL"><i class="fa fa-copy"></i> Copy</button>
                            <a href="{{ $effect->effect_url }}" target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Open Link"><i class="fa fa-external-link"></i> Open</a>
                          </div>
                        </div>
                      </td>
                      <td>
                        @if($effect->is_premium || ($effect->license_price && $effect->license_price > 0))
                            <span class="badge badge-warning"><i class="fa fa-star"></i> Premium (${{ number_format($effect->license_price, 2) }})</span>
                        @else
                            <span class="badge badge-success">Free</span>
                        @endif
                      </td>
                      <td>@if($effect->is_active)<span class="badge badge-success">Active</span> @else<span class="badge badge-danger">Inactive</span>@endif</td>
                      <td class="status-cell">
                          @if($effect->status == 'ready')
                              <span class="badge badge-success" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-check-circle"></i> Ready</span>
                              @if($effect->converted_bytes !== null)
                                  <br><small style="color: #aaa;">{{ number_format($effect->converted_bytes / 1048576, 2) }} MB</small>
                              @endif
                          @elseif($effect->status == 'processing')
                              <span class="badge badge-warning" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-spin fa-spinner"></i> Processing...</span>
                          @elseif($effect->status == 'error')
                              <span class="badge badge-danger" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-exclamation-circle"></i> Failed</span>
                          @else
                              <span class="badge badge-secondary" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-clock-o"></i> Pending</span>
                          @endif
                      </td>
                      <td class="text-center" style="white-space: nowrap;">
                        <a href="{{ route('admin.effects.edit', $effect->id) }}" class="btn btn-icon waves-effect waves-light btn-success btn-xs m-r-5" data-toggle="tooltip" title="Edit"> <i class="fa fa-edit"></i> </a>
                        <form action="{{ route('admin.effects.destroy', $effect->id) }}" method="POST" id="delete-form-{{ $effect->id }}" style="display:inline-block;">
                          @csrf
                          @method('DELETE')
                          <button type="button" class="btn btn-icon waves-effect waves-light btn-danger btn-xs" onclick="confirmDeleteEffect({{ $effect->id }})" data-toggle="tooltip" title="Remove"> <i class="fa fa-remove"></i> </button>
                        </form>
                      </td>
                    </tr>
                   @endforeach
                  </tbody>
                </table>
              </div>
                <nav class="paging_simple_numbers">
                  @include('admin.pagination', ['paginator' => $effects])
                </nav>

              </div>
            </div>
          </div>
        </div>
      </div>
      @include("admin.copyright")
    </div>
    <!-- Video Preview Modal -->
    <div id="videoPreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="videoPreviewModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Processed Effect Preview</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closePreview()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-4">
                    <video id="previewPlayer" controls style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <source src="" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyEffectUrl(elementId) {
            var copyText = document.getElementById(elementId);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Copied!',
                    text: 'URL copied to clipboard.',
                    showConfirmButton: false,
                    timer: 2000,
                    background: '#1a2234',
                    color: '#fff'
                });
            } else {
                alert("URL copied to clipboard!");
            }
        }

        function confirmDeleteEffect(id) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '{{ trans("words.dlt_warning") }}',
                    text: '{{ trans("words.dlt_warning_text") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ trans("words.dlt_confirm") }}',
                    cancelButtonText: '{{ trans("words.btn_cancel") }}',
                    background: '#1a2234',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            } else {
                if (confirm("Are you sure you want to delete this effect?")) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        }

        function showPreview(url) {
            var player = document.getElementById('previewPlayer');
            player.src = url;
            $('#videoPreviewModal').modal('show');
            player.play().catch(function(e) { console.warn("Auto-play prevented", e); });
        }

        function closePreview() {
            var player = document.getElementById('previewPlayer');
            player.pause();
            player.src = '';
        }

        // Auto-poll effect processing status via AJAX every 3 seconds
        $(document).ready(function() {
            var checkInterval = setInterval(function() {
                var pendingIds = [];
                $('tr[data-effect-id]').each(function() {
                    var status = $(this).attr('data-status');
                    if (status === 'pending' || status === 'processing') {
                        pendingIds.push($(this).attr('data-effect-id'));
                    }
                });

                if (pendingIds.length === 0) {
                    clearInterval(checkInterval);
                    return;
                }

                $.ajax({
                    url: '{{ route("admin.effects.check-status") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: pendingIds
                    },
                    success: function(data) {
                        $.each(data, function(id, item) {
                            var tr = $('#card_box_id_' + id);
                            if (tr.length) {
                                tr.attr('data-status', item.status);
                                var statusCell = tr.find('.status-cell');
                                
                                if (item.status === 'ready') {
                                    var html = '<span class="badge badge-success" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-check-circle"></i> Ready</span>';
                                    if (item.converted_mb) {
                                        html += '<br><small style="color: #aaa;">' + item.converted_mb + '</small>';
                                    }
                                    statusCell.html(html);

                                    // Auto-inject Preview button into URL input group append if not present
                                    var appendGroup = tr.find('.input-group-append');
                                    if (item.processed_url && appendGroup.find('.btn-preview-processed').length === 0) {
                                        var previewBtn = '<button class="btn btn-sm btn-info btn-preview-processed" type="button" onclick="showPreview(\'' + item.processed_url + '\')" data-toggle="tooltip" title="Preview Processed Video"><i class="fa fa-play-circle"></i> Preview</button>';
                                        appendGroup.prepend(previewBtn);
                                    }
                                } else if (item.status === 'error') {
                                    statusCell.html('<span class="badge badge-danger" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-exclamation-circle"></i> Failed</span>');
                                } else if (item.status === 'processing') {
                                    statusCell.html('<span class="badge badge-warning" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-spin fa-spinner"></i> Processing...</span>');
                                }
                            }
                        });
                    }
                });
            }, 3000);
        });

        // Also stop video if modal is closed by clicking outside
        $('#videoPreviewModal').on('hidden.bs.modal', function () {
            closePreview();
        });
    </script>
@endsection
