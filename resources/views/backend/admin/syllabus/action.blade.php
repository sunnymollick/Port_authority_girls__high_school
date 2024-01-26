@can('syllabus-edit')
    <a data-toggle='tooltip' class='btn btn-primary btn-xs edit' id='{{$id}}' title='Edit'> <i
            class='fa fa-pencil-square-o'></i></a>
@endcan
@can('syllabus-delete')
    <a data-toggle='tooltip' class='btn btn-danger btn-xs delete' id='{{$id}}' title='Delete'> <i
            class='fa fa-trash'></i></a>
@endcan
