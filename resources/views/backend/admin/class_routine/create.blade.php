<form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    <div class="form-group col-md-4 col-sm-12">
        <select name="class_id" id="class_id" class="form-control" required
                onchange="get_class_sections(this.value)">
            <option value="" selected disabled>Select a class</option>
            @foreach($stdclass as $class)
                <option value="{{$class->id}}">{{$class->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <select class="form-control" name="section_id" id="class_section_id" required>
            <option value="">Select a section</option>
        </select>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <select class="form-control" name="subject_id" id="subject_id" required>
            <option value="">Select a subject</option>
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-6 col-sm-12">
        <select name="teacher_id" id="teacher_id" class="form-control" required>
            <option value="" selected disabled>Select class teacher</option>
            @foreach($teachers as $teacher)
                <option value="{{$teacher->id}}">{{$teacher->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <select name="class_room_id" id="class_room_id" class="form-control" required>
            <option value="" selected disabled>Select class room</option>
            @foreach($classrooms as $room)
                <option value="{{$room->id}}">{{$room->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <select name="day" id="day" class="form-control" required="">
            <option value="">Select a day</option>
            <option value="saturday">Saturday</option>
            <option value="sunday">Sunday</option>
            <option value="monday">Monday</option>
            <option value="tuesday">Tuesday</option>
            <option value="wednesday">Wednesday</option>
            <option value="thursday">Thursday</option>
            <option value="friday">Friday</option>
        </select>
    </div>
    <div class="form-group col-md-2 col-sm-12">
        <select name="time_start" id="time_start" class="form-control" required="">
            <option value="">Starting hour</option>
            @for($i = 1; $i <= 24 ; $i++)
                <option value="{{ sprintf("%02d", $i)  }}">
                    {{ ( $i <= 12) ? sprintf("%02d", $i). ' AM' : sprintf("%02d", ($i-12)).' PM' }}</option>
            @endfor
        </select>
    </div>
    <div class="form-group col-md-2 col-sm-12">
        <select name="time_start_min" id="time_start_min" class="form-control" required="">
            <option value="">Starting minute</option>
            @for($i = 0; $i <= 60 ; $i++)
                <option value="{{sprintf("%02d", $i)}}"> {{sprintf("%02d", $i)}}</option>
            @endfor
        </select>
    </div>
    <div class="form-group col-md-2 col-sm-12">
        <select name="time_end" id="time_end" class="form-control" required="">
            <option value="">Ending hour</option>
            @for($i = 1; $i <= 24 ; $i++)
                <option value="{{sprintf("%02d", $i)}}">
                    {{ ( $i <= 12) ? sprintf("%02d", $i). ' AM' : sprintf("%02d", ($i-12)).' PM' }}</option>
            @endfor
        </select>
    </div>
    <div class="form-group col-md-2 col-sm-12">
        <select name="time_end_min" id="time_end_min" class="form-control" required="">
            <option value="">Ending minute</option>
            @for($i = 0; $i <= 60 ; $i++)
                <option value="{{sprintf("%02d", $i)}}"> {{sprintf("%02d", $i)}}</option>
            @endfor
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12">
        <button type="submit" class="btn btn-success button-submit"
                data-loading-text="Loading..."><span class="fa fa-save fa-fw"></span> Save
        </button>
        <button type="button" class="btn btn-default submit" data-dismiss="modal"><span
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
                    url: 'classroutines',
                    type: 'POST',
                    data: myData,
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('#loader').show();
                        $(".submit").prop('disabled', false); // disable button
                    },
                    success: function (data) {
                        if (data.type === 'success') {
                            notify_view(data.type, data.message);
                            getRoutines();
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