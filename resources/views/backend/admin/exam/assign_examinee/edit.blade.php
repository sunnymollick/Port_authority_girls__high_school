<form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    {{method_field('PATCH')}}
    <div class="form-group col-md-4 col-sm-12">
        <select name="class_id" id="class_id" class="form-control" required
                onchange="get_sections(this.value)">
            <option value="" selected disabled>Select a class</option>
            @foreach($stdclass as $class)
                <option value="{{$class->id}}"
                    {{ ( $class->id == $examinee->class_id) ? 'selected' : '' }}
                >{{$class->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <select class="form-control" name="section_id" id="section_id" required>
            <option value="">Select a section</option>
            @foreach($section as $s)
                @if($s->id == $examinee->section_id )
                    <option value="{{$s->id}}" selected>{{$s->name}}</option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <select name="exam_id" id="exam_id" class="form-control" required>
            <option value="" selected disabled>Select a Exam</option>
            @foreach($exams as $exam)
                @if($exam->id == $examinee->exam_id )
                    <option value="{{$exam->id}}" selected>{{$exam->name}}</option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <select name="teacher_id" id="teacher_id" class="form-control filter" required>
            <option value="" selected disabled>Select a Examinee</option>
            @foreach($teacher as $t)
                <option value="{{$t->id}}"
                    {{ ( $t->id == $examinee->teacher_id) ? 'selected' : '' }}
                >{{$t->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <select class="form-control filter" name="subject_id" id="subject_id" required>
            <option value="">Select a subject</option>
            @foreach($subjects as $sub)
                @if($sub->id == $examinee->subject_id )
                    <option value="{{$sub->id}}" selected>{{$sub->name}}</option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4">
        <input type="radio" name="status" class="flat-green"
               value="1" {{ ( $examinee->status == 1 ) ? 'checked' : '' }} /> Active
        <input type="radio" name="status" class="flat-green"
               value="0" {{ ( $examinee->status == 0 ) ? 'checked' : '' }}/> In Active
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
    function get_sections(val) {
        get_exams(val)
        get_class_subjects(val);
        $("#section_id").empty();
        $.ajax({
            type: 'GET',
            url: 'getSections/' + val,
            success: function (data) {
                $("#section_id").html(data);
            },
            error: function (result) {
                $("#modal_data").html("Sorry Cannot Load Data");
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

    function get_exams(val) {

        $("#exam_id").empty();
        $.ajax({
            type: 'GET',
            url: 'getExams/' + val,
            success: function (data) {
                $("#exam_id").html(data);
            },
            error: function (result) {
                $("#modal_data").html("Sorry Cannot Load Data");
            }
        });
    }


    $(document).ready(function () {

        $('input[type="radio"].flat-green').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });


        $('#loader').hide();
        $('.filter').select2();

        $('#edit').validate({// <- attach '.validate()' to your form
            // Rules for form validation
            rules: {
                exam_id: {
                    required: true
                }
            },
            // Messages for form validation
            messages: {
                exam_id: {
                    required: 'Enter exam name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#edit")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'editExaminee/' + '{{ $examinee->id }}',
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