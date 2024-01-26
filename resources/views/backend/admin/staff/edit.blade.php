<form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    {{method_field('PATCH')}}
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Staff Name </label>
        <input type="text" class="form-control" id="name" name="name" value="{{$staff->name}}"
               placeholder="" required>
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Gender </label>
        <select name="gender" class="form-control">
            <option value="{{$staff->gender}}">{{$staff->gender}}</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
        <span id="error_gender" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Religion </label>
        <select name="religion" class="form-control">
            <option value="{{$staff->religion}}">{{$staff->religion}}</option>
            <option value="Islam">Islam</option>
            <option value="Hindu">Hindu</option>
            <option value="Buddhist">Buddhist</option>
            <option value="Christian">Christian</option>
            <option value="Others">Others</option>
        </select>
        <span id="error_religion" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Staff ID </label>
        <input type="text" class="form-control" id="staff_code" name="staff_code" value="{{$staff->staff_code}}"
               placeholder="" required>
        <span id="error_staff_code" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Qualification </label>
        <input type="text" class="form-control" id="qualification" name="qualification"
               value="{{$staff->qualification}}"
               placeholder="">
        <span id="error_qualification" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Join Date </label>
        <input type="text" class="form-control" id="doj" name="doj"
               value="{{$staff->doj}}"/>
        <span id="error_doj" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Contact </label>
        <input type="text" class="form-control" id="phone" name="phone" value="{{$staff->phone}}"
               placeholder="" required>
        <span id="error_phone" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Email </label>
        <input type="text" class="form-control" id="email" name="email" value="{{$staff->email}}"
               placeholder="">
        <span id="error_email" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Designition </label>
        <input type="text" class="form-control" id="designation" name="designation" value="{{$staff->designation}}"
               placeholder="">
        <span id="error_designation" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Address </label>
        <input type="text" class="form-control" id="address" name="address" value="{{$staff->address}}"
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
                   value="{{$staff->file_path}}" readonly>
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
               value="1" {{ ( $staff->status == 1 ) ? 'checked' : '' }} /> Active
        <input type="radio" name="status" class="flat-green"
               value="0" {{ ( $staff->status == 0 ) ? 'checked' : '' }}/> In Active
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
                    url: 'staffs/' + '{{ $staff->id }}',
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