<form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    {{method_field('PATCH')}}
    <div class="form-group col-md-12 col-sm-12">
        <label for=""> Syllabus Title </label>
        <input type="text" class="form-control" id="title" name="title" value="{{$syllabus->title}}"
               placeholder="" required>
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <select name="class_id" id="class_id" class="form-control" required
                onchange="get_class_sections(this.value)">
            @foreach($stdclass as $class)
                <option value="{{$class->id}}"
                    {{ ( $class->id == $syllabus->class_id) ? 'selected' : '' }} >{{$class->name}}</option>
            @endforeach
        </select>
        <span id="error_class_id" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <select class="form-control" name="section_id" id="class_section_id" required>
            <option value="{{$syllabus->section->id}}">{{$syllabus->section->name}}</option>
        </select>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <select class="form-control" name="subject_id" id="subject_id" required>
            <option value="{{$syllabus->subject->id}}">{{$syllabus->subject->name}}</option>
        </select>
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
                   value="{{$syllabus->file_path}}" readonly>
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
    function get_class_sections(val) {
        $("#class_section_id").empty();
        get_class_subjects(val);
        $.ajax({
            type: 'GET',
            url: 'getSections/' + val,
            success: function (data) {
                $("#class_section_id").html(data);
            },
            error: function (result) {
                $("#subject_id").html("Sorry Cannot Load Data");
            }
        });
    }

    function get_class_subjects(val) {
        $("#subject_id").empty();
        $.ajax({
            type: 'GET',
            url: 'getSubjects/' + val,
            success: function (data) {
                $("#subject_id").html(data);
            },
            error: function (result) {
                $("#subject_id").html("Sorry Cannot Load Data");
            }
        });
    }

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
                    required: 'Enter class name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#edit")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'syllabus/' + '{{ $syllabus->id }}',
                    type: 'POST',
                    data: myData,
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('#loader').show();
                        $(".button-submit").prop('disabled', false); // disable button
                    },
                    success: function (data) {
                        if (data.type === 'success') {
                            notify_view(data.type, data.message);
                            getSyllabus();
                            $('#loader').hide();
                            $(".button-submit").prop('disabled', false); // disable button
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
                            $(".button-submit").prop('disabled', false); // disable button

                        }
                    }
                });
            }
            // <- end 'submitHandler' callback
        });                    // <- end '.validate()'

    });
</script>