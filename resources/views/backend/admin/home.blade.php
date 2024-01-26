@extends('backend.layouts.master')
@section('title', ' Dashboard')
@section('content')
    <!-- /.row -->
    <!-- Info boxes -->
    <div class="container-fluid">
        <div class="row">
            @if(isset($app_settings))
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-3 text-center">
                            <img src="{{ asset($app_settings->logo) }}" class=""
                                 width="260px"/>
                        </div>
                        <div class="col-md-9">
                            <h5 class="pull-right text-bold text-green"> Running Session
                                : {{ $app_settings->running_year }}</h5>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!-- Info boxes -->
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="mdi mdi-account-group"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Students </span>
                        <span class="info-box-number">{{$data[0]->students}}
                        </span>
                        <span><a href="{{ URL :: to('/admin/students') }}" class="small-box-footer">View All <i
                                    class="fa fa-arrow-circle-right"></i></a></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="mdi mdi-account-group"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Parents</span>
                        <span class="info-box-number">{{$data[0]->parents}} </span>
                        <a href="{{ URL :: to('/admin/parents') }}" class="small-box-footer">View All <i
                                class="fa fa-arrow-circle-right"></i></a>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="mdi mdi-account-group"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Teachers</span>
                        <span class="info-box-number">{{$data[0]->teachers}}</span>
                        <a href="{{ URL :: to('/admin/teachers') }}" class="small-box-footer">View All <i
                                class="fa fa-arrow-circle-right"></i></a>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.row -->

@endsection
