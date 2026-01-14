@extends('layouts.master')

@section('top')
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
    /* Directory Hierarchy Styling */
    td.details-control {
        background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
        cursor: pointer;
        width: 30px;
    }
    tr.shown td.details-control {
        background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
    }
    .child-table-wrapper {
        padding: 15px 20px 15px 50px;
        background: #fdfdfd;
        border-left: 5px solid #00a65a; /* Visual link to parent cabinet */
    }
    .drawer-row:hover {
        background-color: #f1f1f1 !important;
    }
    .item-badge {
        margin-right: 5px;
        display: inline-block;
        margin-bottom: 3px;
        transition: transform 0.2s;
    }
    .item-badge:hover {
        transform: scale(1.05);
    }
</style>
@endsection

@section('content')
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-folder-open"></i> Storage Directory</h3>
        <div class="box-tools pull-right">
            <button onclick="addForm()" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add New Cabinet</button>
        </div>
    </div>

    <div class="box-body">
        <table id="cabinets-table" class="table table-bordered table-hover" style="width:100%">
            <thead>
                <tr>
                    <th></th> {{-- Toggle Icon --}}
                    <th width="50px">ID</th>
                    <th>Cabinet Title</th>
                    <th>Location</th>
                    <th width="150px">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Load All Modals --}}
@include('cabinets.form')
@include('cabinets.drawer_form') 


@endsection

@section('bot')
<script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/validator/validator.min.js') }}"></script>

<script type="text/javascript">
                var table;
                var save_method;

                // 1. FORMAT NESTED ROWS (Drawers > Items)
                function format(d) {
                    var drawerHtml = '<div class="child-table-wrapper">' +
                            '<table class="table table-condensed table-bordered" style="background:#fff">' +
                            '<thead>' +
                            '<tr class="bg-blue">' +
                            '<th width="30%">Drawer Name</th>' +
                            '<th>Items Inside (Click to view)</th>' +
                            '<th width="180px">Drawer Actions</th>' +
                            '</tr>' +
                            '</thead>' +
                            '<tbody>';

                    if (!d.drawers || d.drawers.length === 0) {
                        drawerHtml += '<tr><td colspan="3" class="text-center text-muted">No drawers found.</td></tr>';
                    } else {
                        $.each(d.drawers, function (i, drawer) {
                            var itemsList = "";
                            if (drawer.items && drawer.items.length > 0) {
                                $.each(drawer.items, function (j, item) {
                                    var itemUrl = "{{ url('items') }}/" + item.id;
                                    itemsList += '<a href="' + itemUrl + '" target="_blank" class="item-badge">' +
                                            '<span class="label label-default" style="border:1px solid #ccc; color:#333;">' +
                                            '<i class="fa fa-tag text-primary"></i> ' + item.name +
                                            ' <span class="badge bg-blue">' + (item.qty || 0) + '</span>' +
                                            '</span></a>';
                                });
                            }

                            drawerHtml += '<tr class="drawer-row">' +
                                    '<td><i class="fa fa-folder-o text-yellow"></i> ' + drawer.title + '</td>' +
                                    '<td>' + (itemsList || '<small class="text-muted">Empty</small>') + '</td>' +
                                    '<td>' +
                                    '<div class="btn-group">' +
                                    '<button onclick="editDrawer(' + drawer.id + ', \'' + drawer.title + '\', ' + d.id + ')" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></button>' +
                                    '<button onclick="deleteDrawer(' + drawer.id + ')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>' +
                                    '<button onclick="addItemToDrawer(' + drawer.id + ')" class="btn btn-xs btn-default"><i class="fa fa-plus"></i> Item</button>' +
                                    '</div>' +
                                    '</td>' +
                                    '</tr>';
                        });
                    }

                    drawerHtml += '</tbody></table>' +
                            '<button class="btn btn-xs btn-success" onclick="addDrawerForm(' + d.id + ')"><i class="fa fa-plus"></i> Add Drawer to ' + d.title + '</button>' +
                            '</div>';

                    return drawerHtml;
                }

                $(function () {
                    // 2. INITIALIZE MAIN TABLE
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

                    // 3. EXPAND/COLLAPSE LOGIC
                    $('#cabinets-table tbody').on('click', 'td.details-control', function () {
                        var tr = $(this).closest('tr');
                        var row = table.row(tr);

                        if (row.child.isShown()) {
                            row.child.hide();
                            tr.removeClass('shown');
                        } else {
                            // Fetch deep data from API
                            $.get("{{ url('api/cabinet-details') }}/" + row.data().id, function (data) {
                                row.child(format(data)).show();
                                tr.addClass('shown');
                            });
                        }
                    });

                    // 4. SUBMIT CABINET (ADD/EDIT)
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

                    // 5. SUBMIT DRAWER (ADD/EDIT)
                    $('#form-drawer').validator().on('submit', function (e) {
                        if (!e.isDefaultPrevented()) {
                            $.ajax({
                                url: "{{ route('drawers.store') }}",
                                type: "POST",
                                data: new FormData($("#form-drawer")[0]),
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    $('#modal-drawer-form').modal('hide');
                                    // Use reload(null, false) so current expanded rows stay open
                                    table.ajax.reload(null, false);
                                    swal({title: 'Success!', text: data.message, type: 'success', timer: '1500'});
                                }
                            });
                            return false;
                        }
                    });
                });

                // --- CABINET FUNCTIONS ---
                function addForm() {
                    save_method = "add";
                    $('input[name=_method]').val('POST');
                    $('#modal-form').modal('show');
                    $('#form-cabinet')[0].reset();
                    $('.modal-title').text('Add New Cabinet');
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
                            $('#location_id').val(data.location_id);
                        }
                    });
                }

                // --- DRAWER FUNCTIONS ---
                function addDrawerForm(cabinetId) {
                    $('#form-drawer')[0].reset();
                    $('#drawer_id').val('');
                    $('#drawer_cabinet_id').val(cabinetId);
                    $('#modal-drawer-form').modal('show');
                    $('.modal-title').text('Add Drawer');
                }

                function editDrawer(id, title, cabinetId) {
                    $('#form-drawer')[0].reset();
                    $('#drawer_id').val(id);
                    $('#drawer_title').val(title);
                    $('#drawer_cabinet_id').val(cabinetId);
                    $('#modal-drawer-form').modal('show');
                    $('.modal-title').text('Edit Drawer');
                }

                function deleteDrawer(id) {
                    swal({
                        title: 'Delete Drawer?',
                        text: "This will fail if items are inside!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete!'
                    }).then(function () {
                        $.ajax({
                            url: "{{ url('drawers') }}/" + id,
                            type: "POST",
                            data: {'_method': 'DELETE', '_token': $('meta[name="csrf-token"]').attr('content')},
                            success: function (data) {
                                table.ajax.reload(null, false);
                                swal({title: 'Deleted!', text: data.message, type: 'success'});
                            },
                            error: function (data) {
                                swal({title: 'Error', text: data.responseJSON.message, type: 'error'});
                            }
                        });
                    });
                }

                function deleteData(id) {
                    swal({
                        title: 'Delete Cabinet?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete!'
                    }).then(function () {
                        $.ajax({
                            url: "{{ url('cabinets') }}/" + id,
                            type: "POST",
                            data: {'_method': 'DELETE', '_token': $('meta[name="csrf-token"]').attr('content')},
                            success: function (data) {
                                table.ajax.reload();
                                swal({title: 'Success!', text: data.message, type: 'success'});
                            }
                        });
                    });
                }
</script>
@endsection