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
                                onclick="openAudioPreview('{{ addslashes($audio->title) }}', '{{ $audio->audio_url }}', '{{ $audio->drive_file_id ?? '' }}')"
                                data-toggle="tooltip" title="Open Audio Preview Popup">
                                <i class="fa fa-play-circle"></i> Preview
                            </button>
                        @else
                            <span class="text-muted"><i class="fa fa-ban"></i> No Audio</span>
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

    <!-- Audio Preview Popup Modal -->
    <div id="audioPreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
            <div class="modal-content" style="background: #1a2234; border: 1px solid #32383e; color: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <div class="modal-header" style="border-bottom: 1px solid #2a3447; padding: 14px 20px; background: #151c2b;">
                    <h5 class="modal-title mt-0" id="audioPreviewModalTitle" style="font-size: 15px; font-weight: 600;">
                        <i class="fa fa-music text-info mr-1"></i> Audio Track Preview
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="closeAudioPreview()" style="outline: none; opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 text-center" style="background: #121824;">
                    <div class="m-b-20">
                        <div style="width: 70px; height: 70px; margin: 0 auto; background: linear-gradient(135deg, #10c469, #35b8e0); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(16, 196, 105, 0.3);">
                            <i class="fa fa-headphones" style="font-size: 32px; color: #fff;"></i>
                        </div>
                        <h4 id="audioModalTrackTitle" class="text-white mt-3 mb-2" style="font-size: 15px; font-weight: 600; word-break: break-word; overflow-wrap: anywhere; white-space: normal; line-height: 1.4; padding: 0 10px;">Track Title</h4>
                        <p class="text-muted mb-0" style="font-size: 13px;"><i class="fa fa-play-circle-o"></i> Audio Playback & Preview</p>
                    </div>

                    <!-- Single Player Container (Only ONE player is shown, never dual) -->
                    <div id="audioPlayerWrapper" class="p-2" style="background: #1e2838; border-radius: 8px; border: 1px solid #32383e; display: none;">
                        <audio id="modalAudioPlayer" controls style="width: 100%; outline: none;"></audio>
                    </div>

                    <div id="audioIframeWrapper" style="display: none;">
                        <iframe id="modalAudioIframe" src="" style="width: 100%; height: 110px; border: 0; border-radius: 8px; background: #1e2838;" allow="autoplay"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

 <script type="text/javascript">
  function openAudioPreview(title, audioUrl, driveFileId) {
    document.getElementById('audioModalTrackTitle').innerText = title;
    var audioPlayer = document.getElementById('modalAudioPlayer');
    var audioPlayerWrapper = document.getElementById('audioPlayerWrapper');
    var iframeWrapper = document.getElementById('audioIframeWrapper');
    var iframe = document.getElementById('modalAudioIframe');

    // Immediately stop & clear any previous playback
    if (audioPlayer) {
        audioPlayer.pause();
        audioPlayer.currentTime = 0;
        audioPlayer.src = '';
    }
    if (iframe) {
        iframe.src = '';
    }

    if (driveFileId && driveFileId !== '') {
        // Show ONLY the GDrive player
        audioPlayerWrapper.style.display = 'none';
        iframe.src = 'https://drive.google.com/file/d/' + driveFileId + '/preview';
        iframeWrapper.style.display = 'block';
    } else if (audioUrl && audioUrl !== '') {
        // Show ONLY the HTML5 player
        iframeWrapper.style.display = 'none';
        audioPlayer.src = audioUrl;
        audioPlayerWrapper.style.display = 'block';
        audioPlayer.play().catch(function() {});
    }

    $('#audioPreviewModal').modal('show');
  }

  function closeAudioPreview() {
    var audioPlayer = document.getElementById('modalAudioPlayer');
    var iframe = document.getElementById('modalAudioIframe');
    if (audioPlayer) {
        audioPlayer.pause();
        audioPlayer.currentTime = 0;
        audioPlayer.src = '';
    }
    if (iframe) {
        iframe.src = '';
    }
  }

  $('#audioPreviewModal').on('hidden.bs.modal', function () {
    closeAudioPreview();
  });

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
