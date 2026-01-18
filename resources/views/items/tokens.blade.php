@extends('layouts.master')

@section('top')
<link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Inventory Token List</h3>
        </div>

        <div class="box-body">
            <form action="{{ route('tokens') }}" method="GET" class="form-inline">
                <input type="text" name="search" class="form-control" placeholder="Search Name or Serial..." value="{{ request('search') }}">

                <select name="item_type" class="form-control">
                    <option value="">-- All Item Types --</option>
                    {{-- Garments & Textile Production --}}
                    <optgroup label="Garments & RMG Production">
                        <option value="RMG Finished Goods">RMG Finished Goods</option>
                        <option value="Production Samples">Production Samples (Proto/Fit/Size Set)</option>
                        <option value="Fabrics">Fabrics (Woven/Knit)</option>
                        <option value="Pocketing Fabric">Pocketing & Lining Fabric</option>
                        <option value="Interlining">Interlining & Padding</option>
                        <option value="Rib/Collar">Rib, Collar & Cuffs</option>
                    </optgroup>

                    {{-- Trims & Accessories --}}
                    <optgroup label="Trims & Accessories">
                        <option value="Sewing Thread">Sewing Thread (Cones)</option>
                        <option value="Buttons">Buttons (Plastic/Metal/Snap)</option>
                        <option value="Zippers">Zippers & Sliders</option>
                        <option value="Labels">Labels (Main/Care/Size)</option>
                        <option value="Hangtags">Hangtags & Price Tickets</option>
                        <option value="Elastic">Elastic & Drawstrings</option>
                        <option value="Poly Bags">Poly Bags & Packaging Material</option>
                        <option value="Cartons">Empty Cartons / Gum Tapes</option>
                    </optgroup>

                    {{-- Office & Administrative --}}
                    <optgroup label="Office Supplies">
                        <option value="Documents">Legal & Commercial Documents</option>
                        <option value="Files/Folders">Files & Ring Binders</option>
                        <option value="General Stationery">General Stationery (Pens/Paper/Staplers)</option>
                        <option value="Printed Forms">Printed Forms & Logbooks</option>
                        <option value="Toner/Ink">Printer Toners & Ink Cartridges</option>
                        <option value="Cleaning/Janitorial">Cleaning & Janitorial Supplies</option>
                    </optgroup>

                    {{-- IT & Electronics --}}
                    <optgroup label="IT & Electronics">
                        <option value="Computer Parts">Computer Parts (RAM/HDD/SSD)</option>
                        <option value="Peripherals">Peripherals (Mouse/Keyboard/Cables)</option>
                        <option value="Networking">Networking (Routers/Switches/LAN)</option>
                        <option value="CCTV">CCTV & Security Equipment</option>
                    </optgroup>
                </select>

                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('tokens') }}" class="btn btn-default">Reset</a>
            </form>
            <br/>

            <table id="token-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Serial</th>
                        <th>Item Name</th> 
                        <th>Image</th>
                        <th>Type</th>
                        <th>Location Path</th>
                        <th class="text-center">QR Code</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td><span class="badge bg-gray">#{{ $item->serial_number }}</span></td>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td>
                            <img src="{{ $item->show_photo }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                        </td>
                        <td><span class="label label-info">{{ $item->item_type }}</span></td>
                        <td>
                            @if($item->trackable == 'Yes' && $item->cabinet)
                            <a href="{{ route('locations.show', $item->cabinet->location->id) }}">{{ $item->cabinet->location->name }}</a>
                            <i class="fa fa-angle-right" style="margin: 0 3px;"></i>
                            <a href="{{ route('cabinets.show', $item->cabinet->id) }}">{{ $item->cabinet->title }}</a>
                            <span class="text-muted">[{{ $item->drawer->title ?? 'N/A' }}]</span>
                            @else
                            <i class="fa fa-map-marker text-muted"></i> {{ $item->location ?? 'No Location' }}
                            @endif
                        </td>
                        <td class="text-center">
                            <div id="visible-qr-{{ $item->id }}">
                                {!! QrCode::size(55)->generate(route('items.show', $item->id)) !!}
                            </div>
                            <button onclick="downloadQR('{{ $item->id }}', '{{ $item->name }}', '{{ $item->serial_number }}')" class="btn btn-xs btn-default" style="margin-top: 5px;">
                                <i class="fa fa-download"></i> PNG
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="box-footer">
            {{ $items->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@section('bot')
<script>
    function downloadQR(id, name, serial) {
    const qrDiv = document.getElementById(`visible-qr-${id}`);
    const svgElement = qrDiv.querySelector('svg');
    if (!svgElement) return;
    const size = 600;
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");
    const img = new Image();
    const svgData = new XMLSerializer().serializeToString(svgElement);
    const svgBlob = new Blob([svgData], {type: "image/svg+xml;charset=utf-8"});
    const url = URL.createObjectURL(svgBlob);
    img.onload = function() {
    canvas.width = size;
    canvas.height = size + 120; // Space for text
    ctx.fillStyle = "white";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    // Draw QR
    ctx.drawImage(img, 0, 0, size, size);
    // Draw Text
    ctx.fillStyle = "black";
    ctx.textAlign = "center";
    ctx.font = "bold 45px Arial";
    ctx.fillText(serial, size / 2, size + 40);
    ctx.font = "30px Arial";
    ctx.fillText(name.substring(0, 35), size / 2, size + 90);
    const pngUrl = canvas.toDataURL("image/png");
    const downloadLink = document.createElement("a");
    downloadLink.href = pngUrl;
    downloadLink.download = `Token_${serial}.png`;
    downloadLink.click();
    URL.revokeObjectURL(url);
    };
    img.src = url;
    }
</script>
@endsection