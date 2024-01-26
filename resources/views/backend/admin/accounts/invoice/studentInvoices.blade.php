<div class="row">
    @if($feecategory->count()>0)
        <div class="col-md-12 col-sm-12 table-responsive">
            @php $i = 1 @endphp
            <table id="fee_result" class="table table-collapse table-bordered table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Roles</th>
                    <th>Month</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($feecategory as $fee)
                    <tr>
                        <td> {{ $i++ }}</td>
                        <td> {{ $fee->name }}</td>
                        <td> {!! $monthName = date("F", mktime(0, 0, 0, $fee->month, 10)) !!}</td>
                        <td> {{ $fee->amount }}</td>
                        <td><a data-toggle='tooltip' class='btn btn-success btn-xs printBook'
                               href="{{ url('admin/printFeeBook/' . $fee->id . '/' . $student_id ) }}"
                               title='Print Fee Book'> <i class='fa fa-print'></i></a></td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="row" style="display: block">
            <div class="col-md-4 col-sm-12 col-md-offset-4">
                <a class="btn btn-danger  btn-block"
                   href="{{ url('admin/printAllFeeBook/' . $enroll->class_id . '/' . $student_id ) }}">Download
                    All Fee Books</a>
            </div>
        </div>
    @else
        <div class="col-md-12">
            <h4 class="text-center">Sorry!! no fee roles applied yet</h4>
        </div>
    @endif
</div>

