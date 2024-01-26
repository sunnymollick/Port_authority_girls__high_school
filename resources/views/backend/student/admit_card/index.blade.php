@extends('backend.layouts.student_master')
@section('title', 'Admit Card')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Admit Card Print </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for=""> Select a Exam </label>
                                <select name="exam_id" id="exam_id" class="form-control" required>
                                    <option value="" selected disabled>Select a Exam</option>
                                    @foreach($exams as $exam)
                                        <option value="{{$exam->id}}">{{$exam->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group  col-xl-2 col-lg-2 col-md-2 col-sm-12 mb-3 mb-lg-0">
                                <button type="button" class="btn  btn-success form-control"
                                        onclick="printAdmitCard()">Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function () {
            $('#loader').hide();
            $('.filter').select2();
        });

        function printAdmitCard() {

            var exam_id = $("#exam_id").val();

            if (exam_id != null) {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                var base = '/student/generateAdmitCard';
                var url = base + '?exam_id=' + exam_id;
                window.location.href = url;

            } else {
                swal("Warning!!", "Please select class, section and month", "warning");
            }
        }
    </script>
@stop