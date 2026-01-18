@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="panel-title">
                        <strong>Category:</strong> {{ $itemType->name }}
                    </h3>
                    <a href="{{ route('item-types.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Unique Items:</label> 
                            <span class="label label-primary">{{ $itemType->items->count() }}</span>
                        </div>
                        <div class="col-md-3">
                            <label>Total Stock Qty:</label> 
                            <span class="label label-success">{{ $itemType->items->sum('qty') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Items under this Type</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="bg-light">
                                    <th width="80">S/N</th>
                                    <th width="60">Image</th>
                                    <th>Item Name</th>
                                    <th>Qty</th>
                                    <th>Physical Location</th>
                                    <th>Trackable</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($itemType->items as $item)
                                <tr>
                                    <td class="text-center"><code>{{ $item->serial_number }}</code></td>
                                    <td>
                                        <img src="{{ $item->show_photo }}" style="width: 45px; height: 45px; border-radius: 4px;" alt="item">
                                    </td>
                                    <td>
                                        <strong>{{ $item->name }}</strong><br>
                                        <small class="text-muted">{{ $item->description }}</small>
                                    </td>
                                    <td>{{ $item->qty }}</td>
                                    <td>
                                        <i class="fa fa-map-marker text-muted"></i>

                                        {{-- 1. Base Location --}}
                                        @if($item->itemLocation)
                                        <a href="{{ route('locations.show', $item->itemLocation->id) }}">
                                            {{ $item->itemLocation->name }}
                                        </a>
                                        @else
                                        <span class="text-muted">No Location</span>
                                        @endif

                                        {{-- 2. Display Sub-Location based on Trackable status --}}
                                        @if($item->trackable == 'Yes')
                                        {{-- Trackable Path: Location > Cabinet > Drawer --}}
                                        @if($item->cabinet)
                                        <i class="fa fa-angle-right" style="margin: 0 5px;"></i>
                                        <a href="{{ route('cabinets.show', $item->cabinet->id) }}">
                                            {{ $item->cabinet->title }}
                                        </a>
                                        @endif

                                        @if($item->drawer)
                                        <i class="fa fa-angle-right" style="margin: 0 5px;"></i>
                                        <span class="label label-default">{{ $item->drawer->title }}</span>
                                        @endif
                                        @else
                                        {{-- Manual Path: Location > General Text --}}
                                        @if($item->location)
                                        <i class="fa fa-angle-right" style="margin: 0 5px;"></i>
                                        <span class="text-muted"><em>{{ $item->location }}</em></span>
                                        @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->trackable == 'Yes')
                                        <span class="text-success"> Yes</span>
                                        @else
                                        <span class="text-muted"> No</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No items found for this type.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection