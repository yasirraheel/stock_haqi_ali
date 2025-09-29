@if(count($slider)!=0)
<!-- Start Slider -->
<div class="slider-area">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators">
            @foreach($slider as $key => $slider_data)
              <li data-target="#carouselExampleIndicators" data-slide-to="{{$key}}" @if($key==0) class="active" @endif></li>
            @endforeach
          </ol>
          <div class="carousel-inner">
            @foreach($slider as $key => $slider_data)
            <div class="carousel-item @if($key==0) active @endif">
              <img class="d-block w-100" src="{{URL::to('/'.$slider_data->slider_image)}}" alt="{{$slider_data->slider_title}}">
              @if($slider_data->slider_title || $slider_data->slider_description)
              <div class="carousel-caption d-none d-md-block">
                @if($slider_data->slider_title)
                <h5>{{$slider_data->slider_title}}</h5>
                @endif
                @if($slider_data->slider_description)
                <p>{{$slider_data->slider_description}}</p>
                @endif
              </div>
              @endif
            </div>
            @endforeach
          </div>
          <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- End Slider -->
@endif
