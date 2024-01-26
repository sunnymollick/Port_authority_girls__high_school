@extends('frontend.layouts.master')
@section('title', 'Home')
@section('content')

    <!--=== Slider Start ===---->

    @include('frontend.layouts.slider')

    <!--=== Slider End ===---->

    <!--=== Welcome Chairman Message and Notice Board Start ===---->
    <div class="container m-top-60">
        <div class="row">
            <!-- Home message -->
            <div class="col-md-8 col-sm-12 post-list">
                <div class="post-item">
                    <div class="post-content">
                        <h4>Welcome To Khagrachari Police Lines High School</h4>
                        <hr/>
                        <p class="justify">
                            <img src="{{ asset('assets/images/front/gallery_7.jpg') }}" class="img-thumbnail"
                                 width="300"
                                 alt=""/>
                            Khagrachari Hill Tracts is a district of wonderful landscape of natural beauty. People of
                            different religion, tribes and castes live here together in peace and solidarity. The
                            generosity of nature that can be experienced in the people of this diversified cultural
                            environment is really extraordinary. In such a distinct environment, Khagrachari Police
                            Lines High School has been established in 2011. Developed by the sincere efforts of the then
                            Superintendent of police, Mr. Abul Kalam Siddique, Khagrachari District Police and the
                            fervent cooperation of the inhabitants of Khagrachari, this institution has started its
                            educational journey since 2012.
                            At the beginning of 21st century as well as in the stream of science and knowledge, skilled
                            human society is the only medium to become a developed nation. Educational institution and
                            teacher’s society are the pioneers who play an active role in developing such precious human
                            resources. In the backward areas of Khagrachari Hill Tracts where there is insufficiency of
                            educational institutions, where the dream of making skilled human resource is fading in the
                            dark Khagracahri Police Lines High School has been developed keeping this dream ahead.
                            Education no matter which kind it is turns people in a positive way. Real education aids our
                            children to build up character and human life. The main aim of education is to create human
                            with human qualities. Khagrachari Police Lines High School has been published keeping the
                            aim of making our children real human. When Poor and helpless people of this locality are
                            deprived of quality education because of money right then this school has come up for those
                            people as a hope of light...
                            <a href="{{ URL :: to('/ourHistory') }}" class="text-green"> Read More</a>
                        </p>
                    </div>
                </div>
            </div>
            <!-- sidebar -->
            <div class="col-sm-12 col-md-4 sidebar">
                @include('frontend.layouts.notice')
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12 post-list">
                <div class="post-item">
                    <div class="post-content">
                        <h4>Message of Honorable President </h4>
                        <hr/>
                        <p class="justify">
                            <img src="{{ asset('assets/images/committee/Md_Ahmar_Uzzaman.jpg') }}" class="img-thumbnail"
                                 width="200"
                                 alt=""/>
                            Bangladesh has been moving towards with continuous development for the last two decades
                            facing the increasing competition of this intensive competitive world begun from the 21st
                            century. We already have joined the lower-middle income country category. Through the
                            implementation of vision 2021 the country continues to express its aspiration to join the
                            middle income country by 2021 and also by 2041, expecting herself as a developed country
                            Khagrachari Police Lines High School is working diligently to build up a skilled and
                            meritorious human resources who are the crying need to achieve this goal... ...
                            <a href="{{ URL :: to('/chairmanMessage') }}" class="text-green"> Read More</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 post-list">
                <div class="post-item">
                    <div class="post-content">
                        <h4>Message of Honorable Principal </h4>
                        <hr/>
                        <p class="justify">
                            <img src="{{ asset('assets/images/committee/Mr_Uttam_Kumar_Nath.jpg') }}"
                                 class="img-thumbnail" width="200"
                                 alt=""/>
                            At the dawn of the creation, man intends to disclose himself to others. This trait of
                            disclosure is man’s instinctive quality. “Khagrachari Police Lines High School” is
                            contributing this gigantic task to fetch the hidden talent out of the tender hearted
                            students taking part in co-educational process along with pragmatic timely academic
                            education for the betterment of these backward communities to make themselves perfect
                            citizen. This institution is playing a pioneer role to develop human resources upholding the
                            actual scientific and technology based education method...
                            <a href="{{ URL :: to('/principalMessage') }}" class="text-green"> Read More</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--=== Welcome Chairman Message and Notice Board End ===---->

    <!--=== Count Board Start ===---->

    <section class="fact-section spad set-bg" data-setbg="{{ asset('assets/images/count.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-4 fact">
                    <div class="fact-icon">
                        <i class="ti-crown"></i>
                    </div>
                    <div class="fact-text">
                        <h2>{{ $app_settings->stablished }}</h2>
                        <p>School Stablished Year</p>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 fact">
                    <div class="fact-icon">
                        <i class="ti-briefcase"></i>
                    </div>
                    <div class="fact-text">
                        <h2>{{ $teacher }}</h2>
                        <p>TEACHERS</p>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 fact">
                    <div class="fact-icon">
                        <i class="ti-user"></i>
                    </div>
                    <div class="fact-text">
                        <h2>{{ $students  }}</h2>
                        <p>STUDENTS Of {{ date('Y') }} Session </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--=== Count Board End ===---->


    <!--=== Gallery Start ===---->
    {{--@include('frontend.layouts.gallery')--}}
    <!--=== Gallery Start ===---->

    <!--=== Latest News Start ===---->
    @include('frontend.layouts.latest_news')
    <!--=== Latest News Start ===---->


    <!--=== Birthday Start ===---->
    @include('frontend.layouts.birthday')
    <!--=== Birthday Start ===---->
@endsection
