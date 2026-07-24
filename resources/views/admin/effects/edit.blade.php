@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card-box">
              <h4 class="m-t-0 header-title">Edit Stock Effect</h4>
              <p class="text-muted m-b-30 font-13">Modify effect parameters and Google Drive links.</p>

              @if (count($errors) > 0)
                <div class="alert alert-danger">
                  <ul>
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <form action="{{ route('admin.effects.update', $effect->id) }}" method="POST" class="form-horizontal" role="form">
                @csrf
                @method('PUT')

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Effect Title *</label>
                  <div class="col-sm-9">
                    <input type="text" name="title" value="{{ old('title', $effect->title) }}" class="form-control" required>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Category</label>
                  <div class="col-sm-9">
                    <input type="text" name="category" value="{{ old('category', $effect->category) }}" class="form-control">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Effect File URL / Google Drive Link *</label>
                  <div class="col-sm-9">
                    <input type="url" name="effect_url" value="{{ old('effect_url', $effect->effect_url) }}" class="form-control" required>
                    <small class="form-text text-muted">Direct file URL or Google Drive share link.</small>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Description</label>
                  <div class="col-sm-9">
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $effect->description) }}</textarea>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Access Type (Free / Premium) *</label>
                  <div class="col-sm-9">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="is_premium" id="premium_free" value="0" {{ !$effect->is_premium && ($effect->license_price == 0) ? 'checked' : '' }} onclick="document.getElementById('price_box').style.display='none';">
                      <label class="form-check-label text-success font-weight-bold" for="premium_free">Free Access</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="is_premium" id="premium_pro" value="1" {{ $effect->is_premium || ($effect->license_price > 0) ? 'checked' : '' }} onclick="document.getElementById('price_box').style.display='block';">
                      <label class="form-check-label text-warning font-weight-bold" for="premium_pro">Premium Only (Pro)</label>
                    </div>
                  </div>
                </div>

                <div class="form-group row" id="price_box" style="display: {{ $effect->is_premium || ($effect->license_price > 0) ? 'block' : 'none' }};">
                  <label class="col-sm-3 col-form-label">License Price ($)</label>
                  <div class="col-sm-9">
                    <input type="number" step="0.01" min="0" name="license_price" value="{{ old('license_price', $effect->license_price) }}" class="form-control">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Status</label>
                  <div class="col-sm-9">
                    <div class="checkbox checkbox-success">
                      <input id="is_active" name="is_active" type="checkbox" value="1" {{ $effect->is_active ? 'checked' : '' }}>
                      <label for="is_active"> Active / Published</label>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary waves-effect waves-light"> Update Effect </button>
                    <a href="{{ route('admin.effects.index') }}" class="btn btn-secondary waves-effect"> Cancel </a>
                  </div>
                </div>

              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
    @include("admin.copyright")
</div>

@endsection
