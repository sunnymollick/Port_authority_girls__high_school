@extends('frontend.layouts.right_master')
@section('title', ' Academic Events Calender')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h4> {{ $title }}</h4>
            <hr>
        </div>
        <div class="col-md-12">
            <div id="tag_container">
                @include('frontend.academicNoticesNewsPag')
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
@stop




