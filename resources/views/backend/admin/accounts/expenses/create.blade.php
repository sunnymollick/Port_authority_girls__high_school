<form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Title </label>
        <input type="text" class="form-control" id="title" name="title" value=""
               placeholder="" required>
        <span id="error_title" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Expense Category </label>
        <select name="expense_category_id" class="form-control filter" required>
            <option value="" selected disabled>Select Expense Category</option>
            @foreach($expense_category as $category)
                <option value="{{$category->id}}">{{$category->name}}</option>
            @endforeach
        </select>
        <span id="error_expense_category_id" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12 col-sm-12">
        <label for=""> Details </label>
        <textarea type="text" class="form-control" id="details" name="details" value=""
                  placeholder=""></textarea>
        <span id="error_details" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Amount </label>
        <input type="text" class="form-control" id="amount" name="amount" value=""
               placeholder="" required>
        <span id="error_amount" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Method </label>
        <select name="method" id="method" class="form-control" required>
            <option value="Cash">Cash</option>
            <option value="Check">Check</option>
            <option value="Card">Card</option>
        </select>
        <span id="error_category" class="has-error"></span>
    </div>
    <div class="form-group col-md-4 col-sm-12">
        <label for=""> Expense Date </label>
        <input type="text" class="form-control" id="expense_date" name="expense_date" value=""
               placeholder="" required readonly>
        <span id="error_expense_date" class="has-error"></span>
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

        $('#expense_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });

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
                    required: 'Enter expense title'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#create")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'expenses',
                    type: 'POST',
                    data: myData,
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('#loader').show();
                        $(".button-submit").prop('disabled', true); // disable button
                    },
                    success: function (data) {
                        if (data.type === 'success') {
                            reload_table();
                            notify_view(data.type, data.message);
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