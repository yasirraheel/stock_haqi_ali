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
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                   @foreach($effects as $effect)
                    <tr id="card_box_id_{{$effect->id}}">
                      <td><strong>{{ $effect->title }}</strong></td>
                      <td><span class="badge badge-info">{{ $effect->category ?? 'General' }}</span></td>
                      <td>
                        <a href="{{ $effect->effect_url }}" target="_blank" class="text-primary truncate" style="max-width: 250px; display: inline-block;">
                          {{ $effect->effect_url }}
                        </a>
                      </td>
                      <td>
                        @if($effect->is_premium || ($effect->license_price && $effect->license_price > 0))
                            <span class="badge badge-warning"><i class="fa fa-star"></i> Premium (${{ number_format($effect->license_price, 2) }})</span>
                        @else
                            <span class="badge badge-success">Free</span>
                        @endif
                      </td>
                      <td>@if($effect->is_active)<span class="badge badge-success">Active</span> @else<span class="badge badge-danger">Inactive</span>@endif</td>

                      <td>
                        <a href="{{ route('admin.effects.edit', $effect->id) }}" class="btn btn-icon waves-effect waves-light btn-success m-b-5 m-r-5" data-toggle="tooltip" title="Edit"> <i class="fa fa-edit"></i> </a>
                        <form action="{{ route('admin.effects.destroy', $effect->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this effect?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger m-b-5" data-toggle="tooltip" title="Remove"> <i class="fa fa-remove"></i> </button>
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
@endsection
