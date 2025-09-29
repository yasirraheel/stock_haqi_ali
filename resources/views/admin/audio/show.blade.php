@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="m-t-0 header-title">Audio Details</h4>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('admin.audio.index') }}" class="btn btn-secondary btn-sm pull-right">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                                <a href="{{ route('admin.audio.edit', $audio->id) }}" class="btn btn-warning btn-sm pull-right" style="margin-right: 10px;">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Title:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $audio->title }}
                                </div>
                            </div>
                            <hr>

                            @if($audio->description)
                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Description:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $audio->description }}
                                </div>
                            </div>
                            <hr>
                            @endif

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Audio File:</strong>
                                </div>
                                <div class="col-sm-9">
                                    @if($audio->audio_path)
                                        <audio controls class="w-100">
                                            <source src="{{ asset('storage/' . $audio->audio_path) }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                        <br>
                                        <a href="{{ asset('storage/' . $audio->audio_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fa fa-download"></i> Download Audio
                                        </a>
                                    @else
                                        <span class="text-muted">No audio file</span>
                                    @endif
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>License Price:</strong>
                                </div>
                                <div class="col-sm-9">
                                    @if($audio->license_price && $audio->license_price > 0)
                                        <span class="badge badge-warning">${{ number_format($audio->license_price, 2) }}</span>
                                    @else
                                        <span class="badge badge-success">Free</span>
                                    @endif
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Status:</strong>
                                </div>
                                <div class="col-sm-9">
                                    @if($audio->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Downloads:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $audio->downloads_count }}
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Views:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $audio->views_count }}
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Created:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $audio->created_at->format('M d, Y H:i:s') }}
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Updated:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $audio->updated_at->format('M d, Y H:i:s') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Audio Information</h5>
                                </div>
                                <div class="card-body">
                                    @if($audio->duration)
                                    <div class="mb-3">
                                        <strong>Duration:</strong><br>
                                        {{ $audio->duration }}
                                    </div>
                                    @endif

                                    @if($audio->file_size)
                                    <div class="mb-3">
                                        <strong>File Size:</strong><br>
                                        {{ $audio->file_size }}
                                    </div>
                                    @endif

                                    @if($audio->format)
                                    <div class="mb-3">
                                        <strong>Format:</strong><br>
                                        <span class="badge badge-info">{{ $audio->format }}</span>
                                    </div>
                                    @endif

                                    @if($audio->bitrate)
                                    <div class="mb-3">
                                        <strong>Bitrate:</strong><br>
                                        {{ $audio->bitrate }} kbps
                                    </div>
                                    @endif

                                    @if($audio->sample_rate)
                                    <div class="mb-3">
                                        <strong>Sample Rate:</strong><br>
                                        {{ number_format($audio->sample_rate) }} Hz
                                    </div>
                                    @endif

                                    @if($audio->genre)
                                    <div class="mb-3">
                                        <strong>Genre:</strong><br>
                                        <span class="badge badge-secondary">{{ $audio->genre }}</span>
                                    </div>
                                    @endif

                                    @if($audio->mood)
                                    <div class="mb-3">
                                        <strong>Mood:</strong><br>
                                        <span class="badge badge-primary">{{ $audio->mood }}</span>
                                    </div>
                                    @endif

                                    @if($audio->tags)
                                    <div class="mb-3">
                                        <strong>Tags:</strong><br>
                                        @foreach(explode(',', $audio->tags) as $tag)
                                            <span class="badge badge-light mr-1">{{ trim($tag) }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group-vertical w-100" role="group">
                                        <a href="{{ route('admin.audio.edit', $audio->id) }}" class="btn btn-warning mb-2">
                                            <i class="fas fa-edit"></i> Edit Audio
                                        </a>
                                        <form action="{{ route('admin.audio.destroy', $audio->id) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this audio?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fas fa-trash"></i> Delete Audio
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
