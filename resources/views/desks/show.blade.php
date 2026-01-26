@extends('layouts.master')

@section('top')
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
    /* Styling to ensure the QR code fits nicely */
    .qr-code-container svg {
        width: 150px;
        height: 150px;
        margin-bottom: 10px;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-body box-profile">
                <div class="text-center qr-code-container">
                    {!! $qrcode !!}
                </div>
                <h3 class="profile-username text-center">{{ $desk->title }}</h3>
                <p class="text-muted text-center">Desk Detail</p>

                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Location</b> <a class="pull-right" href="{{route('locations.show',$desk->location->id)}}">{{ optional($desk->location)->name }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Total Compartments</b> <a class="pull-right">{{ $desk->deskparts->count() }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Total Items</b> 
                        <a class="pull-right">
                            {{ $desk->deskparts->sum(function($deskpart) { return $deskpart->items->count(); }) }}
                        </a>
                    </li>
                </ul>

                <button onclick="addItem()" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> <b>Add Item</b></button>
                <a href="{{ route('locations.index') }}" class="btn btn-default btn-block"><b>Back to Locations</b></a>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#deskparts" data-toggle="tab">Compartments</a></li>
                <li><a href="#items" data-toggle="tab">Stored Items</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane" id="deskparts">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>Compartment Name</th>
                                <th>Item Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($desk->deskparts as $deskpart)
                            <tr>
                                <td>{{ $deskpart->title }}</td>
                                <td><span class="label label-info">{{ $deskpart->items->count() }} Items</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane" id="items">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Serial No.</th>
                                <th>Item Name</th>
                                <th>Compartment</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($desk->deskparts as $deskpart)
                            @foreach($deskpart->items as $item)
                            <tr>
                                <td>
                                    <img src="{{ $item->show_photo }}" width="40" class="img-thumbnail">
                                </td>
                                <td>{{ $item->serial_number }}</td>
                                <td>{{ $item->name }}</td>
                                <td><span class="label label-warning">{{ $deskpart->title }}</span></td>
                                <td>{{ $item->qty }}</td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-form" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-item" method="post" class="form-horizontal" data-toggle="validator" enctype="multipart/form-data">
                {{ csrf_field() }} {{ method_field('POST') }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Add Item to {{ $desk->title }}</h3>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="location_id" value="{{ $desk->location_id }}">
                    <input type="hidden" name="desk_id" value="{{ $desk->id }}">
                    <input type="hidden" name="trackable" value="Yes">

                    <div class="box-body">


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Compartment</label>
                                    <select class="form-control" name="deskpart_id" required>
                                        <option value="" selected disabled>-- Select Compartment --</option>
                                        @foreach($desk->deskparts as $deskpart)
                                        <option value="{{ $deskpart->id }}">{{ $deskpart->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Item Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Item Type</label>
                                    <select class="form-control" id="item_type" name="item_type" required>
                                        <option value="" selected disabled>-- Select Item Category --</option>

                                        @foreach($item_types as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="number" min="1" class="form-control" id="qty" name="qty" required>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>


                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Image</label>
                                    <input type="file" class="form-control" name="image">
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('bot')
<script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/validator/validator.min.js') }}"></script>




<script>
                    $(document).ready(function () {
                        // Initialize DataTable
                        $('.datatable').DataTable({
                            'paging': true,
                            'searching': true,
                            'ordering': true,
                            'info': true,
                            'autoWidth': false
                        });

                        // Handle Form Submission via AJAX
                        $('#modal-form form').validator().on('submit', function (e) {
                            if (!e.isDefaultPrevented()) {
                                var id = $('#id').val();
                                var url = $(this).attr('action');

                                $.ajax({
                                    url: url,
                                    type: "POST",
                                    // Use FormData to handle file uploads (images)
                                    data: new FormData($("#modal-form form")[0]),
                                    contentType: false,
                                    processData: false,
                                    success: function (data) {
                                        // 1. Hide the modal
                                        $('#modal-form').modal('hide');

                                        // 2. Reload the page to show new item
                                        location.reload();
                                    },
                                    error: function (data) {
                                        alert('Oops! Something went wrong. Please check your input.');
                                    }
                                });
                                return false;
                            }
                        });
                    });

                    function addItem() {
                        save_method = "add";
                        $('input[name=_method]').val('POST');
                        $('#modal-form').modal('show');
                        $('#modal-form form')[0].reset();
                        $('.modal-title').text('Add Item to {{ $desk->title }}');
                        $('#modal-form form').attr('action', "{{ route('items.store') }}");
                    }
</script>


@endsection