<form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    {{method_field('PATCH')}}
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Applicant Name (In English Block Letter)* </label>
        <input type="text" class="form-control" id="applicant_name_en" name="applicant_name_en"
               value="{{ $admissionApplication->applicant_name_en }}"
               required>
        <span id="error_applicant_name_en" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Applicant Name (In Bangla)* </label>
        <input type="text" class="form-control" id="applicant_name_bn" name="applicant_name_bn"
               value="{{ $admissionApplication->applicant_name_bn }}"
               required>
        <span id="error_applicant_name_bn" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Father's Name (In English Block Letter)* </label>
        <input type="text" class="form-control" id="father_name_en" name="father_name_en"
               value="{{ $admissionApplication->father_name_en }}"
               required>
        <span id="error_father_name_en" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Father's Name (In Bangla)* </label>
        <input type="text" class="form-control" id="father_name_bn" name="father_name_bn"
               value="{{ $admissionApplication->father_name_bn }}"
               required>
        <span id="error_father_name_bn" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Mother's Name (In English Block Letter)* </label>
        <input type="text" class="form-control" id="mother_name_en" name="mother_name_en"
               value="{{ $admissionApplication->mother_name_en }}"
               required>
        <span id="error_mother_name_en" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Mother's Name (In Bangla)* </label>
        <input type="text" class="form-control" id="mother_name_bn" name="mother_name_bn"
               value="{{ $admissionApplication->mother_name_bn }}"
               required>
        <span id="error_mother_name_bn" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Father's Qualification* </label>
        <input type="text" class="form-control" id="father_qualification" name="father_qualification"
               value="{{ $admissionApplication->father_qualification }}"
               required>
        <span id="error_father_qualification" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Mother's Qualification* </label>
        <input type="text" class="form-control" id="mother_qualification" name="mother_qualification"
               value="{{ $admissionApplication->mother_qualification }}"
               required>
        <span id="error_mother_qualification" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Father's Occupation* </label>
        <input type="text" class="form-control" id="father_occupation" name="father_occupation"
               value="{{ $admissionApplication->father_occupation }}"
               required>
        <span id="error_father_occupation" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Mother's Occupation * </label>
        <input type="text" class="form-control" id="mother_occupation" name="mother_occupation"
               value="{{ $admissionApplication->mother_occupation }}"
               required>
        <span id="error_mother_occupation" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Father's Occupation Post Name (If Job Holder) </label>
        <input type="text" class="form-control" id="father_occupation_post_name"
               name="father_occupation_post_name" value="{{ $admissionApplication->father_occupation_post_name }}">
        <span id="error_father_occupation_post_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Father's Occupation Company Name (If Job Holder) </label>
        <input type="text" class="form-control" id="father_occupation_org_name"
               name="father_occupation_org_name" value="{{ $admissionApplication->father_occupation_org_name }}">
        <span id="error_father_occupation_org_name" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Father's Business Type (If Businessman) </label>
        <input type="text" class="form-control" id="father_occupation_business_type"
               name="father_occupation_business_type"
               value="{{ $admissionApplication->father_occupation_business_type }}">
        <span id="error_father_occupation_business_type" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Father's Yearly Income </label>
        <input type="text" class="form-control" id="yearly_income"
               name="yearly_income" value="{{ $admissionApplication->yearly_income }}" required>
        <span id="error_yearly_income" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Mobile * </label>
        <input type="text" class="form-control" id="mobile"
               name="mobile" value="{{ $admissionApplication->mobile }}" required>
        <span id="error_mobile" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Date of Birth </label>
        <input type="text" class="form-control" id="dob"
               name="dob" value="{{ $admissionApplication->dob }}" required readonly>
        <span id="error_dob" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <br/><h5>If parents unavailable then other gurdian information : </h5><br/>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Other Gurdian (If parents unavailable) * </label>
        <input type="text" class="form-control" id="alternet_gurdian_name"
               name="alternet_gurdian_name" value="{{ $admissionApplication->alternet_gurdian_name }}" required>
        <span id="error_alternet_gurdian_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Other Gurdian Phone </label>
        <input type="text" class="form-control" id="alternet_gurdian_phone"
               name="alternet_gurdian_phone" value="{{ $admissionApplication->alternet_gurdian_phone }}">
        <span id="error_alternet_gurdian_phone" class="has-error"></span>
    </div>
    <div class="form-group col-md-5 col-sm-12">
        <label for=""> Other Gurdian Address </label>
        <input type="text" class="form-control" id="alternet_gurdian_address"
               name="alternet_gurdian_address" value="{{ $admissionApplication->alternet_gurdian_address }}">
        <span id="error_alternet_gurdian_address" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <br/><h5>Present Address Information : </h5><br/>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Present Village </label>
        <input type="text" class="form-control" id="present_village"
               name="present_village" value="{{ $admissionApplication->present_village }}" required>
        <span id="error_present_village" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Present Post Office * </label>
        <input type="text" class="form-control" id="present_post_office"
               name="present_post_office" value="{{ $admissionApplication->present_post_office }}" required>
        <span id="error_present_post_office" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Present Thana </label>
        <input type="text" class="form-control" id="present_thana"
               name="present_thana" value="{{ $admissionApplication->present_thana }}" required>
        <span id="error_present_thana" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Present District </label>
        <input type="text" class="form-control" id="present_district"
               name="present_district" value="{{ $admissionApplication->present_district }}" required>
        <span id="error_present_thana" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <br/><h5>Parmanent Address Information : </h5><br/>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Parmanent Village </label>
        <input type="text" class="form-control" id="parmanent_village"
               name="parmanent_village" value="{{ $admissionApplication->parmanent_village }}" required>
        <span id="error_parmanent_village" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Parmanent Post Office * </label>
        <input type="text" class="form-control" id="parmanent_post_office"
               name="parmanent_post_office" value="{{ $admissionApplication->parmanent_post_office }}" required>
        <span id="error_parmanent_post_office" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Parmanent Thana </label>
        <input type="text" class="form-control" id="parmanent_thana"
               name="parmanent_thana" value="{{ $admissionApplication->parmanent_thana }}" required>
        <span id="error_parmanent_thana" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Parmanent District </label>
        <input type="text" class="form-control" id="parmanent_district"
               name="parmanent_district" value="{{ $admissionApplication->parmanent_district }}" required>
        <span id="error_parmanent_district" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Email </label>
        <input type="text" class="form-control" id="email"
               name="email" value="{{ $admissionApplication->email }}">
        <span id="error_email" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Nationality * </label>
        <input type="text" class="form-control" id="nationality"
               name="nationality" value="{{ $admissionApplication->nationality }}" required>
        <span id="error_nationality" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Religion * </label>
        <select name="religion" class="form-control">
            <option value="{{ $admissionApplication->nationality }}"
                    selected>{{ $admissionApplication->nationality }}</option>
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
               name="blood_group" value="{{ $admissionApplication->blood_group }}">
        <span id="error_blood_group" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <br/><h5>Is any children already admitted in this School : </h5><br/>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Children Name </label>
        <input type="text" class="form-control" id="children_name"
               name="children_name" value="{{ $admissionApplication->children_name }}">
        <span id="error_children_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Children Class </label>
        <input type="text" class="form-control" id="children_class"
               name="children_class" value="{{ $admissionApplication->children_class }}">
        <span id="error_children_class" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Children Section </label>
        <input type="text" class="form-control" id="children_section"
               name="children_section" value="{{ $admissionApplication->children_section }}">
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
                <option value="{{$class->id}}"
                    {{$class->id == $admissionApplication->admitted_class ? 'selected':''}}>{{$class->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <select class="form-control" name="admitted_section" id="admitted_section" required>
            <option value="" disabled>Select a section</option>
            <option value="{{$admissionApplication->children_section}}"
                    selected="">{{$admissionApplication->section->name}}</option>
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <br/><h5>Which school did you read before? : </h5><br/>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Previous School Name </label>
        <input type="text" class="form-control" id="old_school_name"
               name="old_school_name" value="{{ $admissionApplication->old_school_name }}">
        <span id="error_old_school_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Previous Class </label>
        <input type="text" class="form-control" id="old_class"
               name="old_class" value="{{ $admissionApplication->old_class }}">
        <span id="error_old_class" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4">
        <label for=""> Status </label><br/>
        <input type="radio" name="status" class="flat-green"
               value="2" {{ ( $admissionApplication->status == 2 ) ? 'checked' : '' }} /> Pending
        <input type="radio" name="status" class="flat-green"
               value="1" {{ ( $admissionApplication->status == 1 ) ? 'checked' : '' }}/> Selected
        <input type="radio" name="status" class="flat-green"
               value="0" {{ ( $admissionApplication->status == 0 ) ? 'checked' : '' }}/> Cancelled
    </div>
    <div class="col-md-8">
        <label for="photo">Upload Image</label>
        <input id="photo" type="file" name="photo" style="display:none">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn btn-success" onclick="$('input[id=photo]').click();">Browse</a>
            </div><!-- /btn-group -->
            <input type="text" name="SelectedFileName" class="form-control" id="SelectedFileName"
                   value="{{$admissionApplication->file_path}}" readonly>
        </div>
        <div class="clearfix"></div>
        <p class="help-block">File must be jpg, jpeg, png.</p>
        <span id="error_photo" class="has-error"></span>
        <script type="text/javascript">
            $('input[id=photo]').change(function () {
                $('#SelectedFileName').val($(this).val());
            });
        </script>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12">
        <button type="submit" class="btn btn-success submit"><span class="fa fa-save fa-fw"></span> Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><span
                class="fa fa-times-circle fa-fw"></span> Cancel
        </button>
    </div>
    <div class="clearfix"></div>
</form>
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

        $('#dob').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });

        $('input[type="radio"].flat-green').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });

        $('#loader').hide();
        $('#edit').validate({// <- attach '.validate()' to your form
            // Rules for form validation
            rules: {
                name: {
                    required: true
                }
            },
            // Messages for form validation
            messages: {
                name: {
                    required: 'Enter class room name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#edit")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'admissionApplication/' + '{{ $admissionApplication->id }}',
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
                            reload_table();
                            notify_view(data.type, data.message);
                            $('#loader').hide();
                            $(".submit").prop('disabled', false); // disable button
                            $("html, body").animate({scrollTop: 0}, "slow");
                            $('#myModal').modal('hide'); // hide bootstrap modal

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