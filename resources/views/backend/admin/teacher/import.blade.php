@extends('backend.layouts.master')
@section('title', 'Import Teachers')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Import Teachers
                        <a style="color: #fff" href="{{ asset('assets/documents/teacher_demo.xlsx') }}"
                           class="btn btn-danger">Demo Teachers Excel File</a>
                    </p>

                </div>
                <div class="panel-body">
                    <div class="row">
                        <form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                            <div id="status"></div>
                            <div class="col-md-12">
                                <label for="excel_upload">Import Teachers</label>
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
                            <p> Please Follow The Instructions While Importing Teachers: </p>
                            <ol>
                                <li>At first download excel demo file.</li>
                                <li>Please do not edit or delete heading column.</li>
                                <li>You must have to add unique teacher code , email address</li>
                                <li>Browse the saved file.</li>
                                <li>Hit "Import" Button and wait untill success message.</li>
                            </ol>
                            <span>
                                ***This System Keeps Track Of Duplication In Email and Teacher Code. So Please Enter
                                Unique Email ID
                                and Teacher Code For Every Teachers. </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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

                    swal({
                        title: "Are you sure",
                        text: "Please double check the information ",
                        type: "warning",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Imports",
                        cancelButtonText: "Cancel"
                    }, function () {
                        $.ajax({
                            url: '{!! route('admin.importTeachers.import') !!}',
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
