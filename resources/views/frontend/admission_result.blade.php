@extends('frontend.layouts.right_master')
@section('title', 'Admission Result')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h4>Admission Result</h4>
            <hr>
        </div>
        <div class="col-md-12 col-sm-12 table-responsive">
            <table id="manage_all" class="table table-collapse table-bordered table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Download</th>
                </tr>
                </thead>
                <tbody>
                @php $no =1  @endphp
                @foreach($result as $value)
                    <tr>
                        <td>{{$no++}}</td>
                        <td>{{$value->title}}</td>
                        <td>{{$value->stdclass->name}}</td>
                        <td>{{$value->section->name}}</td>
                        <td>{!! $value->file_path ? "<a class='btn btn-primary' href='" . asset($value->file_path) . "' target='_blank'>Download</a>" : '' !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
