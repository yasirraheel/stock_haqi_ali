@extends("admin.admin_app")

@section("content")


  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                <div class="row">
                  <div class="col-md-3">
                     <a href="{{ route('admin.audio.create') }}" class="btn btn-success btn-md waves-effect waves-light m-b-20" data-toggle="tooltip" title="Add Audio"><i class="fa fa-plus"></i> Add Audio</a>
                  </div>
                  <div class="col-md-3">
                     {!! Form::open(array('url' => 'admin/audio','class'=>'app-search','id'=>'search','role'=>'form','method'=>'get')) !!}
                      <input type="text" name="s" placeholder="Search by title..." class="form-control">
                      <button type="submit"><i class="fa fa-search"></i></button>
                    {!! Form::close() !!}
                  </div>
                <div class="col-md-3">
                  <a href="#" class="btn btn-info btn-md waves-effect waves-light m-b-20 mt-2 pull-right" data-toggle="tooltip" title="Export Audio"><i class="fa fa-file-excel-o"></i> Export Audio</a>
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
                      <th class="text-center" style="width: 120px;">Preview</th>
                      <th>Duration</th>
                      <th>Format</th>
                      <th>License Price</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                   @foreach($audios as $i => $audio)
                    <tr id="card_box_id_{{$audio->id}}">
                      <td style="max-width: 220px;">
                        <strong style="color: #fff;">{{ $audio->title }}</strong>
                        @if($audio->genre)
                            <small class="text-muted d-block">{{ $audio->genre }}</small>
                        @endif
                      </td>
                      <td class="text-center">
                        @if ($audio->audio_url || $audio->drive_file_id)
                            <button type="button" class="btn btn-sm btn-danger waves-effect waves-light"
                                onclick="openAudioPreview(this)"
                                data-preview-title="{{ e($audio->title) }}"
                                data-preview-url="{{ e($audio->audio_url ?? '') }}"
                                data-preview-drive-id="{{ e($audio->drive_file_id ?? '') }}"
                                data-toggle="tooltip" title="Play audio preview here">
                                <i class="fa fa-play-circle"></i> Preview
                            </button>
                        @else
                            <span class="text-muted"><i class="fa fa-ban"></i> No Audio</span>
                        @endif
                        @if ($audio->audio_url || $audio->drive_file_id)
                            <div class="stock-inline-preview mt-2 text-left" style="display:none; min-width:260px; max-width:360px; margin-left:auto; margin-right:auto;">
                                <div class="stock-preview-loading text-info small p-2" style="display:none; background:#121824; border:1px solid #32383e; border-radius:6px;">
                                    <i class="fa fa-spinner fa-spin mr-1"></i> Loading preview…
                                </div>
                                <div class="stock-preview-error text-danger small p-2" style="display:none; background:#121824; border:1px solid #5d2830; border-radius:6px;"></div>
                                <audio class="stock-inline-audio" controls preload="none" style="display:none; width:100%; outline:none;"></audio>
                                <iframe class="stock-inline-drive" title="Audio preview" src="about:blank" allow="autoplay" style="display:none; width:100%; height:90px; border:0; border-radius:6px; background:#121824;"></iframe>
                            </div>
                        @endif
                      </td>
                      <td>{{ $audio->duration ?? 'N/A' }}</td>
                      <td>{{ $audio->format ?? 'N/A' }}</td>
                      <td>
                        @if($audio->license_price && $audio->license_price > 0)
                            <span class="badge badge-warning">${{ number_format($audio->license_price, 2) }}</span>
                        @else
                            <span class="badge badge-success">Free</span>
                        @endif
                      </td>
                      <td>@if($audio->is_active)<span class="badge badge-success">Active</span> @else<span class="badge badge-danger">Inactive</span>@endif</td>

                      <td>
                      <a href="{{ route('admin.audio.show', $audio->id) }}" class="btn btn-icon waves-effect waves-light btn-primary m-b-5 m-r-5" data-toggle="tooltip" title="View"> <i class="fa fa-eye"></i> </a>
                      <a href="{{ route('admin.audio.edit', $audio->id) }}" class="btn btn-icon waves-effect waves-light btn-success m-b-5 m-r-5" data-toggle="tooltip" title="Edit"> <i class="fa fa-edit"></i> </a>
                      <a href="#" class="btn btn-icon waves-effect waves-light btn-danger m-b-5 data_remove" data-toggle="tooltip" title="Remove" data-id="{{$audio->id}}"> <i class="fa fa-remove"></i> </a>
                      </td>
                    </tr>
                   @endforeach


                  </tbody>
                </table>
              </div>
                <nav class="paging_simple_numbers">
                @include('admin.pagination', ['paginator' => $audios])
                </nav>

              </div>
            </div>
          </div>
        </div>
      </div>
      @include("admin.copyright")
    </div>

    <script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

 <script type="text/javascript">
  function resetAudioPreview(container) {
    if (!container) return;
    var audio = container.querySelector('.stock-inline-audio');
    var iframe = container.querySelector('.stock-inline-drive');
    if (audio) {
        audio.pause();
        audio.removeAttribute('src');
        audio.load();
        audio.style.display = 'none';
    }
    if (iframe) {
        iframe.src = 'about:blank';
        iframe.style.display = 'none';
    }
    container.querySelector('.stock-preview-loading').style.display = 'none';
    container.querySelector('.stock-preview-error').style.display = 'none';
  }

  function closeAllAudioPreviews(except) {
    document.querySelectorAll('.stock-inline-preview').forEach(function (container) {
        if (container !== except) {
            resetAudioPreview(container);
            container.style.display = 'none';
        }
    });
  }

  function openAudioPreview(button) {
    var container = button.parentElement.querySelector('.stock-inline-preview');
    if (!container) return;

    if (container.style.display !== 'none') {
        resetAudioPreview(container);
        container.style.display = 'none';
        button.classList.remove('active');
        return;
    }

    closeAllAudioPreviews(container);
    var audioUrl = button.dataset.previewUrl || '';
    var driveFileId = button.dataset.previewDriveId || '';
    var audio = container.querySelector('.stock-inline-audio');
    var iframe = container.querySelector('.stock-inline-drive');
    var loading = container.querySelector('.stock-preview-loading');
    var error = container.querySelector('.stock-preview-error');

    container.style.display = 'block';
    button.classList.add('active');
    loading.style.display = 'block';

    if (driveFileId) {
        iframe.onload = function () {
            loading.style.display = 'none';
        };
        iframe.onerror = function () {
            loading.style.display = 'none';
            error.textContent = 'Preview could not be loaded.';
            error.style.display = 'block';
        };
        iframe.src = 'https://drive.google.com/file/d/' + encodeURIComponent(driveFileId) + '/preview';
        iframe.style.display = 'block';
    } else if (audioUrl) {
        audio.onloadeddata = function () {
            loading.style.display = 'none';
            audio.style.display = 'block';
        };
        audio.oncanplay = function () {
            loading.style.display = 'none';
            audio.style.display = 'block';
        };
        audio.onerror = function () {
            loading.style.display = 'none';
            error.textContent = 'Audio preview could not be loaded.';
            error.style.display = 'block';
        };
        audio.src = audioUrl;
        audio.load();
        audio.play().catch(function () {});
    } else {
        loading.style.display = 'none';
        error.textContent = 'No preview source is available.';
        error.style.display = 'block';
    }
  }

  // Pause other audios when any audio starts playing
  document.addEventListener('play', function (e) {
    var audios = document.getElementsByTagName('audio');
    for (var i = 0; i < audios.length; i++) {
        if (audios[i] !== e.target) {
            audios[i].pause();
        }
    }
  }, true);

  $(".data_remove").click(function () {

    var post_id = $(this).data("id");
    var action_name='audio_delete';

    Swal.fire({
      title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: "Cancel",
    background:"#1a2234",
    color:"#fff"

  }).then((result) => {
      if(result.isConfirmed) {
          $.ajax({
              type: 'post',
              url: "{{ URL::to('admin/ajax_delete') }}",
              dataType: 'json',
              data: {"_token": "{{ csrf_token() }}",id: post_id, action_for: action_name},
              success: function(res) {
                if(res.status=='1')
                {
                    var selector = "#card_box_id_"+post_id;
                      $(selector ).fadeOut(1000);
                      setTimeout(function(){
                              $(selector ).remove()
                          }, 1000);

                    Swal.fire({
                      position: 'center',
                      icon: 'success',
                      title: 'Deleted!',
                      showConfirmButton: true,
                      confirmButtonColor: '#10c469',
                      background:"#1a2234",
                      color:"#fff"
                    })
                }
                else
                {
                  Swal.fire({
                          position: 'center',
                          icon: 'error',
                          title: 'Something went wrong!',
                          showConfirmButton: true,
                          confirmButtonColor: '#10c469',
                          background:"#1a2234",
                          color:"#fff"
                         })
                }
              }
          });
      }
  })
  });
 </script>
@endsection
