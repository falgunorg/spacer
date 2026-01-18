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
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="number" min="1" class="form-control" name="qty" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Item Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Item Type</label>
                                <select class="form-control" id="item_type" name="item_type" required>
                                    <option value="" selected disabled>-- Select Item Category --</option>

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



                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" class="form-control" name="image">
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