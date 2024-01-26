<form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    <div class="form-group col-md-12 col-sm-12">
        <label for=""> Fee Title </label>
        <input type="text" class="form-control" id="title" name="title" value=""
               placeholder="" required>
        <span id="error_title" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
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
        <select class="form-control" name="section_id" id="section_id" required>
            <option value="">Select a section</option>
        </select>
    </div>
    <div class="form-group col-md-4 col-sm-12">
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
    <div class="clearfix"></div>
    <div class="col-sm-12 col-md-12">
        <label for="se_permission"> Account Head : </label>
        <br/> <br/>
        @foreach($accountsHead as $value)
            <div class="col-md-4">
                <div class="col-md-7">
                    <input type="checkbox" name="all_head" class="data-check flat-green"
                           value="{{$value->id}}" id="all_head"/>
                    {{ $value->name }}
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" id="amount" name="amount_{{$value->id}}" value=""
                           placeholder="">
                </div>
            </div>
        @endforeach
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12"><br/>
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
    $('input[type="checkbox"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green'
    });

    function get_sections(val) {
        $("#section_id").empty();
        $.ajax({
            type: 'GET',
            url: 'getAllSection/' + val,
            success: function (data) {
                $("#section_id").html(data);
            },
            error: function (result) {
                $("#modal_data").html("Sorry Cannot Load Data");
            }
        });
    }
    $(document).ready(function () {


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

                var list_id = [];
                $(".data-check:checked").each(function () {
                    list_id.push(this.value);
                });
                if (list_id.length > 0) {

                    var myData = new FormData($("#create")[0]);
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    myData.append('_token', CSRF_TOKEN);
                    myData.append('accounts_head_id', list_id);


                    swal({
                        title: "Confirm to assign " + list_id.length + " fee items",
                        text: "Assign fee items!",
                        type: "warning",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes, Assign!"
                    }, function () {

                        $.ajax({
                            url: 'accountsFees',
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
                                    swal("Done!", "It was succesfully done!", "success");
                                    reload_table();
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
                                    swal("Error!", data.message, "error");

                                }

                            }
                        });
                    });

                }
                else {
                    swal("", "Please selects accounts fee items!", "warning");
                }
            }
            // <- end 'submitHandler' callback
        });                    // <- end '.validate()'

    });
</script>