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
                      <td>{{ $audio->title }}</td>
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

 <script type="text/javascript">

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

   //alert(post_id);

   //alert(JSON.stringify(result));

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
