@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Inventory Token List</h3>
        </div>

        <div class="box-body">
            <form action="{{ route('tokens') }}" method="GET" class="form-inline">
                <input type="text" name="search" class="form-control" placeholder="Search Item Name..." value="{{ request('search') }}">

                <select name="category_id" class="form-control">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>

                <select name="condition" class="form-control">
                    <option value="">-- All Conditions --</option>
                    <option value="New" {{ request('condition') == 'New' ? 'selected' : '' }}>New</option>
                    <option value="Old" {{ request('condition') == 'Old' ? 'selected' : '' }}>Old</option>
                    <option value="Fresh" {{ request('condition') == 'Fresh' ? 'selected' : '' }}>Fresh</option>
                    <option value="Fair" {{ request('condition') == 'Fair' ? 'selected' : '' }}>Fair</option>
                    <option value="Like New" {{ request('condition') == 'Like New' ? 'selected' : '' }}>Like New</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('tokens') }}" class="btn btn-default">Reset</a>
            </form>
            <br/>

            <table id="token-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item</th> 
                        <th>Image</th>
                        <th>Category</th>
                        <th>Condition</th>
                        <th>Description</th>
                        <th class="text-center">QR Code</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>#{{ $item->serial_number }}</td>
                        <td class="item-name-cell"><strong>{{ $item->name }}</strong></td>
                        <td>
                            <img src="{{ $item->show_photo }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td><span class="label label-info">{{ $item->category->name ?? 'N/A' }}</span></td>
                        <td>{{ $item->condition ?? 'Good' }}</td>
                        <td style="max-width: 250px;">{{ $item->description }}</td>
                        <td class="text-center">
                            {{-- Visible small QR --}}
                            <div class="qr-container" id="visible-qr-{{ $item->id }}" style="margin-bottom: 5px;">
                                {!! QrCode::size(60)->generate(route('items.show', $item->id)) !!}
                                <div style="font-size: 12px; font-weight: bold; text-transform: uppercase;">{{$item->serial_number}}</div>
                            </div>

                            <button onclick="downloadQR('{{ $item->id }}', '{{ $item->name }}', '{{ $item->serial_number }}')" class="btn btn-xs btn-default">
                                <i class="fa fa-download"></i> PNG
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="no-print">
            {{ $items->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
    // 1. Initialize DataTable
    var table = $('#token-table').DataTable({
    "order": [[ 0, "desc" ]],
            "pageLength": 10,
            "dom": 'lrtip', // Hides default search box so we can use our custom one
    });
    // 2. Custom Name Search
    $('#search-name').on('keyup', function() {
    table.column(1).search(this.value).draw();
    });
    // 3. Custom Category Filter
    $('#filter-category').on('change', function() {
    table.column(3).search(this.value).draw();
    });
    // 4. Custom Condition Filter
    $('#filter-condition').on('change', function() {
    table.column(4).search(this.value).draw();
    });
    });
    /**
     * Robust QR Download Function
     * Fixes the "not working" issue by selecting the SVG directly 
     * even if the row is hidden by pagination.
     */
   function downloadQR(id, name, serial) {
    const qrDiv = document.getElementById(`visible-qr-${id}`);
    const svgElement = qrDiv.querySelector('svg');
    
    if (!svgElement) {
        alert("Error: QR Code not found!");
        return;
    }

    const size = 600; // High resolution
    const clonedSvg = svgElement.cloneNode(true);
    clonedSvg.setAttribute("width", size);
    clonedSvg.setAttribute("height", size);
    
    const svgData = new XMLSerializer().serializeToString(clonedSvg);
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");
    const img = new Image();
    const svgBlob = new Blob([svgData], {type: "image/svg+xml;charset=utf-8"});
    const url = URL.createObjectURL(svgBlob);

    img.onload = function() {
        canvas.width = size;
        canvas.height = size;

        // 1. Background
        ctx.fillStyle = "white";
        ctx.fillRect(0, 0, size, size);

        // 2. Draw the QR Code
        ctx.drawImage(img, 0, 0, size, size);

        // 3. Create the "Logo" space in the middle
        const boxWidth = 160; 
        const boxHeight = 60;
        const centerX = (size - boxWidth) / 2;
        const centerY = (size - boxHeight) / 2;

        // Draw a white rectangle to clear the QR bits behind the text
        ctx.fillStyle = "white";
        ctx.roundRect ? ctx.roundRect(centerX, centerY, boxWidth, boxHeight, 10) : ctx.fillRect(centerX, centerY, boxWidth, boxHeight);
        ctx.fill();

        // Optional: Add a small border around the middle box
        ctx.strokeStyle = "#eeeeee";
        ctx.lineWidth = 2;
        ctx.stroke();

        // 4. Draw the Serial Number inside the box
        ctx.fillStyle = "black";
        ctx.font = "bold 32px Arial";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.fillText(serial, size / 2, size / 2);

        // 5. Download
        const pngUrl = canvas.toDataURL("image/png");
        const downloadLink = document.createElement("a");
        downloadLink.href = pngUrl;
        downloadLink.download = `${serial}.png`;
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
        URL.revokeObjectURL(url);
    };
    img.src = url;
}
</script>
@endsection