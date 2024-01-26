<form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Father's Name </label>
        <input type="text" class="form-control" id="father_name" name="father_name" value=""
               placeholder="" required>
        <span id="error_father_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Mother's Name </label>
        <input type="text" class="form-control" id="mother_name" name="mother_name" value=""
               placeholder="" required>
        <span id="error_mother_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-2">
        <label for="gender">Gender</label>
        <select name="gender" id="gender" class="form-control" required>
            <option value="">Select a gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="others">Others</option>
        </select>
    </div>
    <div class="form-group col-md-2">
        <label for="blood_group">Blood group</label>
        <select name="blood_group" id="blood_group" class="form-control">
            <option value="">Blood group</option>
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
    <div class="clearfix"></div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Parent Code </label>
        <input type="text" class="form-control" id="parent_code" name="parent_code" value=""
               placeholder="" required>
        <span id="error_parent_code" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Phone </label>
        <input type="text" class="form-control" id="phone" name="phone" value=""
               placeholder="" required>
        <span id="error_phone" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Profession </label>
        <input type="text" class="form-control" id="profession" name="profession" value=""
               placeholder="">
        <span id="error_profession" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Email </label>
        <input type="text" class="form-control" id="email" name="email" value=""
               placeholder="" >
        <span id="error_email" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12 col-sm-12">
        <label for=""> Address </label>
        <input type="text" class="form-control" id="address" name="address" value=""
               placeholder="">
        <span id="error_address" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <label for="photo">Upload Image</label>
        <input id="photo" type="file" name="photo" style="display:none">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn btn-success" onclick="$('input[id=photo]').click();">Browse</a>
            </div><!-- /btn-group -->
            <input type="text" name="SelectedFileName" class="form-control" id="SelectedFileName"
                   value="" readonly>
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
    <div class="clearfix"></div>
    <div class="form-group col-md-12">
        <button type="submit" class="btn btn-success button-submit"
                data-loading-text="Loading..."><span class="fa fa-save fa-fw"></span> Save
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><span
                class="fa fa-times-circle fa-fw"></span> Cancel
        </button>
    </div>
    <div class="clearfix"></div>
</form>
<div class="row">
    <div class="col-md-12 import_notice">
        <p> Please Follow The Instructions While Adding Parent: </p>
        <ol>
            <li>Parent code must be same as student code.Take Student code from student table. If student not insterted use this parent code while inserting the student.</li>
            <li>Email id must be unique</li>
        </ol>
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

                var myData = new FormData($("#create")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'parents',
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