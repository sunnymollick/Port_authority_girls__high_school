@can('fee_category-view')
    <a data-toggle='tooltip' class='btn btn-info btn-xs view' id='{{$id}}' title='View'> <i
            class='fa fa-eye'></i></a>
@endcan
@can('fee_category-delete')
    <a data-toggle='tooltip' class='btn btn-danger btn-xs  delete' id='{{$id}}' title='Delete'> <i
            class='fa fa-trash-o'></i></a>
@endcan