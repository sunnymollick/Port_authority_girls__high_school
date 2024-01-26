<div class="row">
    <div class="col-md-12 col-sm-12 table-responsive">
        <table id="view_details" class="table table-bordered table-hover">
            <tbody>
            <tr>
                <td class="subject"> Class Name</td>
                <td> :</td>
                <td> {{ $exam->name }} </td>
            </tr>
            <tr>
                <td class="subject"> Description</td>
                <td> :</td>
                <td> {{ $exam->description }} </td>
            </tr>
            <tr>
                <td class="subject"> Start Date</td>
                <td> :</td>
                <td> {{ $exam->start_date }} </td>
            </tr>
            <tr>
                <td class="subject"> End Date</td>
                <td> :</td>
                <td> {{ $exam->end_date }} </td>
            </tr>
            <tr>
                <td class="subject"> Result Modification Last Date</td>
                <td> :</td>
                <td> {{ $exam->result_modification_last_date }} </td>
            </tr>
            <tr>
                <td class="subject"> Main Marks %</td>
                <td> :</td>
                <td> {{ $exam->main_marks_percentage . '%' }} </td>
            </tr>
            <tr>
                <td class="subject"> Class Test Marks %</td>
                <td> :</td>
                <td> {{ $exam->ct_marks_percentage . '%' }} </td>
            </tr>
            <tr>
                <td class="subject"> Exam Routine</td>
                <td> :</td>
                <td> @php $file_path = $exam->file_path ? "<a class='btn btn-primary' href='" . asset($exam->file_path) . "' target='_blank'>Download</a>" : '' ;  @endphp {!! $file_path !!}   </td>
            </tr>
            <tr>
                <td class="subject"> Status</td>
                <td> :</td>
                <td> @php $status = $exam->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>' ;  @endphp {!! $status !!}   </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>