<tr class="removeItem">
    <input type="hidden" name="std_code_{{$data[0]->barcode}}" value="{{$data[0]->std_code}}"/>
    <input type="hidden" name="class_id_{{$data[0]->barcode}}" value="{{$data[0]->class_id}}"/>
    <input type="hidden" name="section_id_{{$data[0]->barcode}}" value="{{$data[0]->section_id}}"/>
    <input type="hidden" name="month_{{$data[0]->barcode}}" value="{{$data[0]->month}}"/>
    <td><input type="text" name="barcode_data[]" readonly value="{{$data[0]->barcode}}"/></td>
    <td><input style="width: 200px" type="text" name="student_name_{{$data[0]->barcode}}" readonly
               value="{{$data[0]->name}}"/>
    </td>
    <td><input type="text" style="width: 100px" name="std_code_{{$data[0]->barcode}}" readonly
               value="{{$data[0]->std_code}}"/></td>
    <td><input type="text" style="width: 50px" name="class_name_{{$data[0]->barcode}}" readonly
               value="{{$data[0]->class_name}}"/></td>
    <td><input type="text" name="section_name_{{$data[0]->barcode}}" readonly value="{{$data[0]->section_name}}"/>
    </td>
    <td><input type="text" style="width: 50px" name="roll_{{$data[0]->barcode}}" readonly value="{{$data[0]->roll}}"/>
    </td>
    <td>{{ date("F", mktime(0, 0, 0, $data[0]->month , 10))  }}</td>
    <td><input type="text" name="total_amount_{{$data[0]->barcode}}" readonly value="{{$data[0]->total_amount}}"/>
    </td>
    <td>
        <button type="button" href="javascript:;" class='btn btn-danger btn-xs pull-right removeItem'><i
                class="fa fa-remove"></i>
        </button>
    </td>
</tr>

