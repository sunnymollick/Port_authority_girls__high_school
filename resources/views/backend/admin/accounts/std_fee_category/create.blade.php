<form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
    <div id="status"></div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Roles Name </label>
        <input type="text" class="form-control" id="name" name="name" value=""
               placeholder="" required>
        <span id="error_name" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Class Name </label>
        <select name="class_id" id="class_id" class="form-control" required>
            <option value="" selected disabled>Select a class</option>
            @foreach($stdclass as $class)
                <option value="{{$class->id}}">{{$class->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12 col-sm-12">
        <label for=""> Fee Items </label>
        <table id="form_table" class="table">
            <tbody>
            <tr class='case'>
                <td><input type="text" class="form-control" type='text' id="item_name" name='item_name[]'
                           value="" placeholder="Fee name" required/></td>
                <td><input  class="form-control each_amount"  name='item_amount[]'
                           type="number" min="0" placeholder="Item amount" required/></td>
            </tr>
            </tbody>
        </table>
        <button type="button" href="javascript:;" class='btn btn-success addmore pull-right'> + add more</button>
        <br>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-6 col-sm-12">
        <label for=""> Total Amount </label>
        <input type="text" class="form-control" id="amount" name="amount" value=""
               placeholder="" required readonly>
        <span id="error_amount" class="has-error"></span>
    </div>
    <div class="form-group col-md-6 col-sm-12">
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
    <div class="form-group col-md-12">
        <button type="submit" class="btn btn-success button-submit"
                data-loading-text="Loading..."><span class="fa fa-save fa-fw"></span> Save
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><span
                class="fa fa-times-circle fa-fw"></span> Cancel
        </button>
        <span id="loader"><img  src="{{ asset('/assets/images/loadingg.gif') }}"/> Please wait...</span>
    </div>
    <div class="clearfix"></div>
</form>


<script>
    $(document).ready(function () {

        $(".addmore").on('click', function () {
            var data = "<tr class='case'>";
            data += "<td><input type='text' class='form-control' name='item_name[]' placeholder='Fee Name'/></td>"
                + "<td><input  type='number' min='0' class='form-control each_amount' name='item_amount[]' placeholder='Item Amount'/></td>"
                + "<td style='text-align:center;'><a class='btn btn-danger'><i class='fa fa-times'></i></a></td></tr>";
            $('#form_table').append(data);
        });

        $('#form_table').on('click', 'tr a', function (e) {
            e.preventDefault();
            $(this).parents('tr').remove();
            totalAmount();
        });


        $(document).on('keyup', '#form_table .each_amount', function () {

            totalAmount();

        });

        function totalAmount() {
            var cnt = 0;
            $("input[name*='item_amount']").each(function () {
                if (isNaN(this.value)) {
                    $("#amount").val(0);
                    return 0;
                } else {
                    cnt += Number(this.value);
                }

            });

            $("#amount").val(cnt);
        }

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
                    required: 'Enter class room name'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#create")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                $.ajax({
                    url: 'feecategory',
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