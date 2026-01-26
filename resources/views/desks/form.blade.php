<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-desk" method="post" class="form-horizontal" data-toggle="validator" enctype="multipart/form-data">
                {{ csrf_field() }} {{ method_field('POST') }}

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="id" name="id">

                    <div class="form-group">
                        <label for="title" class="col-md-3 control-label">Desk Name</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="title" name="title" placeholder="e.g. Server Rack A" autofocus required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="location_id" class="col-md-3 control-label">Location</label>
                        <div class="col-md-9">
                            {!! Form::select('location_id', $locations, null, [
                                'class' => 'form-control', 
                                'placeholder' => '-- Choose Location --', 
                                'id' => 'location_id', 
                                'required'
                            ]) !!}
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="btnSave">Save Desk</button>
                </div>
            </form>
        </div>
    </div>
</div>