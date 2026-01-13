@extends('layouts.master')

@section('content')
<style>
    /* Screen Styling - Makes it look like a normal web page */
    .print-ticket {
        width: 100%;
        max-width: 800px;
        margin: 20px auto;
    }
    .printable-area {
        border: 1px solid #ddd;
        padding: 10px;
        background: #fff;
    }

    /* Thermal Printer Settings */
    @media print {
        @page {
            margin: 0;
            size: auto;
        }

        body * {
            visibility: hidden;
        }

        /* ONLY show the small ticket area when printing */
        .printable-area, .printable-area * {
            visibility: visible;
        }

        .printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 72mm !important;
            margin: 2mm !important;
            padding: 5px !important;
            border: 1px solid #000 !important;
        }

        .no-print {
            display: none !important;
        }

        /* Typography for Small Printer */
        .printable-area {
            font-size: 10px !important;
            color: #000;
        }

        .printable-area .table td, .printable-area .table th {
            padding: 1px !important;
            border: none !important;
        }

        .item-image {
            width: 50px !important;
            height: 50px !important;
        }
        svg {
            width: 70px !important;
            height: 70px !important;
        }
    }
</style>

<div class="container-fluid print-ticket">

    <div class="box box-primary no-print">
        <div class="box-header with-border">
            <h3 class="box-title">Full Item Details</h3>
            <a href="{{ url()->previous() }}" class="btn btn-default btn-sm pull-right">Back</a>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="{{ $item->show_photo }}" style="width: 200px; border-radius: 8px;">
                </div>
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr><th>Name</th><td>{{ $item->name }}</td></tr>
                        <tr><th>Category</th><td>{{ $item->category->name ?? 'N/A' }}</td></tr>
                        <tr><th>Quantity</th><td>{{ $item->qty }}</td></tr>
                        <tr><th>Location</th><td>
                                {{ $item->trackable == 'Yes' 
        ? ($item->cabinet->title ?? 'N/A') . ' [' . ($item->drawer->title ?? 'N/A') . ']' 
        : $item->location 
                                }}
                            </td></tr>
                        {{-- ADD ALL YOUR OTHER EXTRA INFO HERE --}}
                        <tr><th>Description</th><td>{{ $item->description ?? 'No description' }}</td></tr>
                        <tr><th>Created At</th><td>{{ $item->created_at }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="printable-area"> 
        <div class="box-body" style="padding: 5px;">
            <div class="text-center" style="margin-bottom: 8px; border-bottom: 1px dashed #ccc; padding-bottom: 5px;">
                <h4 style="margin:0; font-size: 14px; font-weight:bold;">{{ $item->name }}</h4>
                <small>{{ $item->category->name ?? 'General' }}</small>
            </div>

            <div class="row">
                <div class="col-xs-4 text-center">
                    <img src="{{ $item->show_photo }}" class="item-image img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                </div>

                <div class="col-xs-8">
                    <table class="table" style="font-size: 10px; margin-bottom: 0;">
                        <tr>
                            <th>Qty:</th>
                            <td>{{ $item->qty }}</td>
                        </tr>
                        <tr>
                            <th>Location:</th>
                            <td>
                                {{ $item->trackable == 'Yes' 
        ? ($item->cabinet->title ?? 'N/A') . ' [' . ($item->drawer->title ?? 'N/A') . ']' 
        : $item->location 
                                }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="text-center" style="margin-top: 10px;">
                <div style="display: inline-block;">
                    {!! QrCode::size(80)->generate(Request::url()); !!}
                </div>
                <p style="font-size: 12px; font-weight: bold; text-transform: uppercase;">{{$item->serial_number}}</p>
            </div>
        </div>
    </div>

    <div class="no-print text-center" style="margin-top: 20px;">
        <button onclick="window.print()" class="btn btn-success btn-lg btn-block">
            <i class="fa fa-print"></i> PRINT TOKEN
        </button>
    </div>
</div>
@endsection