<div class="row">
    <div class="col-md-12 col-sm-12 table-responsive">
        <table id="view_details" class="table table-bordered table-hover">
            <tbody>
            <tr>
                <td class="subject"> Expense Title</td>
                <td> :</td>
                <td> {{ $expense->title }} </td>
            </tr>
            <tr>
                <td class="subject"> Expense Category</td>
                <td> :</td>
                <td> {{ $expense->expense_category ? $expense->expense_category->name : '' }} </td>
            </tr>
            <tr>
                <td class="subject"> Details</td>
                <td> :</td>
                <td> {{ $expense->details }} </td>
            </tr>
            <tr>
                <td class="subject"> Amount</td>
                <td> :</td>
                <td> {{ $expense->amount }} </td>
            </tr>
            <tr>
                <td class="subject"> Method</td>
                <td> :</td>
                <td> {{ $expense->method }} </td>
            </tr>
            <tr>
                <td class="subject"> Expense Date</td>
                <td> :</td>
                <td> {{ $expense->expense_date }} </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>