<div class="modal fade" id="modal-form" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-item" method="post" class="form-horizontal" data-toggle="validator" enctype="multipart/form-data">
                {{ csrf_field() }} {{ method_field('POST') }}

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title"></h3>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="id" name="id">

                    <div class="box-body">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" id="name" name="name" autofocus required>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
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

                            <div class="col-lg-6">
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
                                    <label>Location</label>
                                    <select class="form-control" id="location_id" name="location_id">
                                        <option value="" selected disabled>-- Select Location --</option>
                                        @foreach($locations as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Trackable (Storage System?)</label>
                                    <select class="form-control" id="trackable" name="trackable">
                                        <option value="No">No (Manual Location)</option>
                                        <option value="Yes">Yes (Cabinet & Drawer)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div id="location_group" class="form-group">
                                    <label>General Location</label>
                                    <input type="text" class="form-control" id="location" name="location" placeholder="e.g. Front Desk">
                                </div>

                            </div>

                            <div class="col-lg-12">
                                <div id="storage_group" style="display:none;">
                                    <div class="row">

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Cabinet</label>
                                                <select class="form-control" id="cabinet_id" name="cabinet_id">
                                                    <option value="" selected disabled>-- Select Location First --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Drawer</label>
                                                <select class="form-control" id="drawer_id" name="drawer_id">
                                                    <option value="" selected disabled>-- Select Cabinet First --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Image</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>

            </form>
        </div>
    </div>
</div>
