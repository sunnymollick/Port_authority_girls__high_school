<form id='edit' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    {{method_field('PATCH')}}
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Event's Name </label>
        <input type="text" class="form-control" id="name" name="name" value="{{$event->name}}"
               placeholder="" required>
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Event's Location </label>
        <input type="text" class="form-control" id="location" name="location" value="{{$event->location}}"
               placeholder="">
        <span id="error_location" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12 col-sm-12">
        <label for=""> Event's Details </label>
        <textarea type="text" class="form-control" id="details" name="details"
                  placeholder="">{{$event->details}}</textarea>
        <span id="error_details" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Event's Start Date </label>
        <input type="text" class="form-control" id="start_date" name="start_date" value="{{$event->start_date}}"
               placeholder="Event's Start Date" required readonly>
        <span id="error_start_date" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Event's End Date </label>
        <input type="text" class="form-control" id="end_date" name="end_date" value="{{$event->end_date}}"
               placeholder="Event's End Date" required readonly>
        <span id="error_end_date" class="has-error"></span>
    </div>
    <div class="form-group col-md-4">
        <label for=""> Status </label><br/>
        <input type="radio" name="status" class="flat-green"
               value="1" {{ ( $event->status == 1 ) ? 'checked' : '' }} /> Active
        <input type="radio" name="status" class="flat-green"
               value="0" {{ ( $event->status == 0 ) ? 'checked' : '' }}/> In Active
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
<!-- Date Time Picker library -->
<link rel="stylesheet" href="{{ asset('/assets/css/jquery.datetimepicker.min.css') }}">
<script src="{{ asset('/assets/js/jquery.datetimepicker.full.min.js') }}"></script>
<script>
    $(document).ready(function () {

        $('input[type="radio"].flat-green').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });

        jQuery('#start_date').datetimepicker({
            format: "Y-m-d H:i:d"
        });
        jQuery('#end_date').datetimepicker({
            format: "Y-m-d H:i:d"
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
                    required: 'Enter event name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#edit")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'events/' + '{{ $event->id }}',
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