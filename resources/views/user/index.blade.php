@extends('layouts.master')

@section('top')
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
{{-- SweetAlert2 is required for delete confirmation --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
@endsection

@section('content')
<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title">User Management</h3>
    </div>

    @if(Auth::user()->role == 'admin')
    <div class="box-header">
        <a onclick="addForm()" class="btn btn-success"><i class="fa fa-plus"></i> Add User</a>
    </div>
    @endif

    <div class="box-body">
        <table id="user-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@include('user.form')
@endsection

@section('bot')
<script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/validator/validator.min.js') }}"></script>

<script type="text/javascript">
var table = $('#user-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('api.users') }}",
    columns: [
        {data: 'id', name: 'id'},
        {data: 'name', name: 'name'},
        {data: 'email', name: 'email'},
        {data: 'role', name: 'role'},
        {data: 'action', name: 'action', orderable: false, searchable: false}
    ]
});

function addForm() {
    save_method = "add";
    $('input[name=_method]').val('POST');
    $('#modal-form').modal('show');
    $('#modal-form form')[0].reset();
    $('.modal-title').text('Add User');
}

function editForm(id) {
    save_method = 'edit';
    $('input[name=_method]').val('PATCH');
    $('#modal-form form')[0].reset();
    $.ajax({
        url: "{{ url('user') }}/" + id + "/edit",
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('#modal-form').modal('show');
            $('.modal-title').text('Edit User');
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#role').val(data.role);
        },
        error: function () {
            alert("Could not fetch data");
        }
    });
}

function deleteData(id) {
    var csrf_token = $('meta[name="csrf-token"]').attr('content');
    swal({
        title: 'Are you sure?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then(function () {
        $.ajax({
            url: "{{ url('user') }}/" + id,
            type: "POST",
            data: {'_method': 'DELETE', '_token': csrf_token},
            success: function (data) {
                table.ajax.reload();
                swal({title: 'Success!', text: data.message, type: 'success', timer: 1500});
            }
        });
    });
}

$(function () {
    $('#modal-form form').validator().on('submit', function (e) {
        if (!e.isDefaultPrevented()) {
            var id = $('#id').val();
            var url = (save_method == 'add') ? "{{ url('user') }}" : "{{ url('user') }}/" + id;

            $.ajax({
                url: url,
                type: "POST",
                data: new FormData($("#modal-form form")[0]),
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#modal-form').modal('hide');
                    table.ajax.reload();
                    swal({title: 'Saved!', text: data.message, type: 'success', timer: 1500});
                },
                error: function (data) {
                    swal({title: 'Error', text: 'Check your input (Email might be taken)', type: 'error'});
                }
            });
            return false;
        }
    });
});
</script>
@endsection