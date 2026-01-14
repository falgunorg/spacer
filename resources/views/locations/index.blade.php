@extends('layouts.master')


@section('top')
<!-- Log on to codeastro.com for more projects! -->
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')
<div class="box box-success">

    <div class="box-header">
        <h3 class="box-title">List of Locations</h3>
    </div>

    <div class="box-header">
        <a onclick="addForm()" class="btn btn-success" ><i class="fa fa-plus"></i> Add a New Location</a>
    </div>


    <!-- /.box-header -->
    <div class="box-body">
        <table id="locations-table" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Cabinets</th> 
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <!-- /.box-body -->
</div><!-- Log on to codeastro.com for more projects! -->

@include('locations.form')

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
            var table = $('#locations-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('api.locations') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {
                        data: 'name',
                        name: 'name',
                        render: function (data, type, row) {
                            // row.items_count comes from withCount('items') in controller
                            return data + ' <span class="label label-info">' + (row.items_count || 0) + ' Items</span>';
                        }
                    },
                    {
                        data: 'cabinets',
                        name: 'cabinets',
                        orderable: false,
                        searchable: false
                    }, // New Column Definition
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });

            function addForm() {
                save_method = "add";
                $('input[name=_method]').val('POST');
                $('#modal-form').modal('show');
                $('#modal-form form')[0].reset();
                $('.modal-title').text('Add Locations');
            }

            function editForm(id) {
                save_method = 'edit';
                $('input[name=_method]').val('PATCH');
                $('#modal-form form')[0].reset();
                $.ajax({
                    url: "{{ url('locations') }}" + '/' + id + "/edit",
                    type: "GET",
                    dataType: "JSON",
                    success: function (data) {
                        $('#modal-form').modal('show');
                        $('.modal-title').text('Edit Locations');

                        $('#id').val(data.id);
                        $('#name').val(data.name);
                    },
                    error: function () {
                        alert("Nothing Data");
                    }
                });
            }

            function deleteData(id) {
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: '#d33',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then(function () {
                    $.ajax({
                        url: "{{ url('locations') }}" + '/' + id,
                        type: "POST",
                        data: {'_method': 'DELETE', '_token': csrf_token},
                        success: function (data) {
                            table.ajax.reload();
                            swal({
                                title: 'Success!',
                                text: data.message,
                                type: 'success',
                                timer: '1500'
                            })
                        },
                        error: function (data) {
                            // Fix: Extract the message from the JSON response
                            var errorData = data.responseJSON;
                            swal({
                                title: 'Oops...',
                                text: errorData.message ? errorData.message : 'Something went wrong!',
                                type: 'error',
                                // Removed timer so user can actually read why it failed
                            })
                        }
                    });
                });
            }

            $(function () {
                $('#modal-form form').validator().on('submit', function (e) {
                    if (!e.isDefaultPrevented()) {
                        var id = $('#id').val();
                        if (save_method == 'add')
                            url = "{{ url('locations') }}";
                        else
                            url = "{{ url('locations') . '/' }}" + id;

                        $.ajax({
                            url: url,
                            type: "POST",
                            //hanya untuk input data tanpa dokumen
//                      data : $('#modal-form form').serialize(),
                            data: new FormData($("#modal-form form")[0]),
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                $('#modal-form').modal('hide');
                                table.ajax.reload();
                                swal({
                                    title: 'Success!',
                                    text: data.message,
                                    type: 'success',
                                    timer: '1500'
                                })
                            },
                            error: function (data) {
                                swal({
                                    title: 'Oops...',
                                    text: data.message,
                                    type: 'error',
                                    timer: '1500'
                                })
                            }
                        });
                        return false;
                    }
                });
            });
</script>

@endsection
