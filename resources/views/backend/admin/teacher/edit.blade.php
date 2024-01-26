<form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    {{method_field('PATCH')}}
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Teacher Name </label>
        <input type="text" class="form-control" id="name" name="name" value="{{$teacher->name}}"
               placeholder="" required>
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Teacher ID </label>
        <input type="text" class="form-control" id="teacher_code" name="teacher_code" value="{{$teacher->teacher_code}}"
               placeholder="" required>
        <span id="error_teacher_code" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Gender </label>
        <select name="gender" class="form-control">
            <option value="{{$teacher->gender}}">{{$teacher->gender}}</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
        <span id="error_gender" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Religion </label>
        <select name="religion" class="form-control">
            <option value="{{$teacher->religion}}">{{$teacher->religion}}</option>
            <option value="Islam">Islam</option>
            <option value="Hindu">Hindu</option>
            <option value="Buddhist">Buddhist</option>
            <option value="Christian">Christian</option>
            <option value="Others">Others</option>
        </select>
        <span id="error_religion" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Date of Birth </label>
        <input type="text" class="form-control" id="dob" name="dob"
               value="{{$teacher->dob}}"/>
        <span id="error_dob" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Blood Group </label>
        <input type="text" class="form-control" id="blood_group" name="blood_group" value="{{$teacher->blood_group}}"
               placeholder="">
        <span id="error_blood_group" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> School Join Date </label>
        <input type="text" class="form-control" id="doj" name="doj"
               value="{{$teacher->doj}}"/>
        <span id="error_doj" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Qualificaion </label>
        <input type="text" class="form-control" id="qualification" name="qualification" value="{{$teacher->qualification}}"
               placeholder="" required>
        <span id="error_qualification" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Teacher Subject</label>
        <input type="text" class="form-control" id="subject" name="subject" value="{{$teacher->subject}}"
               placeholder="" required>
        <span id="error_subject" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Marital Status </label>
        <select name="marital_status" class="form-control">
            <option value="{{$teacher->marital_status}}">{{$teacher->marital_status}}</option>
            <option value="Single">Single</option>
            <option value="Maried">Maried</option>
        </select>
        <span id="error_marital_status" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Contact </label>
        <input type="text" class="form-control" id="phone" name="phone" value="{{$teacher->phone}}"
               placeholder="" required>
        <span id="error_phone" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Teacher Order </label>
        <input type="text" class="form-control" id="order" name="order" value="{{$teacher->order}}"
               placeholder="">
        <span id="error_order" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Designition </label>
        <input type="text" class="form-control" id="designation" name="designation" value="{{$teacher->designation}}"
               placeholder="">
        <span id="error_designation" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Address </label>
        <input type="text" class="form-control" id="address" name="address" value="{{$teacher->address}}"
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
                   value="{{$teacher->file_path}}" readonly>
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
    <div class="form-group col-md-4">
        <label for=""> Status </label><br/>
        <input type="radio" name="status" class="flat-green"
               value="1" {{ ( $teacher->status == 1 ) ? 'checked' : '' }} /> Active
        <input type="radio" name="status" class="flat-green"
               value="0" {{ ( $teacher->status == 0 ) ? 'checked' : '' }}/> In Active
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12">
        <button type="submit" class="btn btn-success" id="submit"><span class="fa fa-save fa-fw"></span> Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><span
                class="fa fa-times-circle fa-fw"></span> Cancel
        </button>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $('input[type="radio"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
    });
    $(document).ready(function () {

        $('#dob').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });
        $('#doj').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
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
                    required: 'Enter class name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#edit")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'teachers/' + '{{ $teacher->id }}',
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
                            reload_table();
                            notify_view(data.type, data.message);
                            $('#loader').hide();
                            $("#submit").prop('disabled', false); // disable button
                            $("html, body").animate({scrollTop: 0}, "slow");
                            $('#myModal').modal('hide'); // hide bootstrap modal

                        } else if (data.type === 'error') {
                            $('.has-error').html('');
                            if (data.errors) {
                                $.each(data.errors, function (key, val) {
                                    $('#error_' + key).html(val);
                                });
                            }
                            $("#status").html(data.message);
                            $('#loader').hide();
                            $("#submit").prop('disabled', false); // disable button

                        }
                    }
                });
            }
            // <- end 'submitHandler' callback
        });                    // <- end '.validate()'

    });
</script>