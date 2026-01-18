@extends('layouts.master')

@section('top')
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')
<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title">List of Item Types</h3>
    </div>

    <div class="box-header">
        <a onclick="addForm()" class="btn btn-success"><i class="fa fa-plus"></i> Add New Item Type</a>
    </div>

    <div class="box-body">
        <table id="item-types-table" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th width="10%">ID</th>
                    <th>Name</th>
                    <th width="20%">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@include('item-types.form')
@endsection

@section('bot')
<script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/validator/validator.min.js') }}"></script>

<script type="text/javascript">
    var table = $('#item-types-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('api.item_types') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    function addForm() {
        save_method = "add";
        $('input[name=_method]').val('POST');
        $('#modal-form').modal('show');
        $('#modal-form form')[0].reset();
        $('.modal-title').text('Add Item Type');
    }

    function editForm(id) {
        save_method = 'edit';
        $('input[name=_method]').val('PATCH');
        $('#modal-form form')[0].reset();
        $.ajax({
            url: "{{ url('item-types') }}" + '/' + id + "/edit",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#modal-form').modal('show');
                $('.modal-title').text('Edit Item Type');
                $('#id').val(data.id);
                $('#name').val(data.name);
            },
            error: function() { alert("Could not fetch data"); }
        });
    }

    function deleteData(id) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        swal({
            title: 'Are you sure?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#d33',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(function () {
            $.ajax({
                url: "{{ url('item-types') }}" + '/' + id,
                type: "POST",
                data: {'_method': 'DELETE', '_token': csrf_token},
                success: function(data) {
                    table.ajax.reload();
                    swal({ title: 'Success!', text: data.message, type: 'success', timer: '1500' })
                },
                error: function(data) {
                    swal({ title: 'Oops...', text: 'Something went wrong!', type: 'error' })
                }
            });
        });
    }

    $(function() {
        $('#modal-form form').validator().on('submit', function (e) {
            if (!e.isDefaultPrevented()) {
                var id = $('#id').val();
                var url = (save_method == 'add') ? "{{ url('item-types') }}" : "{{ url('item-types') . '/' }}" + id;

                $.ajax({
                    url: url,
                    type: "POST",
                    data: new FormData($("#modal-form form")[0]),
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                        swal({ title: 'Success!', text: data.message, type: 'success', timer: '1500' });
                    },
                    error: function(data) {
                        swal({ title: 'Error', text: 'Name already exists or invalid.', type: 'error' });
                    }
                });
                return false;
            }
        });
    });
</script>
@endsection