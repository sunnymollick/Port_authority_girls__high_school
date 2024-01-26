@extends('backend.layouts.master')
@section('title', 'Import Students')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Import Students
                        <a style="color: #fff" href="{{ asset('assets/documents/student_import_file.xlsx') }}"
                           class="btn btn-danger">Demo Students Excel File</a>
                    </p>

                </div>
                <div class="panel-body">
                    <div class="row">
                        <form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                            <div id="status"></div>
                            <div class="form-group col-md-6 col-sm-12">
                                <select name="class_id" id="class_id" class="form-control" required
                                        onchange="get_sections(this.value)">
                                    <option value="" selected disabled>Select a class</option>
                                    @foreach($stdclass as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <select class="form-control" name="section_id" id="section_id" required>
                                    <option value="">Select a section</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-12">
                                <label for="excel_upload">Import Students</label>
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
                            <p> Please Follow The Instructions While Importing Students: </p>
                            <ol>
                                <li>Double check the running session. you can change running session from setting menu.
                                </li>
                                <li>At first download excel demo file.</li>
                                <li>Please do not edit or delete heading column.</li>
                                <li>You must have to add unique student code, email address For Every Students.</li>
                                <li>Re-Check the student optional subject id. Get subject id from subject table.</li>
                                <li>Browse the saved file.</li>
                                <li>Hit "Import" Button and wait untill success message.</li>
                            </ol>
                            <span>
                                *** Double check the information. Double check the Class and Section.
                            Import process first check student exist or not, if exist it will update student basic information(But Will not update enrollment like class, section) if not exists it will insert new student with new enrollment.
                                You can change enrollment information one by one at students table. *** </span>
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

                    var class_name = $("#class_id option:selected").text();
                    var section = $("#section_id option:selected").text();
                    var running_session = "{{config('running_session')}}";

                    myData.append('class_name', class_name);
                    myData.append('section_name', section);

                    swal({
                        title: "Are you sure?",
                        text: "Please check the information "
                        + " \n Class : " + class_name
                        + " \n Section : " + section
                        + " \n Running Session : " + running_session,
                        type: "warning",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Imports",
                        cancelButtonText: "Cancel"
                    }, function () {
                        $.ajax({
                            url: '{!! route('admin.importStudents.import') !!}',
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

                                } else if (data.type === 'error') {

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
