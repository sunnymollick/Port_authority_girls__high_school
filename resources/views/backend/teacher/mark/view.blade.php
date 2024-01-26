<hr/>
@if(count($data) > 0)
    @php  $no = 1; @endphp
    <div class="col-md-12">
        <div class="col-md-4 col-md-offset-4">
            <div class="card card_text">
                <div class="card-body text-center">
                    <h3> {{ $data[0]->exam_name }}</h3>
                    <h4> Class : {{ $data[0]->class_name }} </h4>
                    <h4> Section : {{ $data[0]->section }} </h4>
                    <h4> Subject : {{ $data[0]->sub_name }} </h4>
                </div>
            </div>
            <hr/>
        </div>
    </div>
    <form id='create' action="" enctype="multipart/form-data" method="post" accept-charset="utf-8">
        <input type="hidden" name="exam_id" value="{{ $data[0]->exam_id }}">
        <input type="hidden" name="class_id" value="{{ $data[0]->class_id }}">
        <input type="hidden" name="section_id" value="{{ $data[0]->section_id }}">
        <input type="hidden" name="subject_id" value="{{ $data[0]->sub_id }} ">
        {{--<input type="hidden" name="teacher_id" value="{{ $subject->teacher ? $subject->teacher->id : '' }} ">--}}
        <div id="status"></div>
        <div class="col-md-12 col-sm-12 table-responsive">
            <table id="manage_all" class="table table-collapse table-bordered table-hover">
                <thead>
                <tr>
                    <th class="serial">#</th>
                    <th class="std_id">Student Code</th>
                    <th class="std_name">Name</th>
                    <th class="text-center">Roll</th>
                    <th class="marks">Theory Marks</th>
                    <th class="marks">MCQ Marks</th>
                    <th class="marks">Practical Marks</th>
                    <th class="marks">CT Marks</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $row)
                    <tr>
                        <input type="hidden" name="student_code[]" value="{{ $row->std_code }}">
                        <td>{{ $no++ }}</td>
                        <td>{{ $row->std_code }}</td>
                        <td>{{ $row->std_name }}</td>
                        <td class="text-center">{{ $row->std_roll }}</td>
                        <td><input type="text" class="form-control" name="theory_{{$row->std_code}}"
                                   value="{{$row->theory_marks ? $row->theory_marks : ''}}"></td>
                        <td><input type="text" class="form-control" name="mcq_{{$row->std_code}}"
                                   value="{{$row->mcq_marks ? $row->mcq_marks : ''}}"></td>
                        <td><input type="text" class="form-control" name="practical_{{$row->std_code}}"
                                   value="{{$row->practical_marks ? $row->practical_marks : ''}}"></td>
                        <td><input type="text" class="form-control" name="ct_{{$row->std_code}}"
                                   value="{{$row->ct_marks ? $row->ct_marks : ''}}"></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="clearfix"></div>
        <div class="form-group col-md-12">
            <button type="submit" class="btn btn-success" id="submit"><span class="fa fa-save fa-fw"></span> Save
            </button>
            <img id="loaderSubmit" src="{{asset('assets/images/loadingg.gif')}}" width="20px">
        </div>
    </form>
@else
    <div class="col-md-12 text-center">
        <div class="alert alert-danger">
            <strong>Sorry!! no records have found </strong>
        </div>
    </div>
@endif
<style>
    .serial {
        width: 5%;
    }

    .std_id {
        width: 13%;
    }

    .std_name {
        width: 25%;
    }

    .marks {
        width: 12%;
    }
</style>
<script type="text/javascript">

    $('#loaderSubmit').hide();

    $('#create').validate({// <- attach '.validate()' to your form
        // Rules for form validation
        rules: {
            name: {
                required: true
            },
            phone: {
                required: true,
                number: true
            }
        },
        // Messages for form validation
        messages: {
            name: {
                required: 'Enter name'
            }
        },
        submitHandler: function (form) {
            swal({
                title: "Are you sure?",
                text: "Please check it before submit!!",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
                confirmButtonAttendance: "btn-danger",
                confirmButtonText: "Submit",
                cancelButtonText: "Cancel"
            }, function () {

                var myData = new FormData($("#create")[0]);
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                myData.append('_token', CSRF_TOKEN);


                $.ajax({
                    url: 'updateMarks',
                    data: myData,
                    type: 'POST',
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.type === 'success') {
                            getMarks();
                            swal("Done!", data.message, "success");
                        } else if (data.type === 'danger') {
                            swal("Error!", data.message, "error");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        swal("Error!", "Try again", "error");
                    }
                });
            });
        }
    });

</script>