<div class="row">
    <div class="col-md-12 col-sm-12 table-responsive">
        <table id="view_details" class="table table-bordered table-hover">
            <tbody>
            <tr>
                <td class="subject"> Roles Name</td>
                <td> :</td>
                <td> {{ $feecategory->name }} </td>
            </tr>
            <tr>
                <td class="subject"> Applied Class Name</td>
                <td> :</td>
                <td> {{ $feecategory->std_class ? $feecategory->std_class->name : ''  }} </td>
            </tr>
            <tr>
                <td class="subject"> Category Items</td>
                <td> :</td>
                <td>
                    @foreach($feecategory->fee_items as $items)
                        {{ $items->item_name }} -- {{ $items->amount }}Tk<br/>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td class="subject"> Month</td>
                <td> :</td>
                <td> {!! $monthName = date("F", mktime(0, 0, 0, $feecategory->month, 10)) !!} </td>
            </tr>
            <tr>
                <td class="subject"> Session</td>
                <td> :</td>
                <td> {{ $feecategory->year }} </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>