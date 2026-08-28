@extends("admin.admin_app")

@section("content")

  <style>
    /* High-contrast Stat Cards (distinct from background) */
    .stat-box-card {
      background-color: #1a2234 !important;
      border: 1px solid #2e3c54 !important;
      border-radius: 8px;
      padding: 16px 12px;
      text-align: center;
      transition: all 0.2s ease;
      display: block;
      text-decoration: none !important;
      box-shadow: 0 4px 10px rgba(0,0,0,0.35);
    }
    .stat-box-card:hover {
      transform: translateY(-3px);
      border-color: #4f6485 !important;
      box-shadow: 0 8px 18px rgba(0,0,0,0.5);
    }
    .stat-box-card.active-card {
      border: 2px solid #3b82f6 !important;
      background-color: #202c44 !important;
    }
    .stat-box-card h2 {
      margin: 0 0 4px 0;
      font-weight: 700;
      font-size: 26px;
    }
    .stat-box-card h5 {
      margin: 0;
      font-size: 13px;
      color: #94a3b8;
      font-weight: 500;
    }

    /* Table dark styling & hover fix (ELIMINATES white wash on hover) */
    .table-effects-custom {
      background-color: transparent !important;
      margin-bottom: 0;
    }
    .table-effects-custom th {
      background-color: #111726 !important;
      color: #94a3b8 !important;
      border: 1px solid #2a374f !important;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 12px 10px;
    }
    .table-effects-custom td {
      border: 1px solid #232f44 !important;
      vertical-align: middle !important;
      padding: 10px;
      background-color: #182030;
      color: #cbd5e1;
    }
    .table-effects-custom tbody tr:nth-of-type(even) td {
      background-color: #141b29;
    }
    /* Sleek Navy-Slate hover - completely overrides Bootstrap default white */
    .table-effects-custom tbody tr:hover td {
      background-color: #253550 !important;
      color: #ffffff !important;
    }

    /* Active converting row highlight */
    .row-processing-active td {
      background-color: rgba(245, 158, 11, 0.12) !important;
    }
    .row-processing-active td:first-child {
      border-left: 4px solid #f59e0b !important;
    }
    .row-processing-active strong {
      color: #ffffff !important;
      font-weight: 700 !important;
    }
    .row-processing-active:hover td {
      background-color: rgba(245, 158, 11, 0.2) !important;
    }

    /* Active converting badge (High Contrast: Bold Black on Vibrant Amber - NO MERGING) */
    .badge-converting-active {
      background-color: #f59e0b !important;
      color: #000000 !important;
      font-weight: 800 !important;
      padding: 6px 12px !important;
      font-size: 11px !important;
      border-radius: 4px !important;
      box-shadow: 0 0 10px rgba(245, 158, 11, 0.4) !important;
      display: inline-block !important;
      letter-spacing: 0.3px !important;
    }
    .badge-converting-active i {
      color: #000000 !important;
    }

    /* Filter tab buttons */
    .filter-btn-custom {
      background-color: #1a2234;
      color: #94a3b8;
      border: 1px solid #2e3c54;
      font-size: 12px;
      padding: 6px 13px;
      font-weight: 600;
      transition: all 0.15s ease;
    }
    .filter-btn-custom:hover {
      background-color: #26334d;
      color: #ffffff;
      border-color: #4a5c7c;
    }
    .filter-btn-custom.active {
      background-color: #2563eb !important;
      border-color: #2563eb !important;
      color: #ffffff !important;
    }
    .filter-badge {
      background: rgba(0, 0, 0, 0.35);
      color: #ffffff;
      padding: 3px 7px;
      border-radius: 10px;
      font-size: 10px;
      margin-left: 5px;
    }
  </style>

  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">

              <!-- Top Stat Dashboard Widgets (Like Google Drive Scanned Files view) -->
              <div class="row mb-3">
                <div class="col-xl-2 col-md-4 col-sm-6 mb-2">
                  <a href="{{ route('admin.effects.index', ['status' => 'all', 'sort' => $sort, 's' => request('s')]) }}" class="stat-box-card {{ $filter === 'all' ? 'active-card' : '' }}">
                    <h2 class="text-white" id="stat_total_count">{{ number_format($statusCounts->total ?? 0) }}</h2>
                    <h5><i class="fa fa-th-list text-muted mr-1"></i> Total Effects</h5>
                  </a>
                </div>

                <div class="col-xl-2 col-md-4 col-sm-6 mb-2">
                  <a href="{{ route('admin.effects.index', ['status' => 'ready', 'sort' => $sort, 's' => request('s')]) }}" class="stat-box-card {{ $filter === 'ready' ? 'active-card' : '' }}">
                    <h2 class="text-success" id="stat_ready_count">{{ number_format($statusCounts->ready ?? 0) }}</h2>
                    <h5><i class="fa fa-check-circle text-success mr-1"></i> Ready (MP4)</h5>
                  </a>
                </div>

                <div class="col-xl-3 col-md-4 col-sm-6 mb-2">
                  <a href="{{ route('admin.effects.index', ['status' => 'processing', 'sort' => $sort, 's' => request('s')]) }}" class="stat-box-card {{ $filter === 'processing' ? 'active-card' : '' }}">
                    <h2 class="text-warning" id="stat_processing_count">{{ number_format($statusCounts->processing ?? 0) }}</h2>
                    <h5><i class="fa fa-spinner fa-spin text-warning mr-1"></i> In Processing Now</h5>
                  </a>
                </div>

                <div class="col-xl-2 col-md-4 col-sm-6 mb-2">
                  <a href="{{ route('admin.effects.index', ['status' => 'pending', 'sort' => $sort, 's' => request('s')]) }}" class="stat-box-card {{ $filter === 'pending' ? 'active-card' : '' }}">
                    <h2 class="text-info" id="stat_pending_count">{{ number_format($statusCounts->pending ?? 0) }}</h2>
                    <h5><i class="fa fa-clock-o text-info mr-1"></i> Pending in Queue</h5>
                  </a>
                </div>

                <div class="col-xl-3 col-md-4 col-sm-6 mb-2">
                  <a href="{{ route('admin.effects.index', ['status' => 'failed', 'sort' => $sort, 's' => request('s')]) }}" class="stat-box-card {{ $filter === 'failed' ? 'active-card' : '' }}">
                    <h2 class="text-danger" id="stat_failed_count">{{ number_format($statusCounts->failed ?? 0) }}</h2>
                    <h5><i class="fa fa-exclamation-triangle text-danger mr-1"></i> Not Processing (Failed)</h5>
                  </a>
                </div>
              </div>

              <!-- Main Card Box -->
              <div class="card-box table-responsive" style="border: 1px solid #2e3c54; border-radius: 8px;">

                <!-- Active Processing Banner -->
                <div id="live_processing_banner" class="d-flex justify-content-between align-items-center mb-3 py-2 px-3" 
                     style="background: {{ $activeEffect ? '#2b2314' : '#172030' }}; border: 1px solid {{ $activeEffect ? '#d97706' : '#2e3c54' }}; border-radius: 6px;">
                  <div id="live_banner_content" class="d-flex align-items-center flex-wrap">
                    @if($activeEffect)
                      <i class="fa fa-spinner fa-spin mr-2" style="color: #f59e0b; font-size: 14px;"></i>
                      <strong class="mr-2" style="color: #f59e0b; font-weight: 800; font-size: 13px;">Currently Processing:</strong> 
                      <span id="active_effect_title" style="color: #ffffff; font-weight: 700; font-size: 13px;" class="mr-2">#{{ $activeEffect->id }} - {{ $activeEffect->title }}</span>
                      <span id="active_effect_step" class="badge-converting-active">{{ $activeEffect->process_step ?: 'Converting MP4...' }}</span>
                    @else
                      <i class="fa fa-check-circle text-success mr-2"></i>
                      <span id="active_effect_title" style="color: #94a3b8; font-size: 13px;">All background jobs completed or queue is idle.</span>
                    @endif
                  </div>
                  <div>
                    <span class="text-muted font-11"><i class="fa fa-refresh fa-spin mr-1"></i> Auto-Syncing Live</span>
                  </div>
                </div>

                <!-- Background Processing Cron Info -->
                <div class="p-2 mb-3 d-flex flex-wrap align-items-center justify-content-between" style="background: #141b29; border: 1px solid #28354c; border-radius: 6px;">
                    <small class="text-muted mr-2">
                      <i class="fa fa-info-circle text-info"></i> <strong>Background Cron Job (Once Per Minute):</strong>
                    </small>
                    <div class="input-group input-group-sm" style="max-width: 620px;">
                        <input type="text" class="form-control form-control-sm" id="cronCommand" value="/usr/bin/php /home/u273790872/domains/cineworm.org/public_html/stock/artisan schedule:run >> /dev/null 2>&1" readonly style="background: #0d121c; color: #a5b4fc; border-color: #28354c;">
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-sm" type="button" onclick="copyCron()">Copy</button>
                        </div>
                    </div>
                </div>

                <!-- Action Controls: Buttons & Search -->
                <div class="row align-items-center mb-3">
                  <div class="col-xl-6 col-lg-12 mb-2 mb-xl-0">
                     <a href="{{ route('admin.effects.create') }}" class="btn btn-success btn-sm waves-effect waves-light mr-1" data-toggle="tooltip" title="Add Effect"><i class="fa fa-plus"></i> Add New Effect</a>
                     <form action="{{ route('admin.effects.retry-failed') }}" method="POST" id="retry-failed-form" style="display:inline-block;">
                       @csrf
                       <button type="button" onclick="confirmRetryFailed()" class="btn btn-warning btn-sm waves-effect waves-light mr-1" data-toggle="tooltip" title="Re-queue only failed effects (spaced 10s apart, max 30 per batch to prevent Google Drive rate limit)">
                         <i class="fa fa-refresh"></i> Retry Failed Only (Batch of 30)
                       </button>
                     </form>
                     <form action="{{ route('admin.effects.cleanup-invalid') }}" method="POST" id="cleanup-invalid-form" style="display:inline-block;">
                       @csrf
                       <button type="button" onclick="confirmCleanupInvalid()" class="btn btn-outline-danger btn-sm waves-effect waves-light mr-1" data-toggle="tooltip" title="Remove invalid items (Google Drive Folders, 0-byte files, non-videos) from database">
                         <i class="fa fa-trash-o"></i> Clean Up Invalid / Folders
                       </button>
                     </form>
                  </div>
                  <div class="col-xl-6 col-lg-12 text-xl-right">
                     {!! Form::open(array('url' => 'admin/effects','class'=>'form-inline justify-content-xl-end justify-content-start','id'=>'search','role'=>'form','method'=>'get')) !!}
                      <input type="hidden" name="status" value="{{ $filter }}">
                      <input type="hidden" name="sort" value="{{ $sort }}">

                      <!-- Category Dropdown Filter -->
                      <select name="category" class="form-control form-control-sm mr-2" style="background: #141b29; color: #cbd5e1; border-color: #2e3c54; max-width: 170px;" onchange="this.form.submit()">
                        <option value="all">All Categories</option>
                        @foreach($allCategories as $cat)
                          <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                      </select>

                      <div class="input-group input-group-sm">
                        <input type="text" name="s" placeholder="Search title..." value="{{ request('s', '') }}" class="form-control form-control-sm" style="min-width: 180px; background: #141b29; color: #fff; border-color: #2e3c54;">
                        <div class="input-group-append">
                          <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Search</button>
                          @if(request('s') || ($selectedCategory && $selectedCategory !== 'all'))
                            <a href="{{ route('admin.effects.index', ['status' => $filter, 'sort' => $sort]) }}" class="btn btn-secondary btn-sm" title="Clear Filters"><i class="fa fa-times"></i></a>
                          @endif
                        </div>
                      </div>
                     {!! Form::close() !!}
                  </div>
                </div>

                <!-- Status Filter Tabs & Processing Order Bar -->
                <div class="p-2 mb-3 d-flex flex-wrap justify-content-between align-items-center" style="background-color: #141b29; border: 1px solid #2e3c54; border-radius: 6px; gap: 8px;">
                  
                  <!-- Filter Buttons -->
                  <div class="btn-group flex-wrap">
                    <a href="{{ route('admin.effects.index', ['status' => 'all', 'sort' => $sort, 'category' => $selectedCategory, 's' => request('s')]) }}" class="btn filter-btn-custom {{ $filter === 'all' ? 'active' : '' }}">
                      <i class="fa fa-th-list"></i> All <span class="filter-badge" id="tab_all_badge">{{ number_format($statusCounts->total ?? 0) }}</span>
                    </a>

                    <a href="{{ route('admin.effects.index', ['status' => 'ready', 'sort' => $sort, 'category' => $selectedCategory, 's' => request('s')]) }}" class="btn filter-btn-custom {{ $filter === 'ready' ? 'active' : '' }}">
                      <i class="fa fa-check-circle text-success"></i> Ready <span class="filter-badge text-success" id="tab_ready_badge">{{ number_format($statusCounts->ready ?? 0) }}</span>
                    </a>

                    <a href="{{ route('admin.effects.index', ['status' => 'processing', 'sort' => $sort, 'category' => $selectedCategory, 's' => request('s')]) }}" class="btn filter-btn-custom {{ $filter === 'processing' ? 'active' : '' }}">
                      <i class="fa fa-spinner fa-spin text-warning"></i> In Processing <span class="filter-badge text-warning" id="tab_processing_badge">{{ number_format($statusCounts->processing ?? 0) }}</span>
                    </a>

                    <a href="{{ route('admin.effects.index', ['status' => 'pending', 'sort' => $sort, 'category' => $selectedCategory, 's' => request('s')]) }}" class="btn filter-btn-custom {{ $filter === 'pending' ? 'active' : '' }}">
                      <i class="fa fa-clock-o text-info"></i> Pending <span class="filter-badge text-info" id="tab_pending_badge">{{ number_format($statusCounts->pending ?? 0) }}</span>
                    </a>

                    <a href="{{ route('admin.effects.index', ['status' => 'failed', 'sort' => $sort, 'category' => $selectedCategory, 's' => request('s')]) }}" class="btn filter-btn-custom {{ $filter === 'failed' ? 'active' : '' }}">
                      <i class="fa fa-exclamation-triangle text-danger"></i> Not In Processing <span class="filter-badge text-danger" id="tab_failed_badge">{{ number_format($statusCounts->failed ?? 0) }}</span>
                    </a>
                  </div>

                  <!-- Processing Order Selector -->
                  <div class="d-flex align-items-center">
                    <span class="text-white font-12 mr-2"><i class="fa fa-sort"></i> Order:</span>
                    <div class="btn-group btn-group-sm">
                      <a href="{{ route('admin.effects.index', ['status' => $filter, 'sort' => 'processing_first', 'category' => $selectedCategory, 's' => request('s')]) }}" 
                         class="btn btn-xs {{ $sort === 'processing_first' ? 'btn-secondary active' : 'btn-outline-secondary' }}" 
                         title="Processing Order (Top to Bottom: Active first, then next in Queue)">
                        <i class="fa fa-arrow-down"></i> Processing Order (Top to Bottom)
                      </a>
                      <a href="{{ route('admin.effects.index', ['status' => $filter, 'sort' => 'asc', 'category' => $selectedCategory, 's' => request('s')]) }}" 
                         class="btn btn-xs {{ $sort === 'asc' ? 'btn-secondary active' : 'btn-outline-secondary' }}" 
                         title="ID Ascending (1 to 9999)">
                        <i class="fa fa-sort-numeric-asc"></i> ID (1 &rarr; End)
                      </a>
                      <a href="{{ route('admin.effects.index', ['status' => $filter, 'sort' => 'desc', 'category' => $selectedCategory, 's' => request('s')]) }}" 
                         class="btn btn-xs {{ $sort === 'desc' ? 'btn-secondary active' : 'btn-outline-secondary' }}" 
                         title="Newest ID first">
                        <i class="fa fa-sort-numeric-desc"></i> Newest First
                      </a>
                    </div>
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
                <table class="table table-bordered table-effects-custom">
                  <thead>
                    <tr>
                      <th style="width: 70px;">ID</th>
                      <th>Title</th>
                      <th>Category</th>
                      <th>Effect URL / GD Link</th>
                      <th>Access & Status</th>
                      <th style="min-width: 170px;">Process Status</th>
                      <th class="text-center" style="width: 120px;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                   @forelse($effects as $effect)
                    @php
                      $isCurrentlyProcessing = in_array($effect->status, ['downloading', 'processing']);
                    @endphp
                    <tr id="card_box_id_{{$effect->id}}" 
                        data-effect-id="{{ $effect->id }}" 
                        data-status="{{ $effect->status }}"
                        class="{{ $isCurrentlyProcessing ? 'row-processing-active' : '' }}">
                      <td><span class="badge badge-dark">#{{ $effect->id }}</span></td>
                      <td>
                        <strong>{{ $effect->title }}</strong>
                      </td>
                      <td><span class="badge badge-info">{{ $effect->category ?? 'General' }}</span></td>
                      <td>
                        <div class="input-group input-group-sm" style="min-width: 340px;">
                          <input type="text" class="form-control form-control-sm" id="effect_url_{{ $effect->id }}" value="{{ $effect->effect_url }}" readonly style="background: #141b29; color: #cbd5e1; border-color: #2b384e;">
                          <div class="input-group-append">
                            @if($effect->processed_url)
                              <button class="btn btn-sm btn-info btn-preview-processed" type="button" onclick="showPreview('{{ $effect->processed_url }}')" data-toggle="tooltip" title="Preview Processed Video"><i class="fa fa-play-circle"></i> Preview</button>
                            @endif
                            <button class="btn btn-sm btn-secondary" type="button" onclick="copyEffectUrl('effect_url_{{ $effect->id }}')" data-toggle="tooltip" title="Copy URL"><i class="fa fa-copy"></i> Copy</button>
                            <a href="{{ $effect->effect_url }}" target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Open Link"><i class="fa fa-external-link"></i> Open</a>
                          </div>
                        </div>
                      </td>
                      <td style="white-space: nowrap;">
                        @if($effect->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                        @if($effect->is_premium || ($effect->license_price && $effect->license_price > 0))
                            <span class="badge badge-warning"><i class="fa fa-star"></i> Premium (${{ number_format($effect->license_price, 2) }})</span>
                        @else
                            <span class="badge badge-success">Free</span>
                        @endif
                      </td>
                      <td class="status-cell">
                          @if($effect->status == 'ready')
                              <span class="badge badge-success" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-check-circle"></i> Ready</span>
                              @if($effect->converted_bytes !== null)
                                  <br><small style="color: #aaa;">{{ number_format($effect->converted_bytes / 1048576, 2) }} MB</small>
                              @endif
                          @elseif($effect->status == 'downloading')
                              <span class="badge-converting-active"><i class="fa fa-cloud-download fa-spin mr-1"></i> {{ $effect->process_step ?: 'Downloading...' }}</span>
                          @elseif($effect->status == 'processing')
                              <span class="badge-converting-active"><i class="fa fa-spinner fa-spin mr-1"></i> {{ $effect->process_step ?: 'Converting MP4...' }}</span>
                          @elseif($effect->status == 'error' || $effect->status == 'failed')
                              <span class="badge badge-danger" style="padding: 6px 10px; font-size: 11px;" data-toggle="tooltip" title="{{ $effect->process_step }}"><i class="fa fa-exclamation-triangle"></i> Not Processing (Failed)</span>
                              @if($effect->process_step)
                                <br><small class="text-danger font-11 d-inline-block mt-1" style="max-width: 250px; line-height: 1.3;" data-toggle="tooltip" title="{{ $effect->process_step }}"><i class="fa fa-info-circle mr-1"></i>{{ $effect->process_step }}</small>
                              @endif
                          @else
                              <span class="badge badge-secondary" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-clock-o"></i> In Queue (Waiting)</span>
                          @endif
                      </td>
                      <td class="text-center" style="white-space: nowrap;">
                        @if($effect->status != 'ready')
                          <form action="{{ route('admin.effects.retry-single', $effect->id) }}" method="POST" id="retry-single-form-{{ $effect->id }}" style="display:inline-block;">
                            @csrf
                            <button type="button" class="btn btn-icon waves-effect waves-light btn-warning btn-xs m-r-5" onclick="confirmRetrySingle({{ $effect->id }}, '{{ addslashes($effect->title) }}')" data-toggle="tooltip" title="Retry Processing Now"> <i class="fa fa-refresh"></i> </button>
                          </form>
                        @endif
                        <a href="{{ route('admin.effects.edit', $effect->id) }}" class="btn btn-icon waves-effect waves-light btn-success btn-xs m-r-5" data-toggle="tooltip" title="Edit"> <i class="fa fa-edit"></i> </a>
                        <form action="{{ route('admin.effects.destroy', $effect->id) }}" method="POST" id="delete-form-{{ $effect->id }}" style="display:inline-block;">
                          @csrf
                          @method('DELETE')
                          <button type="button" class="btn btn-icon waves-effect waves-light btn-danger btn-xs" onclick="confirmDeleteEffect({{ $effect->id }})" data-toggle="tooltip" title="Remove"> <i class="fa fa-remove"></i> </button>
                        </form>
                      </td>
                    </tr>
                   @empty
                    <tr>
                      <td colspan="7" class="text-center py-4 text-muted">
                        <i class="fa fa-info-circle font-18 mb-2 d-block"></i>
                        No effects found matching status filter <strong>"{{ ucfirst($filter) }}"</strong>.
                      </td>
                    </tr>
                   @endforelse
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

        function confirmRetryFailed() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Retry Failed Effects?',
                    text: 'Re-queue failed effects in a controlled batch of up to 30 with 10-second spaced delays to prevent rate limits?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: '<i class="fa fa-refresh mr-1"></i> Yes, Retry Batch',
                    cancelButtonText: 'Cancel',
                    background: '#1a2234',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('retry-failed-form').submit();
                    }
                });
            } else {
                if (confirm('Re-queue failed effects (batch of up to 30 with 10-second delays between jobs to prevent rate limits)?')) {
                    document.getElementById('retry-failed-form').submit();
                }
            }
        }

        function confirmCleanupInvalid() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Clean Up Invalid Items?',
                    text: 'Permanently remove invalid imported items (such as Google Drive folders, empty 0-byte files, and non-video items) from stock effects?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: '<i class="fa fa-trash-o mr-1"></i> Yes, Clean Up',
                    cancelButtonText: 'Cancel',
                    background: '#1a2234',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('cleanup-invalid-form').submit();
                    }
                });
            } else {
                if (confirm('Permanently remove invalid items (such as Google Drive folders, empty 0-byte files, and non-video items) from imported effects?')) {
                    document.getElementById('cleanup-invalid-form').submit();
                }
            }
        }

        function confirmRetrySingle(id, title) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Retry Processing?',
                    text: 'Re-queue #' + id + ' (' + title + ') for background processing?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: '<i class="fa fa-refresh mr-1"></i> Yes, Retry Now',
                    cancelButtonText: 'Cancel',
                    background: '#1a2234',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('retry-single-form-' + id).submit();
                    }
                });
            } else {
                if (confirm('Re-queue this effect for processing?')) {
                    document.getElementById('retry-single-form-' + id).submit();
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
        (function() {
            function initPoller() {
                if (typeof jQuery === 'undefined') {
                    setTimeout(initPoller, 100);
                    return;
                }
                var $ = jQuery;

                function checkPendingStatus() {
                    var visibleIds = [];
                    $('tr[data-effect-id]').each(function() {
                        visibleIds.push($(this).attr('data-effect-id'));
                    });

                    $.ajax({
                        url: '{{ route("admin.effects.check-status") }}',
                        type: 'GET',
                        data: {
                            ids: visibleIds.join(',')
                        },
                        success: function(response) {
                            var items = response.items ? response.items : response;
                            var counts = response.counts;
                            var activeEffect = response.active_effect;

                            // 1. Update Top Widgets & Tab counts if provided
                            if (counts) {
                                if ($('#stat_total_count').length) $('#stat_total_count').text(Number(counts.total || 0).toLocaleString());
                                if ($('#stat_ready_count').length) $('#stat_ready_count').text(Number(counts.ready || 0).toLocaleString());
                                if ($('#stat_processing_count').length) $('#stat_processing_count').text(Number(counts.processing || 0).toLocaleString());
                                if ($('#stat_pending_count').length) $('#stat_pending_count').text(Number(counts.pending || 0).toLocaleString());
                                if ($('#stat_failed_count').length) $('#stat_failed_count').text(Number(counts.failed || 0).toLocaleString());

                                if ($('#tab_all_badge').length) $('#tab_all_badge').text(Number(counts.total || 0).toLocaleString());
                                if ($('#tab_ready_badge').length) $('#tab_ready_badge').text(Number(counts.ready || 0).toLocaleString());
                                if ($('#tab_processing_badge').length) $('#tab_processing_badge').text(Number(counts.processing || 0).toLocaleString());
                                if ($('#tab_pending_badge').length) $('#tab_pending_badge').text(Number(counts.pending || 0).toLocaleString());
                                if ($('#tab_failed_badge').length) $('#tab_failed_badge').text(Number(counts.failed || 0).toLocaleString());
                            }

                            // 2. Update Active Processing Banner
                            if (activeEffect) {
                                $('#live_processing_banner').css({'background': '#2b2314', 'border-color': '#d97706'});
                                $('#live_banner_content').html('<i class="fa fa-spinner fa-spin text-warning mr-2"></i><strong class="text-warning">Currently Processing:</strong> <span id="active_effect_title">#' + activeEffect.id + ' - ' + (activeEffect.title || 'Effect') + '</span> &nbsp;|&nbsp; <span id="active_effect_step" class="badge badge-warning font-11">' + (activeEffect.process_step || 'Converting...') + '</span>');
                            } else {
                                $('#live_processing_banner').css({'background': '#172030', 'border-color': '#2e3c54'});
                                $('#live_banner_content').html('<i class="fa fa-check-circle text-success mr-2"></i><span id="active_effect_title" class="text-muted">All background jobs completed or queue is idle.</span>');
                            }

                            // 3. Update Table Rows
                            if (items) {
                                $.each(items, function(id, item) {
                                    var tr = $('#card_box_id_' + id);
                                    if (tr.length) {
                                        var currentStatus = item.status ? item.status.toLowerCase() : 'pending';
                                        tr.attr('data-status', currentStatus);
                                        var statusCell = tr.find('.status-cell');
                                        
                                        if (currentStatus === 'ready') {
                                            tr.removeClass('row-processing-active');
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
                                        } else if (currentStatus === 'downloading') {
                                            tr.addClass('row-processing-active');
                                            var stepText = item.process_step || 'Downloading...';
                                            statusCell.html('<span class="badge-converting-active"><i class="fa fa-cloud-download fa-spin mr-1"></i> ' + stepText + '</span>');
                                        } else if (currentStatus === 'processing') {
                                            tr.addClass('row-processing-active');
                                            var stepText = item.process_step || 'Converting MP4...';
                                            statusCell.html('<span class="badge-converting-active"><i class="fa fa-spinner fa-spin mr-1"></i> ' + stepText + '</span>');
                                        } else if (currentStatus === 'error' || currentStatus === 'failed') {
                                            tr.removeClass('row-processing-active');
                                            var stepText = item.process_step || 'Failed';
                                            statusCell.html('<span class="badge badge-danger" style="padding: 6px 10px; font-size: 11px;" data-toggle="tooltip" title="' + stepText + '"><i class="fa fa-exclamation-triangle"></i> Not Processing (Failed)</span><br><small class="text-danger font-11 d-inline-block mt-1" style="max-width: 250px; line-height: 1.3;" data-toggle="tooltip" title="' + stepText + '"><i class="fa fa-info-circle mr-1"></i>' + stepText + '</small>');
                                        } else {
                                            tr.removeClass('row-processing-active');
                                            statusCell.html('<span class="badge badge-secondary" style="padding: 6px 10px; font-size: 11px;"><i class="fa fa-clock-o"></i> In Queue (Waiting)</span>');
                                        }
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.warn("Status check AJAX error:", error);
                        }
                    });
                }

                checkPendingStatus();
                setInterval(checkPendingStatus, 5000);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initPoller);
            } else {
                initPoller();
            }
        })();

        // Also stop video if modal is closed by clicking outside
        $('#videoPreviewModal').on('hidden.bs.modal', function () {
            closePreview();
        });
    </script>
@endsection
