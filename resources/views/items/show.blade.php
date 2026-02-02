@extends('layouts.master')

@section('content')
<style>
    /* 1. YOUR ORIGINAL SCREEN STYLING (KEEPING YOUR DESIGN) */
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

<div class="container-fluid print-ticket">

    {{-- YOUR ORIGINAL DESIGN - UNTOUCHED --}}
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
                        <tr><th>Category</th><td>{{ $item->itemType->name ?? 'N/A' }}</td></tr>
                        <tr><th>Quantity</th><td>{{ $item->qty }}</td></tr>
                        <tr>
                            <th>Location</th>
                            <td>
                                <i class="fa fa-map-marker text-muted"></i>
                                @if($item->itemLocation)
                                {{ $item->itemLocation->name }}
                                @endif
                            </td>
                        </tr>
                        <tr><th>Description</th><td>{{ $item->description ?? 'No description' }}</td></tr>
                        <tr><th>Created At</th><td>{{ $item->created_at }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="box-body no-print" style="padding: 5px;">
        <div class="text-center" style="margin-bottom: 8px; border-bottom: 1px dashed #ccc; padding-bottom: 5px;">
            <h4 style="margin:0; font-size: 14px; font-weight:bold;">{{ $item->name }}</h4>
            <small>{{ $item->itemType->name ?? 'General' }}</small>
        </div>
    </div>

    {{-- UPDATED WRAPPER FOR BETTER CENTERING --}}
    <div class="text-center">
        <div class="printable-area">
            <div>
                {!! QrCode::size(90)->generate(Request::url()); !!}
            </div>
            <p style="margin:0 0 0 0;">{{$item->serial_number}}</p>
        </div>
    </div>

    <div class="no-print text-center" style="margin-top: 20px;">
        <button onclick="window.print()" class="btn btn-success btn-lg btn-block">
            <i class="fa fa-print"></i> PRINT TOKEN
        </button>
    </div>
</div>
@endsection

<script>
    // If the URL contains ?print=true, trigger print and then close/back
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('print')) {
            window.print();
            // Optional: close or redirect after printing
        }
    }
</script>