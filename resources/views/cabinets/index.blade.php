@extends('layouts.master')

@section('top')
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
    /* Directory Style Toggles */
    td.details-control {
        background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
    }
    .child-table-wrapper {
        padding: 5px 50px;
        background: #f9f9f9;
    }
    .item-row {
        color: #555;
        font-style: italic;
    }
</style>
@endsection

@section('content')
<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-folder-open"></i> Storage Directory (Cabinet > Drawer > Items)</h3>
        <div class="pull-right">
            <a onclick="addForm()" class="btn btn-success"><i class="fa fa-plus"></i> Add Cabinet</a>
        </div>
    </div>

    <div class="box-body">
        <table id="cabinets-table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="2%"></th> {{-- Toggle Icon --}}
                    <th width="5%">ID</th>
                    <th>Cabinet Title</th>
                    <th>Location</th>
                    <th width="15%">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@include('cabinets.form')
@include('cabinets.drawer_form') 

@include('cabinets.item_form')
@endsection

@section('bot')
<script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/validator/validator.min.js') }}"></script>

<script type="text/javascript">
                var table;
                var save_method;

                // 1. Format the "Child Row" (Drawers and Items)
                function format(d) {
                    var drawerHtml = '<div class="child-table-wrapper" style="padding: 10px 50px; background: #f4f4f4;">' +
                            '<table class="table table-condensed table-bordered" style="background:#fff">' +
                            '<thead><tr class="bg-blue"><th>Drawer Name</th><th>Items Inside</th><th width="100px">Action</th></tr></thead>' +
                            '<tbody>';

                    if (d.drawers.length === 0) {
                        drawerHtml += '<tr><td colspan="3" class="text-center">No drawers found in this cabinet.</td></tr>';
                    }

                    $.each(d.drawers, function (i, drawer) {
                        var itemsList = "";
                        $.each(drawer.items, function (j, item) {
                            // Generate the URL for the show page
                            var itemUrl = "{{ url('items') }}/" + item.id;

                            itemsList += '<a target="blank" href="' + itemUrl + '" style="text-decoration:none;">' +
                                    '<small class="label label-default" style="margin-right:5px; display:inline-block; margin-bottom:2px; cursor:pointer;">' +
                                    '<i class="fa fa-tag"></i> ' + item.name +
                                    ' <span class="badge bg-blue" style="font-size: 9px; padding: 2px 5px; margin-left: 3px;">' + (item.qty || 0) + '</span>' +
                                    '</small></a>';
                        });

                        drawerHtml += '<tr>' +
                                '<td><i class="fa fa-folder-o text-yellow"></i> ' + drawer.title + '</td>' +
                                '<td>' + (itemsList || '<small class="text-muted">Empty</small>') + '</td>' +
                                '<td>' +
                                '<a onclick="editDrawer(' + drawer.id + ', \'' + drawer.title + '\', ' + d.id + ')" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edit</a> ' +
                                '<a onclick="deleteDrawer(' + drawer.id + ')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>' +
                                '</td>' +
                                '</tr>';
                    });

                    drawerHtml += '</tbody></table>' +
                            '<button class="btn btn-xs btn-success" onclick="addDrawerForm(' + d.id + ')"><i class="fa fa-plus"></i> Add Drawer to ' + d.title + '</button>' +
                            '</div>';

                    return drawerHtml;
                }

                $(function () {
                    // Init Main Table
                    table = $('#cabinets-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('api.cabinets') }}",
                        columns: [
                            {"className": 'details-control', "orderable": false, "data": null, "defaultContent": ''},
                            {data: 'id', name: 'id'},
                            {
                                data: 'title',
                                name: 'title',
                                render: function (data, type, row) {
                                    return '<strong><i class="fa fa-hdd-o text-green"></i> ' + data + '</strong> ' +
                                            '<span class="badge bg-gray">' + (row.drawers_count || 0) + ' Drawers</span>';
                                }
                            },
                            {data: 'location', name: 'location'},
                            {data: 'action', name: 'action', orderable: false, searchable: false}
                        ]
                    });

                    // Toggle Expand/Collapse
                    $('#cabinets-table tbody').on('click', 'td.details-control', function () {
                        var tr = $(this).closest('tr');
                        var row = table.row(tr);

                        if (row.child.isShown()) {
                            row.child.hide();
                            tr.removeClass('shown');
                        } else {
                            $.get("{{ url('api/cabinet-details') }}/" + row.data().id, function (data) {
                                row.child(format(data)).show();
                                tr.addClass('shown');
                            });
                        }
                    });

                    // Submit Logic for CABINETS
                    $('#form-cabinet').validator().on('submit', function (e) {
                        if (!e.isDefaultPrevented()) {
                            var id = $('#id').val();
                            var url = (save_method == 'add') ? "{{ url('cabinets') }}" : "{{ url('cabinets') }}/" + id;

                            $.ajax({
                                url: url,
                                type: "POST",
                                data: new FormData($("#form-cabinet")[0]),
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    $('#modal-form').modal('hide');
                                    table.ajax.reload();
                                    swal({title: 'Success!', text: data.message, type: 'success', timer: '1500'});
                                },
                                error: function (data) {
                                    swal({title: 'Oops...', text: 'Check your input data', type: 'error'});
                                }
                            });
                            return false;
                        }
                    });

                    // Submit Logic for DRAWERS
                    $('#form-drawer').validator().on('submit', function (e) {
                        if (!e.isDefaultPrevented()) {
                            $.ajax({
                                url: "{{ route('drawers.store') }}",
                                type: "POST",
                                // Use FormData to ensure the _method field is sent correctly
                                data: new FormData($("#form-drawer")[0]),
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    $('#modal-drawer-form').modal('hide');
                                    table.ajax.reload(null, false); // Reload without collapsing rows
                                    swal({title: 'Success!', text: data.message, type: 'success', timer: '1500'});
                                },
                                error: function (data) {
                                    var errorData = data.responseJSON;
                                    // Get the first validation error message from Laravel
                                    var msg = (errorData && errorData.errors) ? Object.values(errorData.errors)[0][0] : 'Something went wrong';

                                    swal({
                                        title: 'Oops...',
                                        text: msg,
                                        type: 'error'
                                    });
                                }
                            });
                            return false;
                        }
                    });
                });

                // --- Cabinet Functions ---
                function addForm() {
                    save_method = "add";
                    $('input[name=_method]').val('POST');
                    $('#modal-form').modal('show');
                    $('#form-cabinet')[0].reset();
                    $('.modal-title').text('Add Cabinet');
                }

                function editForm(id) {
                    save_method = 'edit';
                    $('input[name=_method]').val('PATCH');
                    $.ajax({
                        url: "{{ url('cabinets') }}" + '/' + id + "/edit",
                        type: "GET",
                        dataType: "JSON",
                        success: function (data) {
                            $('#modal-form').modal('show');
                            $('.modal-title').text('Edit Cabinet');
                            $('#id').val(data.id);
                            $('#title').val(data.title);
                            $('#location').val(data.location);
                        }
                    });
                }

                // --- Drawer Functions ---
                function addDrawerForm(cabinetId) {
                    $('#form-drawer')[0].reset();
                    $('#drawer_id').val(''); // Clear ID for "Add"
                    $('#drawer_cabinet_id').val(cabinetId);
                    $('#modal-drawer-form').modal('show');
                    $('.modal-title').text('Add New Drawer');
                }

                function editDrawer(id, title, cabinetId) {
                    save_method = 'edit';
                    $('#form-drawer')[0].reset();

                    // Force method to POST to match your route
                    $('#form-drawer input[name=_method]').val('POST');

                    $('#drawer_id').val(id);
                    $('#drawer_title').val(title);
                    $('#drawer_cabinet_id').val(cabinetId);
                    $('#modal-drawer-form').modal('show');
                    $('.modal-title').text('Edit Drawer');
                }

                function deleteDrawer(id) {
                    var csrf_token = $('meta[name="csrf-token"]').attr('content');
                    swal({
                        title: 'Delete this drawer?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then(function () {
                        $.ajax({
                            url: "{{ url('drawers') }}/" + id,
                            type: "POST",
                            data: {'_method': 'DELETE', '_token': csrf_token},
                            success: function (data) {
                                table.ajax.reload();
                                swal({title: 'Deleted!', text: data.message, type: 'success', timer: '1500'});
                            },
                            error: function (data) {
                                swal({title: 'Error', text: data.responseJSON.message, type: 'error'});
                            }
                        });
                    });
                }

                function deleteData(id) {
                    var csrf_token = $('meta[name="csrf-token"]').attr('content');
                    swal({
                        title: 'Delete Cabinet?',
                        text: "All drawers must be empty!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!'
                    }).then(function () {
                        $.ajax({
                            url: "{{ url('cabinets') }}/" + id,
                            type: "POST",
                            data: {'_method': 'DELETE', '_token': csrf_token},
                            success: function (data) {
                                table.ajax.reload();
                                swal({title: 'Success!', text: data.message, type: 'success', timer: '1500'});
                            },
                            error: function (data) {
                                swal({title: 'Oops...', text: data.responseJSON.message, type: 'error'});
                            }
                        });
                    });
                }
</script>
@endsection