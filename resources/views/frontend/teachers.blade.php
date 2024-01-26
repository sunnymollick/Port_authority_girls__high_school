@extends('frontend.layouts.master')
@section('title', ' Teachers')
@section('content')
	<div class="container m-top-60">
		<div class="section-title text-center">
			<h3>Our Honourable Teachers</h3>
		</div>
		<div class="row">
			@foreach($teacher as $value)
			<div class="col-md-3 col-sm-12">
				<div class="member">
					<div class="member-pic set-bg" data-setbg="{{ asset($value->file_path) }}">

					</div>
					<h5>{{ $value->name }}</h5>
					<p>{{ $value->designation }}</p>
				</div>
			</div>
			@endforeach
		</div>
    </div>
@endsection
