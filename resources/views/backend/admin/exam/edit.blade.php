<form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    {{method_field('PATCH')}}
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Exam's Name </label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $exam->name }}"
               placeholder="" required>
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-5 col-sm-12">
        <label for=""> Exam's Description </label>
        <input type="text" class="form-control" id="description" name="description" value="{{ $exam->description }}"
               placeholder="">
        <span id="error_description" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Select Class </label>
        <select name="class_id" id="class_id" class="form-control" required>
            <option value="" selected disabled>Select a class</option>
            @foreach($stdclass as $class)
                <option value="{{$class->id}}"
                    {{ ( $class->id == $exam->class_id) ? 'selected' : '' }} >{{$class->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Exam's Start Date </label>
        <input type="text" class="form-control" id="start_date" name="start_date" value="{{ $exam->start_date }}"
               placeholder="" required>
        <span id="error_start_date" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Exam's End Date </label>
        <input type="text" class="form-control" id="end_date" name="end_date" value="{{ $exam->end_date }}"
               placeholder="" required>
        <span id="error_end_date" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Result Modification Last Date </label>
        <input type="text" class="form-control" id="result_modification_last_date" name="result_modification_last_date"
               value="{{ $exam->result_modification_last_date }}" placeholder="" required>
        <span id="error_result_modification_last_date" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Main Marks % </label>
        <input type="text" class="form-control" id="main_marks_percentage" name="main_marks_percentage"
               value="{{ $exam->main_marks_percentage }}" min="0" max="100"
               placeholder="Number without % sign" required>
        <span id="error_main_marks_percentage" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Class Test Marks % </label>
        <input type="text" class="form-control" id="ct_marks_percentage" name="ct_marks_percentage"
               value="{{ $exam->ct_marks_percentage }}"
               placeholder="Number without % sign" readonly>
        <span id="error_ct_marks_percentage" class="has-error"></span>
    </div>
    <div class="form-group col-md-3">
        <label for=""> Status </label><br/>
        <input type="radio" name="status" class="flat-green"
               value="1" {{ ( $exam->status == 1 ) ? 'checked' : '' }} /> Active
        <input type="radio" name="status" class="flat-green"
               value="0" {{ ( $exam->status == 0 ) ? 'checked' : '' }}/> In Active
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <label for="photo">Upload Exam Routine</label>
        <input id="photo" type="file" name="photo" style="display:none">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn btn-success" onclick="$('input[id=photo]').click();">Browse</a>
            </div><!-- /btn-group -->
            <input type="text" name="SelectedFileName" class="form-control" id="SelectedFileName"
                   value="{{ $exam->file_path }}" readonly required>
        </div>
        <div class="clearfix"></div>
        <p class="help-block">File must be jpg, jpeg, png , doc, docx, pdf.</p>
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
    <div class="row">
        <div class="col-md-12 import_notice">
            <p> Please follow the Instructions before creating Exam: </p>
            <ol>
                <li>Double check the running session.You can change running session from setting menu.
                </li>
                <li>Set Main marks Percentage 100 if exam will not include class test marks</li>
            </ol>
            <span>
                *** Double check the information. Double check the Exam Name, Marks Percentage. *** </span>
        </div>
    </div>
</form>
<script>
    $(document).ready(function () {

        $('input[type="radio"].flat-green').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });

        $('#start_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });
        $('#end_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });
        $('#result_modification_last_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });

        $('#main_marks_percentage').on('input', function (e) {
            var main_percent = $("#main_marks_percentage").val();
            if ($.isNumeric(main_percent)) {
                $("#ct_marks_percentage").val(100 - main_percent);
            } else {
                $("#ct_marks_percentage").val(0);
                $("#main_marks_percentage").val('');
            }

        });

        $('#loader').hide();
        $('#edit').validate({// <- attach '.validate()' to your form
            // Rules for form validation
            rules: {
                name: {
                    required: true
                },
                marks_percentage: {
                    required: true,
                    number: true
                }
            },
            // Messages for form validation
            messages: {
                name: {
                    required: 'Enter exam name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#edit")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'exams/' + '{{ $exam->id }}',
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