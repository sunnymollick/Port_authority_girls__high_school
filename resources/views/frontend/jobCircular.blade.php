@extends('frontend.layouts.right_master')
@section('title', ' Job Circular')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h4>Job Circular</h4>
            <hr/>
        </div>
        <div class="col-md-12">
            @if($jobs)
                @if($total>0)
                    @foreach($jobs as $job)
                        <div class="col-md-6">
                            <div class="blog-item">
                                <div class="blog-thumb set-bg" data-setbg="{{ asset($job->file_path) }}"></div>
                                <div class="blog-content">
                                    <a href="{{ URL :: to('/viewNews/'.$job->id) }}">
                                        <h4>{{ $job->title }}</h4></a>
                                    <div class="blog-meta">
                                        <span><i class="fa fa-calendar-o"></i> {{ $job->created_at }}</span>
                                        <span><i
                                                class="fa fa-user"></i> {{ $job->author? $job->author->name : ''}}</span>
                                    </div>
                                    <p>{{ str_limit($job->description, 100) }} <a
                                            href="{{ URL :: to('/viewNews/'.$job->id) }}" class="text-green">Read
                                            More</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <strong class="has-error">Sorry!! No job circular available now</strong>

                @endif
            @endif
        </div>
    </div>

@stop




