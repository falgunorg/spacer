@extends('layouts.master')

@section('top')
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
    .printable-area {
        border: 1px solid #ddd;
        padding: 10px;
        background: #fff;
    }
    /* 2. THE CENTERING FIX FOR PRINTING */
    @media print {
        @page {
            size: 45mm 35mm;
            margin: 0 !important;
        }

        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 35mm !important;
            width: 45mm !important;
            overflow: hidden !important;
            background-color: white;
        }

        body * {
            visibility: hidden !important;
        }

        .printable-area, .printable-area * {
            visibility: visible !important;
        }

        .printable-area {
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            width: 45mm !important;
            height: 35mm !important;
            margin: 0 0 0 0  !important;
            padding: 0 0 0 0 !important;
            border: none !important;

            /* PERFECT CENTERING */
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important; /* Vertical center */
            align-items: center !important;     /* Horizontal center */
        }

        /* Target the div holding the QR code */
        .printable-area div {
            line-height: 0 !important; /* Removes bottom spacing from inline-block */
            margin: 0 0 0 0 !important;
            padding: 0 0 0 0 !important;
        }

        .printable-area svg {
            width: 22mm !important; /* Increased slightly for clarity */
            height: 22mm !important;
            display: block !important;
            margin: 11px auto 2px !important;
        }

        .printable-area p {
            margin: 0 0 0 0 !important;
            padding: 0 0 0 0 !important;
            font-size: 11px !important;
            font-weight: bold !important;
            line-height: 0.8 !important; /* Tightens the text block */
            text-align: center !important;
            width: 100% !important;
        }

        .no-print {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-body box-profile">
                {{-- UPDATED WRAPPER FOR BETTER CENTERING --}}
                <div class="text-center">
                    <div class="printable-area">
                        <div>
                            {!! QrCode::size(90)->generate(Request::url()); !!}
                        </div>
                    </div>
                </div>

                <div class="no-print text-center" style="margin-top: 20px;">
                    <button onclick="window.print()" class="btn btn-success btn-block">
                        <i class="fa fa-print"></i> PRINT TOKEN
                    </button>
                </div>
                <h3 class="profile-username text-center">{{ $cabinet->title }}</h3>
                <p class="text-muted text-center">Cabinet Detail</p>

                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Location</b> <a class="pull-right" href="{{route('locations.show',$cabinet->location->id)}}">{{ optional($cabinet->location)->name }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Total Drawers</b> <a class="pull-right">{{ $cabinet->drawers->count() }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Total Items</b> 
                        <a class="pull-right">
                            {{ $cabinet->drawers->sum(function($drawer) { return $drawer->items->count(); }) }}
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
                <li class="active"><a href="#drawers" data-toggle="tab">Drawers</a></li>
                <li><a href="#items" data-toggle="tab">Stored Items</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane" id="drawers">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>Drawer Name</th>
                                <th>Item Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cabinet->drawers as $drawer)
                            <tr>
                                <td>{{ $drawer->title }}</td>
                                <td><span class="label label-info">{{ $drawer->items->count() }} Items</span></td>
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
                                <th>Drawer</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cabinet->drawers as $drawer)
                            @foreach($drawer->items as $item)
                            <tr>
                                <td>
                                    <img src="{{ $item->show_photo }}" width="40" class="img-thumbnail">
                                </td>
                                <td>{{ $item->serial_number }}</td>
                                <td>{{ $item->name }}</td>
                                <td><span class="label label-warning">{{ $drawer->title }}</span></td>
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
                    <h3 class="modal-title">Add Item to {{ $cabinet->title }}</h3>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="location_id" value="{{ $cabinet->location_id }}">
                    <input type="hidden" name="cabinet_id" value="{{ $cabinet->id }}">
                    <input type="hidden" name="trackable" value="Yes">

                    <div class="box-body">


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Drawer</label>
                                    <select class="form-control" name="drawer_id" required>
                                        <option value="" selected disabled>-- Select Drawer --</option>
                                        @foreach($cabinet->drawers as $drawer)
                                        <option value="{{ $drawer->id }}">{{ $drawer->title }}</option>
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
                        $('.modal-title').text('Add Item to {{ $cabinet->title }}');
                        $('#modal-form form').attr('action', "{{ route('items.store') }}");
                    }
</script>


<script>
    // If the URL contains ?print=true, trigger print and then close/back
    window.onload = function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('print')) {
            window.print();
            // Optional: close or redirect after printing
        }
    }
</script>

@endsection