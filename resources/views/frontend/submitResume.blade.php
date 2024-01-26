@extends('frontend.layouts.right_master')
@section('title', 'Submit Resume')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h4>Submit Your Resume</h4>
            <hr>
        </div>
        <div class="col-md-12">
            <div id="status"></div>
            <form id='create' class="comment-form --contact" action="" enctype="multipart/form-data" method="post"
                  accept-charset="utf-8">
                <div class="col-md-6 col-sm-12">
                    <input type="text" name="name" placeholder="Your Name" required>
                </div>
                <div class="col-md-6 col-sm-12">
                    <input type="text" name="email" placeholder="Your Email" required>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-6 col-sm-12">
                    <input type="text" name="mobile" placeholder="Mobile" required>
                </div>
                <div class="col-md-6 col-sm-12">
                    <input type="text" placeholder="Job Position" name="job_position" required>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <textarea placeholder="Cover Letter" name="cover_letter" rows="5"></textarea>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <label for="resume">Upload Resume</label>
                    <input id="resume" type="file" name="resume" required>
                    <p class="help-block">File must be doc, docx, pdf and size less than 1mb. Photo and Signature must
                        be attached with
                        resume</p>
                    <span id="error_resume" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <button class="site-btn submit">SUBMIT</button>
                    <img id="loader" src="{{asset('assets/images/loadingg.gif')}}" width="20px">
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            $('#loader').hide();

            $('#create').validate({// <- attach '.validate()' to your form
                // Rules for form validation
                rules: {
                    name: {
                        required: true
                    },
                    resume: {
                        required: true
                    }
                },
                // Messages for form validation
                messages: {
                    name: {
                        required: 'Enter your name'
                    }
                },
                submitHandler: function (form) {

                    var myData = new FormData($("#create")[0]);
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    myData.append('_token', CSRF_TOKEN);

                    $.ajax({
                        url: 'submitResume',
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
                                $('#loader').hide();
                                $(".submit").prop('disabled', false); // disable button
                                $("html, body").animate({scrollTop: 0}, "slow");
                                $('#status').html(data.message); // hide bootstrap modal
                                document.getElementById("create").reset();

                            } else if (data.type === 'error') {
                                if (data.errors) {
                                    $.each(data.errors, function (key, val) {
                                        $('#error_' + key).html(val);
                                    });
                                }
                                $("#status").html(data.message);
                                $('#loader').hide();
                                $(".submit").prop('disabled', false); // disable button

                            }
                        }
                    });
                }
                // <- end 'submitHandler' callback
            });                    // <- end '.validate()'

        });
    </script>
@endsection