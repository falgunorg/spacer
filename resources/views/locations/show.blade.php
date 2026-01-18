@extends('layouts.master')

@section('top')
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="box box-success">
            <div class="box-body box-profile">
                <div class="text-center">
                    <i class="fa fa-map-marker fa-5x text-success"></i>
                </div>
                <h3 class="profile-username text-center">{{ $location->name }}</h3>
                <p class="text-muted text-center">Primary Location</p>

                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Total Cabinets</b> <a class="pull-right">{{ $location->cabinets->count() }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Tracked Items</b> 
                        <a class="pull-right">
                            {{ $location->cabinets->flatMap->drawers->flatMap->items->count() }}
                        </a>
                    </li>
                    <li class="list-group-item">
                        <b>General Items</b> <a class="pull-right">{{ $location->items->count() }}</a>
                    </li>
                </ul>
                <a href="{{ route('locations.index') }}" class="btn btn-default btn-block"><b>Back to List</b></a>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#cabinets" data-toggle="tab">Cabinets</a></li>
                <li><a href="#all-items" data-toggle="tab">All Items in Location</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane" id="cabinets">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>Cabinet Title</th>
                                <th>Drawers</th>
                                <th>Item Count</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($location->cabinets as $cabinet)
                            <tr>
                                <td>{{ $cabinet->title }}</td>
                                <td>{{ $cabinet->drawers->count() }} Drawers</td>
                                <td>
                                    <span class="label label-warning">
                                        {{ $cabinet->drawers->flatMap->items->count() }} Items
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('cabinets.show', $cabinet->id) }}" class="btn btn-xs btn-primary">
                                        <i class="fa fa-eye"></i> View Cabinet
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane" id="all-items">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Serial</th>
                                <th>Item Name</th>
                                <th>Storage Type</th>
                                <th>Specific Spot</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($location->cabinets as $cabinet)
                                @foreach($cabinet->drawers as $drawer)
                                    @foreach($drawer->items as $item)
                                    <tr>
                                        <td><img src="{{ $item->show_photo }}" width="40" class="img-thumbnail"></td>
                                        <td>{{ $item->serial_number }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td><span class="label label-primary">Tracked</span></td>
                                        <td>{{ $cabinet->title }} > {{ $drawer->title }}</td>
                                        <td>{{ $item->qty }}</td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            @endforeach

                            @foreach($location->items as $item)
                            <tr>
                                <td><img src="{{ $item->show_photo }}" width="40" class="img-thumbnail"></td>
                                <td>{{ $item->serial_number }}</td>
                                <td>{{ $item->name }}</td>
                                <td><span class="label label-default">General</span></td>
                                <td>{{ $item->location }}</td>
                                <td>{{ $item->qty }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('bot')
<script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.datatable').DataTable();
    });
</script>
@endsection