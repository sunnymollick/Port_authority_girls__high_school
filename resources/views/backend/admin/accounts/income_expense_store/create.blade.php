<form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    <input type="hidden" name="store_voucher" value="{{ $voucher_id }}">
    <div class="form-group col-md-2 col-sm-12">
        <label for=""> Payment Date </label>
        <input type="text" class="form-control" id="store_date" name="store_date" value="{{date('Y-m-d')}}"
               placeholder="" required readonly>
        <span id="error_store_date" class="has-error"></span>
    </div>
    <div class="form-group col-md-2 col-sm-12">
        <label for=""> Store Type </label>
        <input type="text" class="form-control" name="store_type" id="store_type" value="{{ $store_type }}" readonly>
        <span id="error_store_type" class="has-error"></span>
    </div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Store Category </label>
        <select name="category_id" id="category_id" class="form-control filter" required
                onchange="get_items(this.value)">
            <option value="" selected disabled>Select Store Category</option>
            @foreach($category as $cat)
                <option value="{{$cat->id}}">{{$cat->category_name}}</option>
            @endforeach
        </select>
        <span id="error_expense_category_id" class="has-error"></span>
    </div>
    <div class="form-group col-md-5 col-sm-12">
        <label for=""> Item Name </label>
        <select name="item_id" id="item_id" class="form-control filter" required>
            <option value="" selected disabled>Select Item</option>
        </select>
        <span id="error_expense_category_id" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-3 col-sm-12">
        <label for=""> Name </label>
        <input type="text" class="form-control" id="name" name="name" value=""
               placeholder="" required>
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-5 col-sm-12">
        <label for=""> Address </label>
        <input type="text" class="form-control" id="address" name="address" value=""
               placeholder="">
        <span id="error_address" class="has-error"></span>
    </div>
    <div class="form-group col-md-2 col-sm-12">
        <label for=""> Amount </label>
        <input type="text" class="form-control" id="amount" name="amount" value=""
               placeholder="" required>
        <span id="error_amount" class="has-error"></span>
    </div>
    <div class="form-group col-md-2 col-sm-12">
        <label for=""> Payment Method </label>
        <select name="payment_method" id="payment_method" class="form-control" required>
            <option value="Cash">Cash</option>
            <option value="Bank">Bank</option>
        </select>
        <span id="error_category" class="has-error"></span>
    </div>
    <div class="clearfix"></div>
    <div id="bank_section">
        <div class="form-group col-md-3 col-sm-12">
            <label for=""> Select Bank </label>
            <select name="bank_name_id" id="bank_name_id" class="form-control"
                    onchange="get_bank_accounts(this.value)" required>
                <option value="" selected disabled>Select Bank</option>
                @foreach($banks as $bank)
                    <option value="{{$bank->id}}">{{$bank->bank_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-3 col-sm-12">
            <label for=""> Select Account Number </label>
            <select name="bank_account_id" id="bank_account_id" class="form-control filter" required>
                <option value="" selected disabled>Select Account Number</option>
            </select>
        </div>
        <div class="form-group col-md-3 col-sm-12">
            <label for=""> Check Number </label>
            <input type="text" class="form-control" id="check_number" name="check_number" value=""
                   placeholder="">
            <span id="error_check_number" class="has-error"></span>
        </div>
        <div class="form-group col-md-3 col-sm-12">
            <label for=""> Check Date </label>
            <input type="text" class="form-control" id="check_date" name="check_date" value=""
                   placeholder="" required readonly>
            <span id="error_check_date" class="has-error"></span>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12 col-sm-12">
        <label for=""> Comment </label>
        <textarea type="text" class="form-control" id="comment" name="comment" value=""
                  placeholder=""></textarea>
        <span id="error_comment" class="has-error"></span>
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

    $("#bank_section").hide();

    $('#payment_method').change(function () {
        var payment_method = $('#payment_method').val();

        if (payment_method === "Bank") {
            $("#bank_section").show();
        } else {
            $("#bank_section").hide();
        }
    });

    function get_category(val) {
        $("#category_id").empty();
        $.ajax({
            type: 'GET',
            url: 'getAllStoreCategory/' + val,
            success: function (data) {
                $("#category_id").html(data);
            },
            error: function (result) {
                $("#category_id").html("Sorry Cannot Load Data");
            }
        });
    }

    function get_items(val) {
        $("#item_id").empty();
        $.ajax({
            type: 'GET',
            url: 'getAllStoreCategoryItems/' + val,
            success: function (data) {
                $("#item_id").html(data);
            },
            error: function (result) {
                $("#item_id").html("Sorry Cannot Load Data");
            }
        });
    }

    function get_bank_accounts(val) {
        $("#bank_account_id").empty();
        $.ajax({
            type: 'GET',
            url: 'getBankAccountNumber/' + val,
            success: function (data) {
                $("#bank_account_id").html(data);
            },
            error: function (result) {
                $("#bank_account_id").html("Sorry Cannot Load Data");
            }
        });
    }

    $(document).ready(function () {

        $('.filter').select2();

        $('#store_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });

        $('#check_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
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
                    required: 'Enter Payer Name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#create")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'incomesExpenses',
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