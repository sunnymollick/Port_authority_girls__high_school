<form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Select Class </label>
        <select name="class_id" id="class_id" class="form-control"
                onchange="get_sections(this.value)" required>
            <option value="" selected disabled>Select a class</option>
            @foreach($stdclass as $class)
                <option value="{{$class->id}}">{{$class->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Select Section </label>
        <select class="form-control" name="section_id" id="section_id" required
                onchange="getStudents()">
            <option value="">Select a section</option>
        </select>
    </div>
    <div class="form-group col-md-5 col-sm-12">
        <label for=""> Select Student </label>
        <select class="form-control filter" name="student_id" id="student_id" required>
            <option value="">Select a Student</option>
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Month </label>
        <select name="month" id="month" class="form-control" required>
            <option value="">Select month</option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
        <span id="error_month" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Select Accounts Head </label>
        <select name="accounts_head_id" id="accounts_head_id" class="form-control filter" required>
            <option value="" selected disabled>Select Accounts Head</option>
            @foreach($accountsHead as $value)
                <option value="{{$value->id}}">{{$value->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="form-group">
            <label for="amount"> Amount </label>
            <input type="text" class="form-control" id="amount" name="amount" value=""
                   placeholder="">
        </div>
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


    function getStudents() {

        var class_id = $("#class_id").val();
        var section_id = $("#section_id").val();

        if (class_id != null && section_id != null) {

            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            $("#student_id").empty();
            $.ajax({
                type: 'GET',
                url: 'getAllStudents',
                data: {"_token": CSRF_TOKEN, "class_id": class_id, "section_id": section_id},
                success: function (data) {
                    $("#student_id").html(data);
                },
                error: function (result) {
                    $("#modal_data").html("Sorry Cannot Load Data");
                }
            });
        }
    }

    $(document).ready(function () {


        $('.filter').select2();

        $('#loader').hide();

        $('#create').validate({// <- attach '.validate()' to your form
            // Rules for form validation
            rules: {
                name: {
                    required: true
                }
            },
            // Messages for form validation
            messages: {
                name: {
                    required: 'Enter name'
                }
            },
            submitHandler: function (form) {


                var myData = new FormData($("#create")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'accountsExceptionalStudent',
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
                            $('#loader').hide();
                            $("#submit").prop('disabled', false); // disable button
                            notify_view(data.type, data.message);
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