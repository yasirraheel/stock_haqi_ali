@extends("admin.admin_app")

@section("content")

  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                <h4 class="m-t-0 header-title"><b>Google Drive Scanned Files</b></h4>
                <p class="text-muted font-13 m-b-20">
                    Scan any Google Drive Folder by ID or URL to sync all its files into the database. Duplicates are automatically prevented.
                </p>

                <!-- Scan Folder Form -->
                <div class="card card-body bg-light m-b-20" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 5px; padding: 20px;">
                    {!! Form::open(array('route' => 'admin.drive-files.scan','class'=>'form-inline','role'=>'form','method'=>'post')) !!}
                        <div class="form-group col-md-8 pl-0">
                            <label for="folder_input" class="sr-only">Folder ID or URL</label>
                            <input type="text" name="folder_input" id="folder_input" class="form-control w-100" placeholder="Paste Google Drive Folder URL or Folder ID (e.g. 1Q7N29v4hu63jsk0_5GhjuMpOykBw6akf)..." value="{{ request('folder_id', '1Q7N29v4hu63jsk0_5GhjuMpOykBw6akf') }}" required style="width: 100%;">
                        </div>
                        <div class="form-group col-md-4 pr-0">
                            <button type="submit" class="btn btn-primary waves-effect waves-light w-100"><i class="fa fa-refresh"></i> Scan & Sync Folder Files</button>
                        </div>
                    {!! Form::close() !!}
                </div>

                @if(Session::has('flash_message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                        <i class="fa fa-check-circle m-r-5"></i> {{ Session::get('flash_message') }}
                    </div>
                @endif

                @if(Session::has('error_message'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                        <i class="fa fa-exclamation-triangle m-r-5"></i> {{ Session::get('error_message') }}
                    </div>
                @endif

                <!-- Search and Stats Bar -->
                <div class="row m-b-20">
                  <div class="col-md-6">
                     {!! Form::open(array('route' => 'admin.drive-files.index','class'=>'app-search','id'=>'search','role'=>'form','method'=>'get')) !!}
                      <input type="text" name="s" placeholder="Search scanned files by name, file ID, or folder ID..." value="{{ request('s') }}" class="form-control">
                      <button type="submit"><i class="fa fa-search"></i></button>
                     {!! Form::close() !!}
                  </div>
                  <div class="col-md-6 text-right">
                      <span class="badge badge-info font-14 p-2 m-r-5"><i class="fa fa-file"></i> Total Synced Files: {{ $totalFiles }}</span>
                      <span class="badge badge-secondary font-14 p-2"><i class="fa fa-folder"></i> Total Folders: {{ $foldersCount }}</span>
                  </div>
                </div>

                <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th width="5%">#</th>
                      <th>File Name</th>
                      <th>File Size</th>
                      <th>File ID</th>
                      <th>Folder ID</th>
                      <th>Direct Download URL</th>
                      <th>Status</th>
                      <th width="10%" class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  @forelse($files as $i => $file)
                    <tr>
                      <td>{{ $files->firstItem() + $i }}</td>
                      <td>
                        <strong>{{ $file->name }}</strong>
                        @if($file->mime_type)
                            <br><small class="text-muted">{{ $file->mime_type }}</small>
                        @endif
                      </td>
                      <td><span class="badge badge-light">{{ $file->formatted_size }}</span></td>
                      <td><code>{{ $file->file_id }}</code></td>
                      <td><code>{{ $file->folder_id }}</code></td>
                      <td>
                          <div class="input-group input-group-sm" style="min-width: 250px;">
                              <input type="text" class="form-control form-control-sm" id="url_{{ $file->id }}" value="{{ $file->url }}" readonly>
                              <div class="input-group-append">
                                  <button class="btn btn-sm btn-outline-secondary" type="button" onclick="copyUrl('url_{{ $file->id }}')"><i class="fa fa-copy"></i> Copy</button>
                                  <a href="{{ $file->url }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa fa-external-link"></i> Open</a>
                              </div>
                          </div>
                      </td>
                      <td>
                          <span class="badge badge-success">{{ ucfirst($file->status) }}</span>
                      </td>
                      <td class="text-center">
                          {!! Form::open(['route' => ['admin.drive-files.destroy', $file->id], 'method' => 'DELETE', 'style'=>'display:inline-block', 'onclick' => 'return confirm("Are you sure you want to remove this file record?")']) !!}
                              <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger btn-xs" data-toggle="tooltip" title="Delete Record"><i class="fa fa-trash"></i></button>
                          {!! Form::close() !!}
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="8" class="text-center p-4">
                          <p class="text-muted m-0">No scanned files found. Enter a Google Drive Folder ID/URL above and click "Scan & Sync Folder Files".</p>
                      </td>
                    </tr>
                  @endforelse
                  </tbody>
                </table>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        {!! $files->appends(request()->except('page'))->render() !!}
                    </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
  </div>

  <script>
      function copyUrl(elementId) {
          var copyText = document.getElementById(elementId);
          copyText.select();
          copyText.setSelectionRange(0, 99999);
          document.execCommand("copy");
          alert("Direct URL copied to clipboard!");
      }
  </script>

@endsection
