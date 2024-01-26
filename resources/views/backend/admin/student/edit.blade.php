<form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    {{method_field('PATCH')}}
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Student's Name </label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $student->name }}"
               placeholder="" required>
        <input type="hidden" name="enroll_id" value="{{$enroll->id}}">
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Student Code </label>
        <input type="text" class="form-control" id="std_code" name="std_code" value="{{ $student->std_code }}"
               placeholder="" required>
        <span id="error_std_code" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-3 col-sm-12">
        <select name="class_id" id="class_id" class="form-control" required
                onchange="get_class_sections(this.value)">
            <option value="" selected disabled>Select a class</option>
            @foreach($stdclass as $class)
                <option value="{{$class->id}}"
                    {{ ( $class->id == $enroll->class_id) ? 'selected' : '' }} >{{$class->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <select class="form-control" name="section_id" id="class_section_id" required>
            <option value="{{ $enroll->section->id }}">{{ $enroll->section->name }}</option>
        </select>
    </div>
    <div class="form-group col-md-3 col-sm-12">
       <input type="text" class="form-control" id="subject_id" name="subject_id" value="{{ $enroll->subject ? $enroll->subject->id : '' }}"
               placeholder="Optional Subject">    
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <input type="text" class="form-control" id="roll" name="roll" value="{{ $enroll->roll }}"
               placeholder="Roll" required>
        <span id="error_roll" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-3">
        <select name="gender" id="gender" class="form-control" required>
            <option value="{{ $student->gender }}">{{ $student->gender }}</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Others">Others</option>
        </select>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <select name="religion" class="form-control">
            <option value="{{ $student->religion }}">{{ $student->religion }}</option>
            <option value="Islam">Islam</option>
            <option value="Hindu">Hindu</option>
            <option value="Buddhist">Buddhist</option>
            <option value="Christian">Christian</option>
            <option value="Others">Others</option>
        </select>
        <span id="error_religion" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <input type="text" class="form-control" id="dob" name="dob"
               value="{{ $student->dob }}" placeholder="Date of Birth"/>
        <span id="error_dob" class="has-error"></span>
    </div>
    <div class="form-group col-md-3">
        <select name="blood_group" id="blood_group" class="form-control">
            <option value="{{ $student->blood_group }}">{{ $student->blood_group }}</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
        </select>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Phone </label>
        <input type="text" class="form-control" id="phone" name="phone" value="{{ $student->phone }}"
               placeholder="" required>
        <span id="error_phone" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Email </label>
        <input type="text" class="form-control" id="email" name="email" value="{{ $student->email }}"
               placeholder="">
        <span id="error_email" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Address </label>
        <input type="text" class="form-control" id="address" name="address" value="{{ $student->address }}"
               placeholder="">
        <span id="error_address" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-8">
        <label for="photo">Upload Image</label>
        <input id="photo" type="file" name="photo" style="display:none">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn btn-success" onclick="$('input[id=photo]').click();">Browse</a>
            </div><!-- /btn-group -->
            <input type="text" name="SelectedFileName" class="form-control" id="SelectedFileName"
                   value="{{ $student->photo }}" readonly>
        </div>
        <div class="clearfix"></div>
        <p class="help-block">File must be jpg, jpeg, png.</p>
        <script type="text/javascript">
            $('input[id=photo]').change(function () {
                $('#SelectedFileName').val($(this).val());
            });
        </script>
        <span id="error_photo" class="has-error"></span>
    </div>
    <div class="form-group col-md-4">
        <label for=""> Status </label><br/>
        <input type="radio" name="status" class="flat-green"
               value="1" {{ ( $student->status == 1 ) ? 'checked' : '' }} /> Active
        <input type="radio" name="status" class="flat-green"
               value="0" {{ ( $student->status == 0 ) ? 'checked' : '' }}/> In Active
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12">
        <button type="submit" class="btn btn-success submit"
                data-loading-text="Loading..."><span class="fa fa-save fa-fw"></span> Save
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><span
                class="fa fa-times-circle fa-fw"></span> Cancel
        </button>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    function get_class_sections(val) {
        $("#class_section_id").empty();
        get_optional_subject(val)
        $.ajax({
            type: 'GET',
            url: 'getSections/' + val,
            success: function (data) {
                $("#class_section_id").html(data);
            },
            error: function (result) {
                $("#class_section_id").html("Sorry Cannot Load Data");
            }
        });
    }

    function get_optional_subject(val) {
        $("#subject_id").empty();
        $.ajax({
            type: 'GET',
            url: 'getOptionalSubjects/' + val,
            success: function (data) {
                $("#subject_id").html(data);
            },
            error: function (result) {
                $("#subject_id").html("Sorry Cannot Load Data");
            }
        });
    }
    $('input[type="radio"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
    });
    $(document).ready(function () {
        $('#dob').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });
        $('#loader').hide();
        $('#edit').validate({// <- attach '.validate()' to your form
            // Rules for form validation
            rules: {
                name: {
                    required: true
                },
                phone: {
                    required: true,
                    number: true
                }
            },
            // Messages for form validation
            messages: {
                name: {
                    required: 'Enter book name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#edit")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                var class_name = $("#class_id option:selected").text();
                var section = $("#section_id option:selected").text();

                myData.append('class_name', class_name);
                myData.append('section_name', section);

                $.ajax({
                    url: 'students/' + '{{ $student->id }}',
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
                            getStudents();
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