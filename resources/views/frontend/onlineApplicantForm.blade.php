@extends('frontend.layouts.fullwidth_master')
@section('title', 'Online Admission Form')
@section('content')
    <div class="row">
        <div class="col-md-12 text-center">
            <h2>Admission Form</h2>
            <h4>To be filled by Guardian</h4>
            <hr>
        </div>
        <div class="col-md-12">
            <div id="status"></div>
            <form id='create' enctype="multipart/form-data" method="post" accept-charset="utf-8">
                <div id="status"></div>
                <div class="form-row">
                    <div class="form-row">
                        <div class="form-group col-md-6 col-sm-12">
                            <label for="enrollment_class">The class that student wants to be admitted</label>
                            <select name="enrollment_class" id="enrollment_class" class="form-control select" required>
                                <option value="" selected disabled>Select Class</option>
                                <option value="Six">Six</option>
                                <option value="Seven">Seven</option>
                                <option value="Eight">Eight</option>
                                <option value="Nine">Nine</option>  
                            </select>
                            <span id="error_enrollment_class" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <label for="phone_no">Mobile No</label>
                            <input type="text" class="form-control" id="phone_no" name="phone_no" value="" placeholder="">
                            <span id="error_phone_no" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-12 col-sm-12">
                            <label for="girl_name">Student Name</label>
                            <input type="text" class="form-control" id="girl_name" name="girl_name" value="" placeholder="" required>
                            <span id="error_girl_name" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <label for="father_name">Father's name</label>
                            <input type="text" class="form-control" id="father_name" name="father_name" value="" placeholder="" required>
                            <span id="error_father_name" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <label for="mother_name">Mother's Name</label>
                            <input type="text" class="form-control" id="mother_name" name="mother_name" value="" placeholder="" required>
                            <span id="error_mother_name" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <label for="vill">Village</label>
                            <input type="text" class="form-control" id="vill" name="vill" value="" placeholder="">
                            <span id="error_vill" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <label for="po">Post Office</label>
                            <input type="text" class="form-control" id="po" name="po" value="" placeholder="">
                            <span id="error_po" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <label for="ps">Police Station</label>
                            <input type="text" class="form-control" id="ps" name="ps" value="" placeholder="">
                            <span id="error_ps" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <label for="dist">District</label>
                            <input type="text" class="form-control" id="dist" name="dist" value="" placeholder="">
                            <span id="error_dist" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-12 col-sm-12">
                            <label for="depended_on_port_authority">Whether the student is dependent on an officer/employee of the Chittagong Port Authority
                            </label>
                            <input type="text" class="form-control" id="depended_on_port_authority" name="depended_on_port_authority" value="" placeholder="">
                            <small id="emailHelp" class="form-text text-muted">Note: Dependent means the son of the officer/employee and fatherless brother or half brother below 21 years of age if they are wholly dependent on the officer/employee and the father is not alive.</small>
        
                            <span id="error_depended_on_port_authority" class="has-error"></span>
                        </div>
        
                        <div class="border border-info rounded p-3 mb-3 w-100">
                            <h4>Student's Date-of-Birth</h4>
        
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="dob">In Numbers</label>
                                    <input type="text" class="form-control" id="dob" name="dob" value="" placeholder="">
                                    <span id="error_dob" class="has-error"></span>
                                </div> 
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="dob_text">In Text</label>
                                    <input type="text" class="form-control" id="dob_text" name="dob_text" value="" placeholder="">
                                    <span id="error_dob_text" class="has-error"></span>
                                </div>
                            </div>                   
                        </div>
                        <div class="border border-info rounded p-3 mb-3 w-100">
                            <h4>The student is dependent on the guardian</h4>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="guardian_name">His/Her name</label>
                                    <input type="text" class="form-control" id="guardian_name" name="guardian_name" value="" placeholder="">
                                    <span id="error_guardian_name" class="has-error"></span>
                                </div> 
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="guardian_father_name">Name of Guardian Father's Name</label>
                                    <input type="text" class="form-control" id="guardian_father_name" name="guardian_father_name" value="" placeholder="">
                                    <span id="error_guardian_father_name" class="has-error"></span>
                                </div> 
                                <div class="form-group col-md-12 col-sm-12">
                                    <label for="guardian_present_address">Guardian present Address</label>
                                    <textarea type="text" class="form-control" id="guardian_present_address" name="guardian_present_address" value="" placeholder=""></textarea>
                                    <span id="error_guardian_present_address" class="has-error"></span>
                                </div>
        
                                <div class="form-group col-md-12 col-sm-12">
                                    <label for="relation_with_guardian">Student's relationship with guardian</label>
                                    <input type="text" class="form-control" id="relation_with_guardian" name="relation_with_guardian" value="" placeholder="">
                                    <span id="error_relation_with_guardian" class="has-error"></span>
                                </div>
        
                                <div class="form-group col-md-12 col-sm-12">
                                    <label for="guardian_work_address">Address of work</label>
                                    <input type="text" class="form-control" id="guardian_work_address" name="guardian_work_address" value="" placeholder="">
                                    <span id="error_guardian_work_address" class="has-error"></span>
                                </div> 
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="guardian_work_designation">Work Designation</label>
                                    <input type="text" class="form-control" id="guardian_work_designation" name="guardian_work_designation" value="" placeholder="">
                                    <span id="error_guardian_work_designation" class="has-error"></span>
                                </div> 
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="guardian_salary_scale">Pay scale</label>
                                    <input type="text" class="form-control" id="guardian_salary_scale" name="guardian_salary_scale" value="" placeholder="">
                                    <span id="error_guardian_salary_scale" class="has-error"></span>
                                </div> 
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="guardian_monthly_salary">Monthly Basic Salary</label>
                                    <input type="text" class="form-control" id="guardian_monthly_salary" name="guardian_monthly_salary" value="" placeholder="">
                                    <span id="error_guardian_monthly_salary" class="has-error"></span>
                                </div> 
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="guardian_salary_next_increment_date">Next date of annual pay increment</label>
                                    <input type="text" class="form-control" id="guardian_salary_next_increment_date" name="guardian_salary_next_increment_date" value="" placeholder="">
                                    <span id="error_guardian_salary_next_increment_date" class="has-error"></span>
                                </div>
                            </div>
                        </div>
                            <div class="form-group col-md-12 col-sm-12">
                                <label for="nationality">Nationality</label>
                                <input type="text" class="form-control" id="nationality" name="nationality" value="" placeholder="">
                                <span id="error_nationality" class="has-error"></span>
                            </div>
                            <div class="form-group col-md-12 col-sm-12">
                                <label for="speciality">Student's special qualities (sports, music, scouting etc. certificate to be attached)</label>
                                <input type="text" class="form-control" id="speciality" name="speciality" value="" placeholder="">
                                <span id="error_speciality" class="has-error"></span>
                            </div>
        
                            <div class="border border-info rounded p-3 mb-3 w-100">
                                <h4>Which school student came from</h4>
                                <div class="form-row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="school_name">Previous School Name</label>
                                        <input type="text" class="form-control" id="school_name" name="school_name" value="" placeholder="">
                                        <span id="error_school_name" class="has-error"></span>
                                    </div> 
                                    
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="school_vill">Village</label>
                                        <input type="text" class="form-control" id="school_vill" name="school_vill" value="" placeholder="">
                                        <span id="error_school_vill" class="has-error"></span>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="school_po">Post Office</label>
                                        <input type="text" class="form-control" id="school_po" name="school_po" value="" placeholder="">
                                        <span id="error_school_po" class="has-error"></span>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="school_ps">Police Station</label>
                                        <input type="text" class="form-control" id="school_ps" name="school_ps" value="" placeholder="">
                                        <span id="error_school_ps" class="has-error"></span>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="school_dist">District</label>
                                        <input type="text" class="form-control" id="school_dist" name="school_dist" value="" placeholder="">
                                        <span id="error_school_dist" class="has-error"></span>
                                    </div>
        
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="is_admit_by_tc">Whether admission through clearance</label>
                                        <input type="text" class="form-control" id="is_admit_by_tc" name="is_admit_by_tc" value="" placeholder="">
                                        <span id="error_is_admit_by_tc" class="has-error"></span>
                                    </div>
        
                                </div>
                                
                            </div>
                        </div>
                
                    <div class="clearfix"></div>
                    
                    <div class="text-primary" style="margin: 20px">
                        <h4>For Port, Please bring the following documents along with the filled form.</h4>
                        <ul>
                            <li>Photocopy of Port ID Card.</li>
                            <li>Photocopy of first and last page of medical book.</li>
                            <li>Photocopy of NID Card (Father-Mother)</li>
                        </ul>
                    </div>

                    <div class="text-success" style="margin: 20px">
                        <h4>For Outsider, Please bring the following documents along with the filled form.</h4>
                        <ul>
                            <li>Photocopy of NID Card (Father-Mother)</li>
                            <li>Photo – 2 copies (recently taken)</li>
                            <li>Birth Certificate Photocopy (Online English)</li>
                        </ul>
                    </div>

                    <p class="text-danger">This form is not editable, please give your correct information carefully.</p>
                
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-success button-submit"
                                data-loading-text="Loading..."><span class="fa fa-save fa-fw"></span> Save
                        </button>
                    </div>
                </div>
                
            </form>
        </div>
    </div>

    <style>
        .row{
            margin: 0;
            padding: 0;
        }
        .border{
            padding: 2px;
        }
    </style>

    <script>

        $(document).ready(function () {

            $('#loader').hide();
            $('#dob').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
                $(this).datepicker('hide');
            });

            $('#create').validate({// <- attach '.validate()' to your form
                // Rules for form validation
                rules: {
                    enrollment_class:{
                        required: true
                    },
                    girl_name: {
                        required: true
                    },
                    father_name: {
                        required: true
                    },
                    mother_name: {
                        required: true
                    }
                },
                // Messages for form validation
                messages: {
                    name: {
                        required: 'Enter girl name'
                    }
                },
                submitHandler: function (form) {

                    var myData = new FormData($("#create")[0]);
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    myData.append('_token', CSRF_TOKEN);

                            $.ajax({
                                url: 'onlineApplicantFormSubmit',
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