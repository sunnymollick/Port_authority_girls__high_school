<div class="row">
    <h2 class="text-center">Student Monthly Fee Payment</h2>
    <form id='confirmation' action="" enctype="multipart/form-data" method="post"
          accept-charset="utf-8">
        <div id="status"></div>
        {{method_field('PATCH')}}
        <input type="hidden" name="invoice_id" value="{{$invoice->id}}"/>
        <div class="col-md-12 col-sm-12 table-responsive">
            <table id="view_details" class="table table-bordered table-hover">
                <tbody>
                <tr>
                    <td class="subject"> Student's Name</td>
                    <td> :</td>
                    <td> {{ $students->name }} </td>
                </tr>
                <tr>
                    <td class="subject"> Student's Code</td>
                    <td> :</td>
                    <td> {{ $students->std_code }} </td>
                </tr>
                <tr>
                    <td class="subject"> Class</td>
                    <td> :</td>
                    <td> {{ $students->class_name }} </td>
                </tr>
                <tr>
                    <td class="subject"> Payment Title</td>
                    <td> :</td>
                    <td> {{ $invoice->title }} </td>
                </tr>
                <tr>
                    <td class="subject"> Payment Month</td>
                    <td> :</td>
                    <td> {{ date("F", mktime(0, 0, 0, $feecategory->month, 10)). ', ' . $invoice->year }} </td>
                </tr>
                <tr>
                    <td class="subject"> Payment Details</td>
                    <td> :</td>
                    <td>

                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Head of the Fund</th>
                                <th class="amount">Amount (Tk.)</th>
                            </tr>
                            </thead>
                            @php $i = 1 @endphp
                            @foreach($feecategory->fee_items as $items)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $items->item_name }}</td>
                                    <td class="amount">{{ $items->amount }}</td>
                                </tr>
                            @endforeach
                        </table>


                    </td>
                </tr>
                <tr>
                    <td class="subject"> Amount</td>
                    <td> :</td>
                    <td> {{ $invoice->amount }} </td>
                </tr>
                <tr>
                    <input type="hidden" name="paid" value="{{$invoice->amount}}"/>
                    <td class="subject"> Paid</td>
                    <td> :</td>
                    <td> {{ $invoice->paid }} </td>
                </tr>
                <tr>
                    <td class="subject"> Due</td>
                    <td> :</td>
                    <td> {{ $invoice->due }} </td>
                </tr>
                <tr>
                    <td class="subject"> Payment Date</td>
                    <td> :</td>
                    <td> {{ $invoice->payment_date }} </td>
                </tr>
                <tr>
                    <td class="subject"> Payment Status</td>
                    <td> :</td>
                    <td> {!!  $invoice->status == 'Paid' ? '<span class="label label-success">Paid</span>' : '<span class="label label-danger">Not Paid</span>' !!}   </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="clearfix"></div>
        <div class="form-group col-md-3 col-sm-12">
            <label for=""> Method </label>
            <select name="method" id="method" class="form-control" required>
                <option value="Bank">Bank</option>
                <option value="Cash">Cash</option>
            </select>
            <span id="error_category" class="has-error"></span>
        </div>
        <div class="form-group col-md-3 col-sm-12">
            <label for=""> Payment Date </label>
            <input type="text" class="form-control" id="payment_date" name="payment_date" value=""
                   placeholder="" required readonly>
            <span id="error_payment_date" class="has-error"></span>
        </div>
        <div class="clearfix"></div>

        <div class="form-group col-md-3 col-sm-12">
            <label for=""> Please Confirm Payment </label>
            <select name="status" id="status" class="form-control" required>
                <option value="" selected disabled>Confirm Payment</option>
                <option value="Paid">Paid</option>
                <option value="Not Paid">Not Paid</option>
            </select>
            <span id="error_category" class="has-error"></span>
        </div>
        <div class="clearfix"></div>
        <div class="form-group col-md-12">
            <button type="submit" class="btn btn-success button-submit"
                    data-loading-text="Loading..."><span class="fa fa-check fa-fw"></span> Payment Confirmation Procced
            </button>
        </div>
        <div class="clearfix"></div>
    </form>
</div>
<script>
    $(document).ready(function () {

        $('#payment_date').datepicker({format: "yyyy-mm-dd"}).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });


        $('#confirmation').validate({// <- attach '.validate()' to your form
            // Rules for form validation
            rules: {
                payment_date: {
                    required: true
                }
            },
            // Messages for form validation
            messages: {
                payment_date: {
                    required: 'Enter  payment date'
                }
            },
            submitHandler: function (form) {

                var myData = new FormData($("#confirmation")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);

                swal({
                    title: "Are you sure?",
                    text: "Before confirmation check everything!!",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel"
                }, function () {

                    $.ajax({
                        url: 'confirmPayment',
                        type: 'POST',
                        data: myData,
                        dataType: 'json',
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function (data) {

                            if (data.type === 'success') {

                                swal("Done!", "Successfully Updated", "success");
                                reload_table();

                            } else if (data.type === 'danger') {

                                swal("Error!", "Try again", "error");

                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            swal("Error!", "Try again", "error");
                        }
                    });
                });
            }

        });

    });
</script>