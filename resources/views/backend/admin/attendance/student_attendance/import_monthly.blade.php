@extends('backend.layouts.master')
@section('title', 'Attendance')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Import Monthly Attendance </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                            <div class="form-group col-md-4 col-sm-12">
                                <label for=""> Select Class </label>
                                <select name="class_id" id="class_id" class="form-control filter"
                                        onchange="get_sections(this.value)" required>
                                    <option value="" selected disabled>Select a class</option>
                                    <option value="all">All</option>
                                    @foreach($stdclass as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for=""> Select Section </label>
                                <select class="form-control" name="section_id" id="section_id" required>
                                    <option value="all">All</option>
                                    <option value="">Select a Section</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for=""> Select Month </label>
                                <select name="month" id="month" class="form-control" required>
                                    <option value="">Select Month</option>
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-12">
                                <label for="excel_upload">Import Students Attendance</label>
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
                </div>
            </div>
        </div>
    </div>

    <script>

        function get_sections(val) {
            if (val != 'all') {
                $("#section_id").empty();
                $.ajax({
                    type: 'GET',
                    url: 'getAllSection/' + val,
                    success: function (data) {
                        $("#section_id").html(data);
                    },
                    error: function (result) {
                        $("#modal_data").html("Sorry Cannot Load Data");
                    }
                });
            }
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
                    var month = $("#month option:selected").text();

                    swal({
                        title: "Are you sure?",
                        text: "Please check the information "
                        + " \n Class : " + class_name
                        + " \n Section : " + section
                        + " \n Month : " + month,
                        type: "warning",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Imports",
                        cancelButtonText: "Cancel"
                    }, function () {
                        $.ajax({
                            url: 'importStdAttendanceMonthly',
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