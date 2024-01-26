<div class="row">
    <div class="col-md-8 col-sm-12 table-responsive">
        <table id="view_details" class="table table-bordered table-hover">
            <tbody>
            <tr>
                <td class="subject"> Book's Name</td>
                <td> :</td>
                <td> {{ $book->name }} </td>
            </tr>
            <tr>
                <td class="subject"> Book's Author</td>
                <td> :</td>
                <td> {{ $book->author }} </td>
            </tr>
            <tr>
                <td class="subject"> Description</td>
                <td> :</td>
                <td> {{ $book->description }} </td>
            </tr>
            <tr>
                <td class="subject"> Class</td>
                <td> :</td>
                <td> {{ $book->stdclass->name }}</td>
            </tr>
            <tr>
                <td class="subject"> Price</td>
                <td> :</td>
                <td> {{ $book->price }} </td>
            </tr>
            <tr>
                <td class="subject"> Total Copies</td>
                <td> :</td>
                <td> {{ $book->total_copies }} </td>
            </tr>
            <tr>
                <td class="subject"> Issued Copies</td>
                <td> :</td>
                <td> {{ $book->issued_book ? $book->issued_book()->count() : 0 }} </td>
            </tr>
            <tr>
                <td class="subject"> Status</td>
                <td> :</td>
                <td> @php $status = $book->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>' ;  @endphp {!! $status !!}   </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-4 col-sm-12">
        <img src="{{ asset($book->file_path) }}" class="img-responsive img-thumbnail" width="200px"/><br/><br/>
        Book's Name : {{ $book->name  }} <br/>
        Book's Author : {{ $book->author  }} <br/>
        Class : {{ $book->stdclass->name }} <br/>
    </div>
</div>