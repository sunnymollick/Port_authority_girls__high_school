<div class="row">
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
</div>