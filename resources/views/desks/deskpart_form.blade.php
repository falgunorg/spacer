<div class="modal fade" id="modal-deskpart-form" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm"> <div class="modal-content">
            <form id="form-deskpart" method="post" class="form-horizontal" data-toggle="validator">
                {{ csrf_field() }} {{ method_field('POST') }}

                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add New Compartment</h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="deskpart_id" name="id">
                    <input type="hidden" id="deskpart_desk_id" name="desk_id">

                    <div class="form-group" style="margin: 0 10px;">
                        <label>Compartment Title / Number</label>
                        <input type="text" class="form-control" id="deskpart_title" name="title" placeholder="e.g. DeskPart 01" required autofocus>
                        <span class="help-block with-errors"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Compartment</button>
                </div>
            </form>
        </div>
    </div>
</div>