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
        <table id="items-table" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th>Name</th>
                      <th>Type/Category</th>
                    <th>Qty.</th>
                    <th>Location</th>
                    <th>Image</th>
                    <th>By</th>
                    <th width="15%">Actions</th>
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
                var save_method;
                var table = $('#items-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('api.items') }}",
                    columns: [
                        {data: 'serial_number', name: 'serial_number'},
                        {data: 'name', name: 'name'},
                        {data: 'item_type', name: 'item_type'},
                        {data: 'qty', name: 'qty'},
                        {data: 'location', name: 'location'},
                        {data: 'show_photo', name: 'show_photo', orderable: false, searchable: false},
                        {data: 'by', name: 'by', orderable: false},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ]
                });

                $(document).ready(function () {
                    // 1. Toggle visibility and validation based on Trackable choice
                    $('#trackable').on('change', function () {
                        if ($(this).val() === 'Yes') {
                            $('#storage_group').show();
                            // Set requirements for Trackable mode
                            $('#cabinet_id, #drawer_id').attr('required', 'required');
                            
                        } else {
                            $('#storage_group').hide();
                          

                            // Set requirements for Manual mode
                           
                            $('#cabinet_id, #drawer_id').val('').removeAttr('required');
                        }
                    });

                    // 2. DEPENDENT DROPDOWN: Location -> Cabinet
                    $('#location_id').on('change', function () {
                        var locationID = $(this).val();
                        if (locationID) {
                            $.ajax({
                                url: "{{ url('api/cabinets-by-location') }}/" + locationID,
                                type: "GET",
                                dataType: "json",
                                success: function (data) {
                                    $('#cabinet_id').empty().append('<option value="" selected disabled>-- Select Cabinet --</option>');
                                    $('#drawer_id').empty().append('<option value="" selected disabled>-- Select Cabinet First --</option>');
                                    $.each(data, function (key, value) {
                                        $('#cabinet_id').append('<option value="' + value.id + '">' + value.title + '</option>');
                                    });
                                }
                            });
                        } else {
                            $('#cabinet_id, #drawer_id').empty().append('<option value="" selected disabled>-- Select Location First --</option>');
                        }
                    });

                    // 3. DEPENDENT DROPDOWN: Cabinet -> Drawer
                    $('#cabinet_id').on('change', function () {
                        var cabinetID = $(this).val();
                        if (cabinetID) {
                            $.ajax({
                                url: "{{ url('api/drawers-by-cabinet') }}/" + cabinetID,
                                type: "GET",
                                dataType: "json",
                                success: function (data) {
                                    $('#drawer_id').empty().append('<option value="" selected disabled>-- Select Drawer --</option>');
                                    $.each(data, function (key, value) {
                                        $('#drawer_id').append('<option value="' + value.id + '">' + value.title + '</option>');
                                    });
                                }
                            });
                        }
                    });
                });

                function addForm() {
                    save_method = "add";
                    $('input[name=_method]').val('POST');
                    $('#modal-form').modal('show');
                    $('#modal-form form')[0].reset();
                    $('.modal-title').text('Add Items');

                    // Initial state for Add
                    $('#cabinet_id, #drawer_id').empty().append('<option value="" selected disabled>-- Select Location First --</option>');
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
                            $('#description').val(data.description);
                            $('#qty').val(data.qty);
                            $('#item_type').val(data.item_type);

                            // 1. Set Location (Always visible)
                            $('#location_id').val(data.location_id);

                            // 2. Set Trackable and trigger the UI toggle
                            $('#trackable').val(data.trackable).trigger('change');

                            if (data.trackable === 'No') {
//                              
                            } else {
                                // Logic to load nested dropdowns sequentially for Edit
                                if (data.location_id) {
                                    $.get("{{ url('api/cabinets-by-location') }}/" + data.location_id, function (cabinets) {
                                        $('#cabinet_id').empty().append('<option value="">-- Select Cabinet --</option>');
                                        $.each(cabinets, function (k, v) {
                                            var selected = (v.id == data.cabinet_id) ? 'selected' : '';
                                            $('#cabinet_id').append('<option value="' + v.id + '" ' + selected + '>' + v.title + '</option>');
                                        });

                                        if (data.cabinet_id) {
                                            $.get("{{ url('api/drawers-by-cabinet') }}/" + data.cabinet_id, function (drawers) {
                                                $('#drawer_id').empty().append('<option value="">-- Select Drawer --</option>');
                                                $.each(drawers, function (k, v) {
                                                    var selected = (v.id == data.drawer_id) ? 'selected' : '';
                                                    $('#drawer_id').append('<option value="' + v.id + '" ' + selected + '>' + v.title + '</option>');
                                                });
                                            });
                                        }
                                    });
                                }
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
                                    var errors = data.responseJSON;
                                    var errorMsg = "Something went wrong";
                                    if (errors && errors.errors) {
                                        errorMsg = Object.values(errors.errors)[0][0];
                                    } else if (errors && errors.message) {
                                        errorMsg = errors.message;
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
