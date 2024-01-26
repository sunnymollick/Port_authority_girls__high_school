<div class="row">
    <div class="col-md-8 col-sm-12 table-responsive">
        <table id="view_details" class="table table-bordered table-hover">
            <tbody>
            <tr>
                <td class="subject"> Subject Name</td>
                <td> :</td>
                <td> {{ $subject->name }} </td>
            </tr>
            <tr>
                <td class="subject"> Class</td>
                <td> :</td>
                <td> {{ $subject->stdclass ? $subject->stdclass->name : '' }} </td>
            </tr>
            <tr>
                <td class="subject"> Subject Code</td>
                <td> :</td>
                <td> {{ $subject->subject_code }} </td>
            </tr>
            <tr>
                <td class="subject"> Subject Order</td>
                <td> :</td>
                <td> {{ $subject->subject_order }} </td>
            </tr>
            <tr>
                <td class="subject"> Subject's Teacher</td>
                <td> :</td>
                <td> {{ $subject->teacher ? $subject->teacher->name : '' }}</td>
            </tr>
            <tr>
                <td class="subject"> Subject Marks</td>
                <td> :</td>
                <td> {{ $subject->subject_marks }} </td>
            </tr>
            <tr>
                <td class="subject"> Pass Marks</td>
                <td> :</td>
                <td> {{ $subject->pass_marks }} </td>
            </tr>
            <tr>
                <td class="subject"> Theory Marks</td>
                <td> :</td>
                <td> {{ $subject->theory_marks }} </td>
            </tr>
            <tr>
                <td class="subject"> Theory Pass Marks</td>
                <td> :</td>
                <td> {{ $subject->theory_pass_marks }} </td>
            </tr>
            <tr>
                <td class="subject"> MCQ Marks</td>
                <td> :</td>
                <td> {{ $subject->mcq_marks }} </td>
            </tr>
            <tr>
                <td class="subject"> MCQ Pass Marks</td>
                <td> :</td>
                <td> {{ $subject->mcq_pass_marks }} </td>
            </tr>
            <tr>
                <td class="subject"> Practical Marks</td>
                <td> :</td>
                <td> {{ $subject->practical_marks }} </td>
            </tr>
            <tr>
                <td class="subject"> Practical Pass Marks</td>
                <td> :</td>
                <td> {{ $subject->practical_pass_marks }} </td>
            </tr>
            </tbody>
        </table>
    </div>
    @if($subject->teacher)
        <div class="col-md-4 col-sm-12">
            <img src="{{asset($subject->teacher->file_path) }}" class="img-responsive img-thumbnail"
                 width="200px"/><br/><br/>
            Subject's Teacher : {{ $subject->teacher->name  }}
        </div>
    @endif
</div>