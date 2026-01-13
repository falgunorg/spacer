@extends('layouts.master')


@section('top')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')
<div class="box box-success">

    <div class="box-header">
        <h3 class="box-title">List of Items</h3>
        <span class="pull-right">
            <a onclick="addForm()" class="btn btn-success" style="margin-top: -8px;"><i class="fa fa-plus"></i> Add Items</a>
            <a  class="btn btn-warning" href="{{route('tokens')}}" style="margin-top: -8px;"> Tokens</a>
        </span>
    </div>


    <!-- /.box-header -->
    <div class="box-body table-responsive">
        <table id="items-table" class="table table-bordered table-hover table-striped table-responsive">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Instructions</th>
                    <th>Condition</th>
                    <th>Location</th>
                    <th>Qty.</th>
                    <th>Image</th>
                    <th>Category</th>
                    <th>By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@include('items.form')

@endsection

@section('bot')

<!-- DataTables -->
<script src=" {{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }} "></script>
<script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }} "></script>

{{-- Validator --}}
<script src="{{ asset('assets/validator/validator.min.js') }}"></script>

{{--<script>--}}
{{--$(function () {--}}
{{--$('#items-table').DataTable()--}}
{{--$('#example2').DataTable({--}}
{{--'paging'      : true,--}}
{{--'lengthChange': false,--}}
{{--'searching'   : false,--}}
{{--'ordering'    : true,--}}
{{--'info'        : true,--}}
{{--'autoWidth'   : false--}}
{{--})--}}
{{--})--}}
{{--</script>--}}


<script type="text/javascript">
                var table = $('#items-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('api.items') }}",
                    columns: [
                        {data: 'serial_number', name: 'serial_number'},
                        {data: 'name', name: 'name'},
                        {data: 'price', name: 'price'},
                        {data: 'description', name: 'description'},
                        {data: 'instructions', name: 'instructions'},
                        {data: 'condition', name: 'condition'},
                        {data: 'location', name: 'location'},
                        {data: 'qty', name: 'qty'},
                        {data: 'show_photo', name: 'show_photo', orderable: false, searchable: false},
                        {data: 'category_name', name: 'category_name', orderable: false},
                        {data: 'by', name: 'by', orderable: false},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ]
                });

                // --- NEW LOGIC: TRACKABLE TOGGLE & DEPENDENT DROPDOWN ---
                $(document).ready(function () {
                    // 1. Toggle visibility based on Trackable choice
                    $('#trackable').on('change', function () {
                        if ($(this).val() === 'Yes') {
                            $('#storage_group').show();
                            $('#location_group').hide();
                            $('#location').val(''); // Clear manual location
                        } else {
                            $('#storage_group').hide();
                            $('#location_group').show();
                            $('#cabinet_id, #drawer_id').val(''); // Clear cabinet/drawer
                        }
                    });

                    // 2. Load Drawers when Cabinet is selected
                    $('#cabinet_id').on('change', function () {
                        var cabinetID = $(this).val();
                        if (cabinetID) {
                            $.ajax({
                                url: "{{ url('api/cabinet-details') }}/" + cabinetID,
                                type: "GET",
                                dataType: "json",
                                success: function (data) {
                                    $('#drawer_id').empty();
                                    $('#drawer_id').append('<option value="" selected disabled>-- Select Drawer --</option>');
                                    $.each(data.drawers, function (key, value) {
                                        $('#drawer_id').append('<option value="' + value.id + '">' + value.title + '</option>');
                                    });
                                }
                            });
                        } else {
                            $('#drawer_id').empty();
                        }
                    });
                });

                function addForm() {
                    save_method = "add";
                    $('input[name=_method]').val('POST');
                    $('#modal-form').modal('show');
                    $('#modal-form form')[0].reset();
                    $('.modal-title').text('Add Items');

                    // Reset view to default (Trackable: No)
                    $('#trackable').val('No').trigger('change');
                }

                function editForm(id) {
                    save_method = 'edit';
                    $('input[name=_method]').val('PATCH');
                    $('#modal-form form')[0].reset();

                    $.ajax({
                        url: "{{ url('items') }}" + '/' + id + "/edit",
                        type: "GET",
                        dataType: "JSON",
                        success: function (data) {
                            $('#modal-form').modal('show');
                            $('.modal-title').text('Edit Items');

                            $('#id').val(data.id);
                            $('#name').val(data.name);
                            $('#price').val(data.price);
                            $('#description').val(data.description);
                            $('#instructions').val(data.instructions);
                            $('#condition').val(data.condition);
                            $('#qty').val(data.qty);
                            $('#category_id').val(data.category_id);

                            // Set Trackable status and trigger toggle
                            $('#trackable').val(data.trackable).trigger('change');

                            if (data.trackable === 'No') {
                                $('#location').val(data.location);
                            } else {
                                $('#cabinet_id').val(data.cabinet_id);

                                // Manually load drawers for this cabinet and select the current one
                                $.get("{{ url('api/cabinet-details') }}/" + data.cabinet_id, function (details) {
                                    $('#drawer_id').empty();
                                    $.each(details.drawers, function (key, value) {
                                        var selected = (value.id == data.drawer_id) ? 'selected' : '';
                                        $('#drawer_id').append('<option value="' + value.id + '" ' + selected + '>' + value.title + '</option>');
                                    });
                                });
                            }
                        },
                        error: function () {
                            swal({title: 'Error', text: 'Could not fetch data', type: 'error'});
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
                            url: "{{ url('items') }}" + '/' + id,
                            type: "POST",
                            data: {'_method': 'DELETE', '_token': csrf_token},
                            success: function (data) {
                                table.ajax.reload();
                                swal({title: 'Success!', text: data.message, type: 'success', timer: '1500'});
                            }
                        });
                    });
                }

                $(function () {
                    $('#modal-form form').validator().on('submit', function (e) {
                        if (!e.isDefaultPrevented()) {
                            var id = $('#id').val();
                            var url = (save_method == 'add') ? "{{ url('items') }}" : "{{ url('items') }}/" + id;

                            $.ajax({
                                url: url,
                                type: "POST",
                                data: new FormData($("#modal-form form")[0]),
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    $('#modal-form').modal('hide');
                                    table.ajax.reload();
                                    swal({title: 'Success!', text: data.message, type: 'success', timer: '1500'});
                                },
                                error: function (data) {
                                    // Extract Laravel validation error messages
                                    var errors = data.responseJSON;
                                    var errorMsg = errors.message;
                                    if (errors.errors) {
                                        errorMsg = Object.values(errors.errors)[0][0];
                                    }
                                    swal({title: 'Oops...', text: errorMsg, type: 'error'});
                                }
                            });
                            return false;
                        }
                    });
                });
</script>

@endsection
