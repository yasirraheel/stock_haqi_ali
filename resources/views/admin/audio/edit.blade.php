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
                                <h4 class="m-t-0 header-title">Edit Audio</h4>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('admin.audio.index') }}" class="btn btn-secondary btn-sm pull-right">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    <form action="{{ route('admin.audio.update', $audio->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title', $audio->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="4">{{ old('description', $audio->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="audio_file">Audio File</label>
                                    <input type="file" class="form-control-file @error('audio_file') is-invalid @enderror"
                                           id="audio_file" name="audio_file" accept="audio/*" onchange="handleAudioUpload(this)">
                                    <small class="form-text text-muted">Supported formats: MP3, WAV, OGG, AAC, FLAC (Max: 50MB)</small>
                                    @if($audio->audio_path)
                                        <div class="mt-2">
                                            <strong>Current file:</strong>
                                            <div class="card mt-2">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-2 text-center">
                                                            <button type="button" id="current_play_pause_btn" class="btn btn-primary btn-circle" style="width: 40px; height: 40px; border-radius: 50%;">
                                                                <i class="fa fa-play" id="current_play_icon"></i>
                                                            </button>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="audio-info">
                                                                <h6 class="mb-1">{{ $audio->title }}</h6>
                                                                <small id="current_audio_duration">0:00 / 0:00</small>
                                                            </div>
                                                            <div class="progress mt-1">
                                                                <div id="current_progress_bar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 text-right">
                                                            <a href="{{ asset('storage/' . $audio->audio_path) }}" target="_blank" class="btn btn-sm btn-info" title="Download">
                                                                <i class="fa fa-download"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <audio id="current_audio_player" preload="metadata" style="display: none;">
                                                        <source src="{{ asset('storage/' . $audio->audio_path) }}" type="audio/mpeg">
                                                        <source src="{{ asset('storage/' . $audio->audio_path) }}" type="audio/wav">
                                                        <source src="{{ asset('storage/' . $audio->audio_path) }}" type="audio/ogg">
                                                        <source src="{{ asset('storage/' . $audio->audio_path) }}" type="audio/mp4">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @error('audio_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <!-- Audio Preview -->
                                    <div id="audio_preview" style="display: none; margin-top: 15px;">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-2 text-center">
                                                        <button type="button" id="play_pause_btn" class="btn btn-primary btn-circle" style="width: 50px; height: 50px; border-radius: 50%;">
                                                            <i class="fa fa-play" id="play_icon"></i>
                                                        </button>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="audio-info">
                                                            <h6 class="mb-1" id="audio_title">Audio Preview</h6>
                                                            <small id="audio_duration">0:00 / 0:00</small>
                                                        </div>
                                                        <div class="progress mt-2">
                                                            <div id="progress_bar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-right">
                                                        <button type="button" id="volume_btn" class="btn btn-sm btn-info" style="border-radius: 50%; width: 35px; height: 35px; padding: 0;">
                                                            <i class="fa fa-volume-up" id="volume_icon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <audio id="audio_player" style="display: none;">
                                                    <source src="" type="audio/mpeg">
                                                    Your browser does not support the audio element.
                                                </audio>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Metadata Display -->
                                    <div id="metadata_section" style="display: none; margin-top: 15px; background-color: #2c3e50; padding: 15px; border-radius: 5px; border: 1px solid #34495e;">
                                        <h6 style="color: #ecf0f1; margin-bottom: 10px;">Extracted Metadata:</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small style="color: #ecf0f1;"><strong style="color: #3498db;">File Size:</strong> <span class="metadata-value" style="color: #ecf0f1;">-</span></small><br>
                                                <small style="color: #ecf0f1;"><strong style="color: #3498db;">Duration:</strong> <span class="metadata-value" style="color: #ecf0f1;">-</span></small><br>
                                                <small style="color: #ecf0f1;"><strong style="color: #3498db;">Format:</strong> <span class="metadata-value" style="color: #ecf0f1;">-</span></small>
                                            </div>
                                            <div class="col-md-6">
                                                <small style="color: #ecf0f1;"><strong style="color: #3498db;">Bitrate:</strong> <span class="metadata-value" style="color: #ecf0f1;">-</span></small><br>
                                                <small style="color: #ecf0f1;"><strong style="color: #3498db;">Sample Rate:</strong> <span class="metadata-value" style="color: #ecf0f1;">-</span></small><br>
                                                <small style="color: #ecf0f1;"><strong style="color: #3498db;">Channels:</strong> <span class="metadata-value" style="color: #ecf0f1;">-</span></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="license_price">License Price ($)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('license_price') is-invalid @enderror"
                                               id="license_price" name="license_price" value="{{ old('license_price', $audio->license_price) }}"
                                               step="0.01" min="0" pattern="[0-9]+([.][0-9]{1,2})?">
                                    </div>
                                    <small class="form-text text-muted">Leave empty for free audio</small>
                                    @error('license_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="duration">Duration</label>
                                    <input type="text" class="form-control @error('duration') is-invalid @enderror"
                                           id="duration" name="duration" value="{{ old('duration', $audio->duration) }}"
                                           placeholder="e.g., 3:45">
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="file_size">File Size</label>
                                    <input type="text" class="form-control @error('file_size') is-invalid @enderror"
                                           id="file_size" name="file_size" value="{{ old('file_size', $audio->file_size) }}"
                                           placeholder="e.g., 5.2 MB">
                                    @error('file_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="format">Format</label>
                                    <select class="form-control @error('format') is-invalid @enderror" id="format" name="format">
                                        <option value="">Select Format</option>
                                        <option value="MP3" {{ old('format', $audio->format) == 'MP3' ? 'selected' : '' }}>MP3</option>
                                        <option value="WAV" {{ old('format', $audio->format) == 'WAV' ? 'selected' : '' }}>WAV</option>
                                        <option value="OGG" {{ old('format', $audio->format) == 'OGG' ? 'selected' : '' }}>OGG</option>
                                        <option value="AAC" {{ old('format', $audio->format) == 'AAC' ? 'selected' : '' }}>AAC</option>
                                        <option value="FLAC" {{ old('format', $audio->format) == 'FLAC' ? 'selected' : '' }}>FLAC</option>
                                    </select>
                                    @error('format')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="bitrate">Bitrate (kbps)</label>
                                    <input type="number" class="form-control @error('bitrate') is-invalid @enderror"
                                           id="bitrate" name="bitrate" value="{{ old('bitrate', $audio->bitrate) }}" min="0">
                                    @error('bitrate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="sample_rate">Sample Rate (Hz)</label>
                                    <input type="number" class="form-control @error('sample_rate') is-invalid @enderror"
                                           id="sample_rate" name="sample_rate" value="{{ old('sample_rate', $audio->sample_rate) }}" min="0">
                                    @error('sample_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="genre">Genre</label>
                                    <input type="text" class="form-control @error('genre') is-invalid @enderror"
                                           id="genre" name="genre" value="{{ old('genre', $audio->genre) }}"
                                           placeholder="e.g., Electronic, Rock, Jazz">
                                    @error('genre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="mood">Mood</label>
                                    <input type="text" class="form-control @error('mood') is-invalid @enderror"
                                           id="mood" name="mood" value="{{ old('mood', $audio->mood) }}"
                                           placeholder="e.g., Upbeat, Calm, Energetic">
                                    @error('mood')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="tags">Tags</label>
                                    <input type="text" class="form-control @error('tags') is-invalid @enderror"
                                           id="tags" name="tags" value="{{ old('tags', $audio->tags) }}"
                                           placeholder="e.g., background, corporate, ambient">
                                    <small class="form-text text-muted">Separate tags with commas</small>
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                               value="1" {{ old('is_active', $audio->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Audio
                            </button>
                            <a href="{{ route('admin.audio.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function handleAudioUpload(input) {
    if (input.files && input.files[0]) {
        var file = input.files[0];
        var reader = new FileReader();

        reader.onload = function(e) {
            // Set up the audio player
            var audioPlayer = document.getElementById('audio_player');
            var source = audioPlayer.querySelector('source');
            source.src = e.target.result;
            audioPlayer.load();

            // Update audio title
            $('#audio_title').text(file.name);

            // Show the preview
            $('#audio_preview').show();

            // Initialize audio player controls
            initializeAudioPlayer();
        }

        reader.readAsDataURL(file);

        // Extract metadata
        extractAudioMetadata(file);
    }
}

function initializeAudioPlayer() {
    var audioPlayer = document.getElementById('audio_player');
    var playPauseBtn = document.getElementById('play_pause_btn');
    var playIcon = document.getElementById('play_icon');
    var progressBar = document.getElementById('progress_bar');
    var audioDuration = document.getElementById('audio_duration');
    var volumeBtn = document.getElementById('volume_btn');
    var volumeIcon = document.getElementById('volume_icon');

    var isPlaying = false;
    var isMuted = false;

    // Play/Pause functionality
    playPauseBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (isPlaying) {
            audioPlayer.pause();
            playIcon.className = 'fa fa-play';
            isPlaying = false;
        } else {
            audioPlayer.play();
            playIcon.className = 'fa fa-pause';
            isPlaying = true;
        }
    });

    // Volume control
    volumeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (isMuted) {
            audioPlayer.volume = 1;
            volumeIcon.className = 'fa fa-volume-up';
            isMuted = false;
        } else {
            audioPlayer.volume = 0;
            volumeIcon.className = 'fa fa-volume-off';
            isMuted = true;
        }
    });

    // Progress bar update
    audioPlayer.addEventListener('timeupdate', function() {
        if (audioPlayer.duration) {
            var progress = (audioPlayer.currentTime / audioPlayer.duration) * 100;
            progressBar.style.width = progress + '%';

            // Update duration display
            var currentTime = formatTime(audioPlayer.currentTime);
            var totalTime = formatTime(audioPlayer.duration);
            audioDuration.textContent = currentTime + ' / ' + totalTime;
        }
    });

    // Progress bar click to seek
    progressBar.parentElement.addEventListener('click', function(e) {
        if (audioPlayer.duration) {
            var rect = this.getBoundingClientRect();
            var clickX = e.clientX - rect.left;
            var width = rect.width;
            var seekTime = (clickX / width) * audioPlayer.duration;
            audioPlayer.currentTime = seekTime;
        }
    });

    // Audio ended
    audioPlayer.addEventListener('ended', function() {
        playIcon.className = 'fa fa-play';
        isPlaying = false;
        progressBar.style.width = '0%';
        audioDuration.textContent = '0:00 / ' + formatTime(audioPlayer.duration);
    });

    // Audio loaded
    audioPlayer.addEventListener('loadedmetadata', function() {
        var totalTime = formatTime(audioPlayer.duration);
        audioDuration.textContent = '0:00 / ' + totalTime;
    });
}

function formatTime(seconds) {
    if (isNaN(seconds)) return '0:00';

    var minutes = Math.floor(seconds / 60);
    var secs = Math.floor(seconds % 60);
    return minutes + ':' + (secs < 10 ? '0' : '') + secs;
}

function extractAudioMetadata(file) {
    console.log('Starting metadata extraction for file:', file.name);

    var formData = new FormData();
    formData.append('audio', file);
    formData.append('_token', '{{ csrf_token() }}');

    $.ajax({
        url: '{{ route("admin.audio.metadata") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Metadata extraction response:', response);

            if (response.success) {
                var meta = response.metadata;
                console.log('Extracted metadata:', meta);

                // Display metadata
                $('#metadata_section').show();
                $('.metadata-value').eq(0).text(meta.file_size || '-');
                $('.metadata-value').eq(1).text(meta.duration || '-');
                $('.metadata-value').eq(2).text(meta.file_type || '-');
                $('.metadata-value').eq(3).text(meta.bitrate ? meta.bitrate + ' kbps' : '-');
                $('.metadata-value').eq(4).text(meta.sample_rate ? meta.sample_rate + ' Hz' : '-');
                $('.metadata-value').eq(5).text(meta.channels || '-');

                // Auto-fill form fields if they're empty
                if (!$('#title').val() && meta.suggested_title) {
                    $('#title').val(meta.suggested_title);
                    console.log('Auto-filled title:', meta.suggested_title);
                }

                if (!$('#duration').val() && meta.duration) {
                    $('#duration').val(meta.duration);
                    console.log('Auto-filled duration:', meta.duration);
                }

                if (!$('#file_size').val() && meta.file_size) {
                    $('#file_size').val(meta.file_size);
                    console.log('Auto-filled file size:', meta.file_size);
                }

                if (!$('#format').val() && meta.file_type) {
                    $('#format').val(meta.file_type.toUpperCase());
                    console.log('Auto-filled format:', meta.file_type);
                }

                if (!$('#bitrate').val() && meta.bitrate) {
                    $('#bitrate').val(meta.bitrate);
                    console.log('Auto-filled bitrate:', meta.bitrate);
                }

                if (!$('#sample_rate').val() && meta.sample_rate) {
                    $('#sample_rate').val(meta.sample_rate);
                    console.log('Auto-filled sample rate:', meta.sample_rate);
                }

                if (!$('#genre').val() && meta.suggested_genre) {
                    $('#genre').val(meta.suggested_genre);
                    console.log('Auto-filled genre:', meta.suggested_genre);
                }

                if (!$('#mood').val() && meta.suggested_mood) {
                    $('#mood').val(meta.suggested_mood);
                    console.log('Auto-filled mood:', meta.suggested_mood);
                }

                if (!$('#tags').val() && meta.file_type) {
                    $('#tags').val('audio, ' + meta.file_type.toLowerCase());
                    console.log('Auto-filled tags:', 'audio, ' + meta.file_type.toLowerCase());
                }
            } else {
                console.error('Metadata extraction failed:', response.error);
                $('#metadata_section').show();
                $('.metadata-value').text('Error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error extracting metadata:', error);
            console.error('Response:', xhr.responseText);
            $('#metadata_section').show();
            $('.metadata-value').text('Error');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Format license price to 2 decimal places on blur
    const licensePriceInput = document.getElementById('license_price');
    if (licensePriceInput) {
        licensePriceInput.addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (!isNaN(value)) {
                this.value = value.toFixed(2);
            }
        });
    }

    // Initialize current audio player if it exists
    if (document.getElementById('current_audio_player')) {
        console.log('Initializing current audio player...');
        initializeCurrentAudioPlayer();
    }
});

function initializeCurrentAudioPlayer() {
    var audioPlayer = document.getElementById('current_audio_player');
    var playPauseBtn = document.getElementById('current_play_pause_btn');
    var playIcon = document.getElementById('current_play_icon');
    var progressBar = document.getElementById('current_progress_bar');
    var audioDuration = document.getElementById('current_audio_duration');

    console.log('Audio player element:', audioPlayer);
    console.log('Audio player source:', audioPlayer ? audioPlayer.querySelector('source').src : 'No audio player');
    console.log('Play button element:', playPauseBtn);

    var isPlaying = false;

    // Play/Pause functionality
    playPauseBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (isPlaying) {
            audioPlayer.pause();
            playIcon.className = 'fa fa-play';
            isPlaying = false;
        } else {
            audioPlayer.play();
            playIcon.className = 'fa fa-pause';
            isPlaying = true;
        }
    });

    // Progress bar update
    audioPlayer.addEventListener('timeupdate', function() {
        if (audioPlayer.duration) {
            var progress = (audioPlayer.currentTime / audioPlayer.duration) * 100;
            progressBar.style.width = progress + '%';

            // Update duration display
            var currentTime = formatTime(audioPlayer.currentTime);
            var totalTime = formatTime(audioPlayer.duration);
            audioDuration.textContent = currentTime + ' / ' + totalTime;
        }
    });

    // Progress bar click to seek
    progressBar.parentElement.addEventListener('click', function(e) {
        if (audioPlayer.duration) {
            var rect = this.getBoundingClientRect();
            var clickX = e.clientX - rect.left;
            var width = rect.width;
            var seekTime = (clickX / width) * audioPlayer.duration;
            audioPlayer.currentTime = seekTime;
        }
    });

    // Audio ended
    audioPlayer.addEventListener('ended', function() {
        playIcon.className = 'fa fa-play';
        isPlaying = false;
        progressBar.style.width = '0%';
        audioDuration.textContent = '0:00 / ' + formatTime(audioPlayer.duration);
    });

    // Audio loaded
    audioPlayer.addEventListener('loadedmetadata', function() {
        var totalTime = formatTime(audioPlayer.duration);
        audioDuration.textContent = '0:00 / ' + totalTime;
    });

    // Audio error handling
    audioPlayer.addEventListener('error', function(e) {
        console.error('Audio error:', e);
        console.error('Audio error details:', audioPlayer.error);
        audioDuration.textContent = 'Error loading audio';
    });

    // Audio can play
    audioPlayer.addEventListener('canplay', function() {
        console.log('Audio can play');
    });
}
</script>
@endsection
