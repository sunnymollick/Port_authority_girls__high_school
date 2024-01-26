<!-- Hero section -->
@if($sliders)
    <section class="hero-section">
        <div class="hero-slider owl-carousel">
            @foreach($sliders as $slider)
                <div class="hs-item set-bg" data-setbg="{{ asset($slider->file_path) }}">
                    <div class="hs-text">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="hs-subtitle">{{ $slider->sub_title }}</div>
                                    <h2 class="hs-title">{{ $slider->title }}</h2>
                                    <p class="hs-des">{{ $slider->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    <!-- Hero section end -->
@endif