<form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    {{method_field('PATCH')}}
    <div class="form-group col-md-12 col-sm-12">
        <label for=""> Title </label>
        <input type="text" class="form-control" id="title" name="title" value="{{$admissionResult->title}}"
               placeholder="" required>
        <span id="error_title" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-6 col-sm-12">
        <select name="class_id" id="class_id" class="form-control" required
                onchange="get_sections(this.value)">
            <option value="" selected disabled>Select a class</option>
            @foreach($stdclass as $class)
                <option value="{{$class->id}}"
                    {{ ( $class->id == $admissionResult->class_id) ? 'selected' : '' }} >{{$class->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <select class="form-control" name="section_id" id="section_id" required>
            <option value="{{ $admissionResult->section_id }}">{{ $admissionResult->section->name }}</option>
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-8">
        <label for="result_file">Upload Image</label>
        <input id="result_file" type="file" name="result_file" style="display:none">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn btn-success" onclick="$('input[id=result_file]').click();">Browse</a>
            </div><!-- /btn-group -->
            <input type="text" name="SelectedFileName" class="form-control" id="SelectedFileName"
                   value="{{$admissionResult->file_path}}" readonly>
        </div>
        <div class="clearfix"></div>
        <p class="help-block">File must be jpg, jpeg, png.</p>
        <span id="error_result_file" class="has-error"></span>
        <script type="text/javascript">
            $('input[id=result_file]').change(function () {
                $('#SelectedFileName').val($(this).val());
            });
        </script>
    </div>
    <div class="form-group col-md-4">
        <label for=""> Status </label><br/>
        <input type="radio" name="status" class="flat-green"
               value="1" {{ ( $admissionResult->status == 1 ) ? 'checked' : '' }} /> Active
        <input type="radio" name="status" class="flat-green"
               value="0" {{ ( $admissionResult->status == 0 ) ? 'checked' : '' }}/> In Active
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12">
        <button type="submit" class="btn btn-success"><span class="fa fa-save fa-fw"></span> Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><span
                class="fa fa-times-circle fa-fw"></span> Cancel
        </button>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    function get_sections(val) {

        $("#section_id").empty();
        $.ajax({
            type: 'GET',
            url: 'getSections/' + val,
            success: function (data) {
                $("#section_id").html(data);
            },
            error: function (result) {
                $("#section_id").html("Sorry Cannot Load Data");
            }
        });
    }
    $('input[type="radio"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
    });
    $(document).ready(function () {
        $('#loader').hide();
        $('#edit').validate({// <- attach '.validate()' to your form
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
                    required: 'Enter book name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#edit")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'admissionResult/' + '{{ $admissionResult->id }}',
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