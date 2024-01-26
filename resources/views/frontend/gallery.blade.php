@extends('frontend.layouts.master')
@section('title', 'Gallery')
@section('content')
    <div class="container m-top-60">
        <div class="row">
            <div class="section-title text-center">
                <h3> Our Gallery</h3>
                <hr/>
            </div>
            <div class="col-md-12">
                <div id="tag_container">
                    @include('frontend.galleryPag')
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

        $(window).on('hashchange', function () {
            if (window.location.hash) {
                var page = window.location.hash.replace('#', '');
                if (page == Number.NaN || page <= 0) {
                    return false;
                } else {
                    getData(page);
                }
            }
        });

        $(document).ready(function () {
            $(document).on('click', '.pagination a', function (event) {
                event.preventDefault();
                $('li').removeClass('active');
                $(this).parent('li').addClass('active');
                var page = $(this).attr('href').split('page=')[1];
                getData(page);
            });
        });


        function getData(page) {
            $.ajax({
                url: '?page=' + page,
                type: "get",
                datatype: "html",
                beforeSend: function () {
                    $("#tag_container").empty().html('Please wait...');
                },
            }).done(function (data) {
                $("#tag_container").empty().html(data);
                location.hash = page;
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                $("#tag_container").empty().html('No records found!!');
            });
        }
    </script>
    <link rel="stylesheet" href="{{ asset('/assets/css/jquery-gallery.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/gallery_style.css') }}">
    <script src="{{ asset('assets/js/jquery-gallery.js') }}"></script>
    <style>
        .glryImg {
            margin-left:10px;
            -webkit-transition: all 0.5s ease;
            -moz-transition: all 0.5s ease;
            -o-transition: all 0.5s ease;
            -ms-transition: all 0.5s ease;
            transition: all 0.5s ease;
        }
        .glryImg:hover{
            -webkit-transform: rotate(5deg);
            -moz-transform: rotate(5deg);
            -o-transform: rotate(5deg);
            -ms-transform: rotate(5deg);
            transform: rotate(5deg);

        }
    </style>
    <script>
        $(document).jquerygallery({

// displays a thumbnails navigation
            'coverImgOverlay': true,

// CSS classes
            'imgActive': "imgActive",
            'thumbnail': "coverImgOverlay",
            'overlay': "overlay",

// the height of the thumbnails
            'thumbnailHeight': 100,

// custom navigation controls.
// requires Font Awesome
            'imgNext': "<i class='fa fa-angle-right'></i>",
            'imgPrev': "<i class='fa fa-angle-left'></i>",
            'imgClose': "<i class='fa fa-times'></i>",

// animation speed
            'speed': 700

        });
    </script>

@stop




