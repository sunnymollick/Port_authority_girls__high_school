@extends('frontend.layouts.right_master')
@section('title', ' class Routine')
@section('content')
    <h4>Academic Calender</h4>
    <hr/>
    <h5>Academic Calender of {{config('running_session') . ' Session'}}</h5> <br/>
    <a class="btn btn-success" href="{{ asset($calender->file_path) }}" target="_blank"> Download </a>
@endsection