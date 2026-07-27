@extends("admin.admin_app")

@section("content")

  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                <div class="row m-b-10">
                    <div class="col-md-8">
                        <h4 class="m-t-0 header-title"><b>Google Drive Scanned Files</b></h4>
                        <p class="text-muted font-13 m-b-15">
                            Scan any Google Drive Folder by ID or URL to sync all its files into the database. Duplicates are automatically prevented.
                        </p>
                    </div>
                    <div class="col-md-4 text-right">
                        <span class="badge badge-info font-13 p-2 m-r-5"><i class="fa fa-file"></i> Synced Files: {{ number_format($totalFiles) }}</span>
                        <span class="badge badge-secondary font-13 p-2"><i class="fa fa-folder"></i> Folders: {{ number_format($foldersCount) }}</span>
                    </div>
                </div>

                <!-- Scan Folder Form -->
                <div class="p-3 m-b-20" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px;">
                    {!! Form::open(array('route' => 'admin.drive-files.scan','class'=>'form-inline','role'=>'form','method'=>'post')) !!}
                        <div class="input-group w-100">
                            <input type="text" name="folder_input" id="folder_input" class="form-control" placeholder="Paste Google Drive Folder URL or Folder ID (e.g. 1Q7N29v4hu63jsk0_5GhjuMpOykBw6akf)..." value="{{ request('folder_id', '1Q7N29v4hu63jsk0_5GhjuMpOykBw6akf') }}" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-danger waves-effect waves-light"><i class="fa fa-refresh"></i> Scan & Sync Folder Files</button>
                            </div>
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

                <!-- Search Bar -->
                <div class="row m-b-20">
                  <div class="col-md-6">
                     {!! Form::open(array('route' => 'admin.drive-files.index','class'=>'app-search','id'=>'search','role'=>'form','method'=>'get')) !!}
                      <input type="text" name="s" placeholder="Search scanned files by name, file ID, or folder ID..." value="{{ request('s') }}" class="form-control">
                      <button type="submit"><i class="fa fa-search"></i></button>
                     {!! Form::close() !!}
                  </div>
                </div>

                <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="4%">#</th>
                      <th>File Name</th>
                      <th>File Size</th>
                      <th>File ID</th>
                      <th>Folder ID</th>
                      <th>Direct Download URL</th>
                      <th>Status</th>
                      <th width="8%" class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  @forelse($files as $i => $file)
                    <tr id="card_box_id_{{ $file->id }}">
                      <td>{{ $files->firstItem() + $i }}</td>
                      <td>
                        <strong>{{ $file->name }}</strong>
                        @if($file->mime_type)
                            <div class="text-muted font-11 mt-1">{{ $file->mime_type }}</div>
                        @endif
                      </td>
                      <td><span class="badge badge-info">{{ $file->formatted_size }}</span></td>
                      <td><code style="color: #ff7043;">{{ $file->file_id }}</code></td>
                      <td><code style="color: #ff7043;">{{ $file->folder_id }}</code></td>
                      <td>
                          <div class="input-group input-group-sm" style="min-width: 280px;">
                              <input type="text" class="form-control form-control-sm" id="url_{{ $file->id }}" value="{{ $file->url }}" readonly style="background: rgba(0,0,0,0.2);">
                              <div class="input-group-append">
                                  <button class="btn btn-sm btn-secondary" type="button" onclick="copyUrl('url_{{ $file->id }}')"><i class="fa fa-copy"></i> Copy</button>
                                  <a href="{{ $file->url }}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-external-link"></i> Open</a>
                              </div>
                          </div>
                      </td>
                      <td>
                          <span class="badge badge-success">{{ ucfirst($file->status) }}</span>
                      </td>
                      <td class="text-center">
                          {!! Form::open(['route' => ['admin.drive-files.destroy', $file->id], 'method' => 'DELETE', 'style'=>'display:inline-block', 'onclick' => 'return confirm("Are you sure you want to remove this file record?")']) !!}
                              <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger btn-xs" data-toggle="tooltip" title="Delete Record"><i class="fa fa-remove"></i></button>
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

                <nav class="paging_simple_numbers">
                    @include('admin.pagination', ['paginator' => $files])
                </nav>

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
