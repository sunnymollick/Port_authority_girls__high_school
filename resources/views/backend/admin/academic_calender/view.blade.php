<div class="row">
    <div class="col-md-12 col-sm-12 table-responsive">
        <table id="view_details" class="table table-bordered table-hover">
            <tbody>
            <tr>
                <td class="subject"> Class Name</td>
                <td> :</td>
                <td> {{ $section->name }} </td>
            </tr>
            <tr>
                <td class="subject"> In Digit</td>
                <td> :</td>
                <td> {{ $section->stdclass->name }} </td>
            </tr>
            <tr>
                <td class="subject"> Status</td>
                <td> :</td>
                <td> @php $status = $section->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>' ;  @endphp {!! $status !!}   </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>