<form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    <div class="form-group col-md-5 col-sm-12">
        <label for=""> Subject Name </label>
        <input type="text" class="form-control" id="name" name="name" value=""
               placeholder="" required>
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Class </label>
        <select name="class_id" class="form-control filter" required>
            <option value="" selected disabled>Select Class</option>
            @foreach($stdclass as $class)
                <option value="{{$class->id}}">{{$class->name}}</option>
            @endforeach
        </select>
        <span id="error_class_id" class="has-error"></span>
    </div>
    <div class="form-group col-md-2 col-sm-12">
        <label for=""> Subject Code </label>
        <input type="text" class="form-control" id="subject_code" name="subject_code" value=""
               placeholder="">
        <span id="error_subject_code" class="has-error"></span>
    </div>
    <div class="form-group col-md-2 col-sm-12">
        <label for=""> Subject Order </label>
        <input type="number" class="form-control decimal" id="subject_order" name="subject_order" value=""
               placeholder="">
        <span id="error_subject_order" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Subject Marks </label>
        <input type="number" class="form-control decimal" max="100" min="1" id="subject_marks" name="subject_marks"
               value=""
               placeholder="" required>
        <span id="error_subject_marks" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Pass Marks </label>
        <input type="number" class="form-control decimal" max="100" min="1" id="pass_marks" name="pass_marks" value=""
               placeholder="" required>
        <span id="error_pass_marks" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Theory Marks </label>
        <input type="number" class="form-control decimal" max="100" min="0" id="theory_marks" name="theory_marks"
               value=""
               placeholder="" required>
        <span id="error_theory_marks" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Theory Pass Marks </label>
        <input type="number" class="form-control decimal" max="100" min="0" id="theory_pass_marks"
               name="theory_pass_marks"
               value=""
               placeholder="" required>
        <span id="error_theory_pass_marks" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-2 col-sm-12">
        <label for=""> MCQ Marks </label>
        <input type="number" class="form-control decimal" max="50" min="0" id="mcq_marks" name="mcq_marks" value=""
               placeholder="">
        <span id="error_mcq_marks" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> MCQ Pass Marks </label>
        <input type="number" class="form-control decimal" id="mcq_pass_marks" name="mcq_pass_marks" value=""
               placeholder="">
        <span id="error_mcq_pass_marks" class="has-error"></span>
    </div>
    <div class="form-group col-md-2 col-sm-12">
        <label for=""> Practical Marks </label>
        <input type="number" class="form-control decimal" id="practical_marks" name="practical_marks" value=""
               placeholder="">
        <span id="error_practical_marks" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Practical Pass Marks </label>
        <input type="number" class="form-control decimal" id="practical_pass_marks" name="practical_pass_marks" value=""
               placeholder="">
        <span id="error_practical_pass_marks" class="has-error"></span>
    </div>
    <div class="form-group col-md-2 col-sm-12">
        <label for=""> CT Marks </label>
        <input type="number" class="form-control decimal" id="ct_marks" name="ct_marks" value=""
               placeholder="">
        <span id="error_ct_marks" class="has-error"></span>
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

<script>
    $(document).ready(function () {

        $('.filter').select2();
        $('#loader').hide();

        $('#create').validate({// <- attach '.validate()' to your form
            // Rules for form validation
            rules: {
                name: {
                    required: true
                },
                in_digit: {
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

                var myData = new FormData($("#create")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'subjects',
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