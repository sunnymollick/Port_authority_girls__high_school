@extends('backend.layouts.teacher_master')
@section('title', 'Import Marks')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Import Marks
                        <a style="color: #fff" href="{{ asset('assets/documents/marks_import_file.xlsx') }}"
                           class="btn btn-danger">Demo Marks Excel File</a>
                    </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                            <div id="status"></div>
                            <div class="form-group col-md-4 col-sm-12">
                                <select name="exam_id" id="exam_id" class="form-control" required>
                                    <option value="" selected disabled>Select a Exam</option>
                                    @foreach($exams as $exam)
                                        <option value="{{$exam->id}}">{{$exam->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2 col-sm-12">
                                <select name="class_id" id="class_id" class="form-control" required
                                        onchange="get_sections(this.value)">
                                    <option value="" selected disabled>Select a class</option>
                                    @foreach($stdclass as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3 col-sm-12">
                                <select class="form-control" name="section_id" id="section_id"
                                        onchange="get_class_subjects(this.value)" required>
                                    <option value="">Select a section</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 col-sm-12">
                                <select class="form-control" name="subject_id" id="subject_id" required>
                                    <option value="">Select a subject</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-12">
                                <label for="excel_upload">Import Marks</label>
                                <input id="excel_upload" type="file" name="excel_upload" style="display:none">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <a class="btn btn-success"
                                           onclick="$('input[id=excel_upload]').click();">Browse</a>
                                    </div><!-- /btn-group -->
                                    <input type="text" name="SelectedFileName" class="form-control"
                                           id="SelectedFileName"
                                           value="" readonly required>
                                </div>
                                <div class="clearfix"></div>
                                <p class="help-block">File must be xlsx, xls, csv.</p>
                                <script type="text/javascript">
                                    $('input[id=excel_upload]').change(function () {
                                        $('#SelectedFileName').val($(this).val());
                                    });
                                </script>
                                <span id="error_excel_upload" class="has-error"></span>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-12">
                                <button type="submit" class="btn btn-success .submit"
                                        data-loading-text="Loading..."><span class="fa fa-save fa-fw"></span> Import
                                </button>
                            </div>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-md-12 import_notice">
                            <p> Please Follow The Instructions While Importing Marks: </p>
                            <ol>
                                <li>At first download excel demo file.</li>
                                <li>Please do not edit or delete heading column.</li>
                                <li>You must have to add all column Marks.</li>
                                <li>Re-Check the mark</li>
                                <li>Select Exam, Class, Section and subject. Re-check the selected items</li>
                                <li>Browse the saved file.</li>
                                <li>Hit "Import" Button and wait untill success message.</li>
                            </ol>
                            <span>
                                *** Double check the information. Double check the Exam Name, Class, Section and Subject name.
                            Import process first check mark exist or not, if exist it will update mark if not exists it will insert new mark.
                                You can change marks from marks marks table. *** </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        function get_sections(val) {
            $("#section_id").empty();
            $.ajax({
                type: 'GET',
                url: 'getSections/' + val,
                success: function (data) {
                    $("#section_id").html(data);
                },
                error: function (result) {
                    $("#modal_data").html("Sorry Cannot Load Data");
                }
            });
        }


        function get_class_subjects(val) {
            $("#subject_id").empty();
            var exam_id = $("#exam_id").val();
            var class_id = $("#class_id").val();
            var section_id = val;
            $.ajax({
                type: 'GET',
                url: 'getSubjects',
                data: {'exam_id': exam_id, 'class_id': class_id, 'section_id': section_id},
                success: function (data) {
                    $("#subject_id").html(data);
                },
                error: function (result) {
                    $("#subject_id").html("Sorry Cannot Load Data");
                }
            });
        }

        $(document).ready(function () {

            $('#loader').hide();

            $('#create').validate({// <- attach '.validate()' to your form
                // Rules for form validation
                rules: {
                    excel_upload: {
                        required: true
                    }
                },
                // Messages for form validation
                messages: {
                    excel_upload: {
                        required: 'Enter file'
                    }
                },
                submitHandler: function (form) {

                    var myData = new FormData($("#create")[0]);
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    myData.append('_token', CSRF_TOKEN);
                    var exam_name = $("#exam_id option:selected").text();
                    var class_name = $("#class_id option:selected").text();
                    var section = $("#section_id option:selected").text();
                    var subject = $("#subject_id option:selected").text();

                    swal({
                        title: "Are you sure?",
                        text: "Please check the information " + " \n Exam Name : " + exam_name
                        + " \n Class : " + class_name
                        + " \n Section : " + section
                        + " \n Subject  : " + subject,
                        type: "warning",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Imports",
                        cancelButtonText: "Cancel"
                    }, function () {
                        $.ajax({
                            url: '{!! route('teacher.importMarks.import') !!}',
                            type: 'POST',
                            data: myData,
                            dataType: 'json',
                            cache: false,
                            processData: false,
                            contentType: false,
                            beforeSend: function () {
                                $('#loader').show();
                                $(".submit").prop('disabled', true); // disable button
                            },
                            success: function (data) {

                                if (data.type === 'success') {
                                    document.getElementById("create").reset();
                                    swal("Done!", data.message, "success");
                                    $(".submit").prop('disabled', false); // disable button

                                } else if (data.type === 'danger') {

                                    if (data.errors) {
                                        $.each(data.errors, function (key, val) {
                                            $('#error_' + key).html(val);
                                        });
                                    }
                                    //   notify_view(data.type, data.message);
                                    swal("Error importing!", data.message, "error");
                                    $(".submit").prop('disabled', false); // disable button
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                swal("Error importing!", "Try again", "error");
                            }
                        });
                    });
                }
                // <- end 'submitHandler' callback
            });                    // <- end '.validate()'

        });
    </script>
@stop
