<div class="row">
    <div class="col-md-12 col-sm-12 table-responsive">
        <table id="view_details" class="table table-bordered table-hover">
            <tbody>
            <tr>
                <td class="subject"> Event Name</td>
                <td> :</td>
                <td> {{ $event->name }} </td>
            </tr>
            <tr>
                <td class="subject"> Description</td>
                <td> :</td>
                <td> {{ $event->details }} </td>
            </tr>
            <tr>
                <td class="subject"> Start Date</td>
                <td> :</td>
                <td> {{ date('dS F, Y H:i:s A', strtotime($event->start_date)) }}</td>
            </tr>
            <tr>
                <td class="subject"> End Date</td>
                <td> :</td>
                <td> {{ date('dS F, Y H:i:s A', strtotime($event->end_date)) }} </td>
            </tr>
            <tr>
                <td class="subject"> Location</td>
                <td> :</td>
                <td> {{ $event->location }} </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>