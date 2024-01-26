@extends('backend.layouts.student_master')
@section('title', 'Optional Subject')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <p class="panel-title"> Optional Subject</p>
                </div>
                <div class="box-body">
                    <div class="row">
                        <form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                            <div id="status"></div>
                            {{method_field('PATCH')}}
                            <div class="form-group col-md-6 col-sm-12">
                                <label for=""> Select Optional Subject </label>
                                <select name="subject_id" id="subject_id" class="form-control" required>
                                    <option value="" selected disabled>Select Optional Subject</option>
                                    @foreach($subjects as $optional)
                                        <option value="{{$optional->id}}"
                                            {{ ( $optional->id == $enroll->subject_id) ? 'selected' : '' }}
                                        >{{$optional->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-12">
                                <button type="submit" class="btn btn-success" id="submit"><span
                                        class="fa fa-save fa-fw"></span> Save
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

        $(document).ready(function () {

            $('#loader').hide();

            $('#edit').validate({// <- attach '.validate()' to your form
                // Rules for form validation
                rules: {
                    subject_id: {
                        required: true
                    },
                    password: {
                        required: true
                    }
                },
                // Messages for form validation
                messages: {
                    subject_id: {
                        required: 'Please Select an optional subject'
                    }
                },
                submitHandler: function (form) {

                    var myData = new FormData($("#edit")[0]);
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    myData.append('_token', CSRF_TOKEN);
                    var subject_name = $("#subject_id option:selected").text();

                    swal({
                        title: "Are you sure?",
                        text: "Please check the information "
                        + " \n Optional Subject : " + subject_name,
                        type: "warning",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Submit",
                        cancelButtonText: "Cancel"
                    }, function () {

                        $.ajax({
                            url: 'optionalSubject',
                            type: 'POST',
                            data: myData,
                            dataType: 'json',
                            cache: false,
                            processData: false,
                            contentType: false,
                            beforeSend: function () {
                                $('#loader').show();
                                $("#submit").prop('disabled', true); // disable button
                            },
                            success: function (data) {
                                if (data.type === 'success') {
                                    swal("Done!", data.message, "success");
                                    $('#loader').hide();
                                    $("#submit").prop('disabled', false); // disable button
                                    $("html, body").animate({scrollTop: 0}, "slow");
                                    $('.has-error').html('');

                                } else if (data.type === 'error') {
                                    $('.has-error').html('');
                                    if (data.errors) {
                                        $.each(data.errors, function (key, val) {
                                            $('#error_' + key).html(val);
                                        });
                                    }
                                    swal("Error!", data.message, "error");
                                    $("#status").html(data.message);
                                    $('#loader').hide();
                                    $("#submit").prop('disabled', false); // disable button

                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                swal("Error!", "Try again", "error");
                            }
                        });
                    });
                }
                // <- end 'submitHandler' callback
            });                    // <- end '.validate()'

        });
    </script>
@endsection
