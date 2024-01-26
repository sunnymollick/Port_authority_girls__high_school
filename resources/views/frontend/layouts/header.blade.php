<section class="topbar">
    <div class="container">
        <div class="row">
            <div class="col-md-10 pull-left">
                <p><i class="fa fa-phone"></i> {{$app_settings->contact}} || Email : <i class="fa fa-envelope"></i>
                    {{$app_settings->email}} </p>
            </div>
        </div>
    </div>
</section>
<section class="logo_bar">
    <div class="container">
        <div class="row">
            <!-- logo -->
            <a href="{{ URL :: to('/') }}" class="site-logo"><img src="{{ asset($app_settings->logo) }}" alt=""
                                                                  width="620px"></a>
            <div class="header-info">
                <div class="hf-item">

                </div>
                <div class="hf-item">
                    <i class="fa fa-map-marker"></i>
                    <p><span>Location :</span>{{$app_settings->address}}</p>
                </div>
            </div>
        </div>
    </div>
</section>


