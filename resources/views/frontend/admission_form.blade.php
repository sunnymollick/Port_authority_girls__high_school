@extends('frontend.layouts.fullwidth_master')
@section('title', 'Online Admission Form')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h4>Online Admission Form</h4>
            <hr>
        </div>
        <div class="col-md-12">
            <div id="status"></div>
            <form id='create' enctype="multipart/form-data" method="post" accept-charset="utf-8">
                <div id="status"></div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Applicant Name (In English Block Letter)* </label>
                    <input type="text" class="form-control" id="applicant_name_en" name="applicant_name_en" value=""
                           required>
                    <span id="error_applicant_name_en" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Applicant Name (In Bangla)* </label>
                    <input type="text" class="form-control" id="applicant_name_bn" name="applicant_name_bn" value=""
                           required>
                    <span id="error_applicant_name_bn" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Father's Name (In English Block Letter)* </label>
                    <input type="text" class="form-control" id="father_name_en" name="father_name_en" value=""
                           required>
                    <span id="error_father_name_en" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Father's Name (In Bangla)* </label>
                    <input type="text" class="form-control" id="father_name_bn" name="father_name_bn" value=""
                           required>
                    <span id="error_father_name_bn" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Mother's Name (In English Block Letter)* </label>
                    <input type="text" class="form-control" id="mother_name_en" name="mother_name_en" value=""
                           required>
                    <span id="error_mother_name_en" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Mother's Name (In Bangla)* </label>
                    <input type="text" class="form-control" id="mother_name_bn" name="mother_name_bn" value=""
                           required>
                    <span id="error_mother_name_bn" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Father's Qualification* </label>
                    <input type="text" class="form-control" id="father_qualification" name="father_qualification"
                           value=""
                           required>
                    <span id="error_father_qualification" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Mother's Qualification* </label>
                    <input type="text" class="form-control" id="mother_qualification" name="mother_qualification"
                           value=""
                           required>
                    <span id="error_mother_qualification" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Father's Occupation* </label>
                    <input type="text" class="form-control" id="father_occupation" name="father_occupation" value=""
                           required>
                    <span id="error_father_occupation" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Mother's Occupation * </label>
                    <input type="text" class="form-control" id="mother_occupation" name="mother_occupation" value=""
                           required>
                    <span id="error_mother_occupation" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Father's Occupation Post Name (If Job Holder) </label>
                    <input type="text" class="form-control" id="father_occupation_post_name"
                           name="father_occupation_post_name" value="">
                    <span id="error_father_occupation_post_name" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Father's Occupation Company Name (If Job Holder) </label>
                    <input type="text" class="form-control" id="father_occupation_org_name"
                           name="father_occupation_org_name" value="">
                    <span id="error_father_occupation_org_name" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Father's Business Type (If Businessman) </label>
                    <input type="text" class="form-control" id="father_occupation_business_type"
                           name="father_occupation_business_type" value="">
                    <span id="error_father_occupation_business_type" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Father's Yearly Income </label>
                    <input type="text" class="form-control" id="yearly_income"
                           name="yearly_income" value="" required>
                    <span id="error_yearly_income" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Mobile * </label>
                    <input type="text" class="form-control" id="mobile"
                           name="mobile" value="" required>
                    <span id="error_mobile" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Date of Birth </label>
                    <input type="text" class="form-control" id="dob"
                           name="dob" value="" required readonly>
                    <span id="error_dob" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <br/><h5>If parents unavailable then other gurdian information : </h5><br/>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Other Gurdian (If parents unavailable) * </label>
                    <input type="text" class="form-control" id="alternet_gurdian_name"
                           name="alternet_gurdian_name" value="" required>
                    <span id="error_alternet_gurdian_name" class="has-error"></span>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Other Gurdian Phone </label>
                    <input type="text" class="form-control" id="alternet_gurdian_phone"
                           name="alternet_gurdian_phone" value="">
                    <span id="error_alternet_gurdian_phone" class="has-error"></span>
                </div>
                <div class="form-group col-md-5 col-sm-12">
                    <label for=""> Other Gurdian Address </label>
                    <input type="text" class="form-control" id="alternet_gurdian_address"
                           name="alternet_gurdian_address" value="">
                    <span id="error_alternet_gurdian_address" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <br/><h5>Present Address Information : </h5><br/>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Present Village </label>
                    <input type="text" class="form-control" id="present_village"
                           name="present_village" value="" required>
                    <span id="error_present_village" class="has-error"></span>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Present Post Office * </label>
                    <input type="text" class="form-control" id="present_post_office"
                           name="present_post_office" value="" required>
                    <span id="error_present_post_office" class="has-error"></span>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Present Thana </label>
                    <input type="text" class="form-control" id="present_thana"
                           name="present_thana" value="" required>
                    <span id="error_present_thana" class="has-error"></span>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Present District </label>
                    <input type="text" class="form-control" id="present_district"
                           name="present_district" value="" required>
                    <span id="error_present_thana" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <br/><h5>Parmanent Address Information : </h5><br/>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Parmanent Village </label>
                    <input type="text" class="form-control" id="parmanent_village"
                           name="parmanent_village" value="" required>
                    <span id="error_parmanent_village" class="has-error"></span>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Parmanent Post Office * </label>
                    <input type="text" class="form-control" id="parmanent_post_office"
                           name="parmanent_post_office" value="" required>
                    <span id="error_parmanent_post_office" class="has-error"></span>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Parmanent Thana </label>
                    <input type="text" class="form-control" id="parmanent_thana"
                           name="parmanent_thana" value="" required>
                    <span id="error_parmanent_thana" class="has-error"></span>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Parmanent District </label>
                    <input type="text" class="form-control" id="parmanent_district"
                           name="parmanent_district" value="" required>
                    <span id="error_parmanent_district" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Email </label>
                    <input type="text" class="form-control" id="email"
                           name="email" value="">
                    <span id="error_email" class="has-error"></span>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Nationality * </label>
                    <input type="text" class="form-control" id="nationality"
                           name="nationality" value="" required>
                    <span id="error_nationality" class="has-error"></span>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Religion * </label>
                    <select name="religion" class="form-control">
                        <option value="Islam">Islam</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddhist">Buddhist</option>
                        <option value="Christian">Christian</option>
                        <option value="Others">Others</option>
                    </select>
                    <span id="error_religion" class="has-error"></span>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label for=""> Blood Group </label>
                    <input type="text" class="form-control" id="blood_group"
                           name="blood_group" value="">
                    <span id="error_blood_group" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <br/><h5>Is any children already admitted in this School : </h5><br/>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Children Name </label>
                    <input type="text" class="form-control" id="children_name"
                           name="children_name" value="">
                    <span id="error_children_name" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Children Class </label>
                    <input type="text" class="form-control" id="children_class"
                           name="children_class" value="">
                    <span id="error_children_class" class="has-error"></span>
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label for=""> Children Section </label>
                    <input type="text" class="form-control" id="children_section"
                           name="children_section" value="">
                    <span id="error_children_section" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <br/><h5>Which class do you want to admit? : </h5><br/>
                </div>
                <div class="form-group col-md-6 col-sm-12">
                    <select name="admitted_class" id="admitted_class" class="form-control" required
                            onchange="get_sections(this.value)">
                        <option value="" selected disabled>Select a class</option>
                        @foreach($stdclass as $class)
                            <option value="{{$class->id}}">{{$class->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6 col-sm-12">
                    <select class="form-control" name="admitted_section" id="admitted_section" required>
                        <option value="" selected disabled>Select a section</option>
                    </select>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <br/><h5>Which school did you read before? : </h5><br/>
                </div>
                <div class="form-group col-md-6 col-sm-12">
                    <label for=""> Previous School Name </label>
                    <input type="text" class="form-control" id="old_school_name"
                           name="old_school_name" value="">
                    <span id="error_old_school_name" class="has-error"></span>
                </div>
                <div class="form-group col-md-6 col-sm-12">
                    <label for=""> Previous Class </label>
                    <input type="text" class="form-control" id="old_class"
                           name="old_class" value="">
                    <span id="error_old_class" class="has-error"></span>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <label for="photo">Upload Photo</label>
                    <input id="photo" type="file" name="photo" required>
                    <p class="help-block">File must be jpg, png and size less than 2mb. </p>
                    <span id="error_photo" class="has-error"></span>
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
        function get_sections(val) {
            $("#admitted_section").empty();
            $.ajax({
                type: 'GET',
                url: 'getSections/' + val,
                success: function (data) {
                    $("#admitted_section").html(data);
                },
                error: function (result) {
                    $("#admitted_section").html("Sorry Cannot Load Data");
                }
            });
        }


        $(document).ready(function () {

            $('#loader').hide();
            $('#dob').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
                $(this).datepicker('hide');
            });

            $('#create').validate({// <- attach '.validate()' to your form
                // Rules for form validation
                rules: {
                    name: {
                        required: true
                    },
                    photo: {
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
                        url: 'onlineAdmission',
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