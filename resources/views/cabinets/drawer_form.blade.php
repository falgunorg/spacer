<div class="modal fade" id="modal-drawer-form" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm"> <div class="modal-content">
            <form id="form-drawer" method="post" class="form-horizontal" data-toggle="validator">
                {{ csrf_field() }} {{ method_field('POST') }}

                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add New Drawer</h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="drawer_id" name="id">
                    <input type="hidden" id="drawer_cabinet_id" name="cabinet_id">

                    <div class="form-group" style="margin: 0 10px;">
                        <label>Drawer Title / Number</label>
                        <input type="text" class="form-control" id="drawer_title" name="title" placeholder="e.g. Drawer 01" required autofocus>
                        <span class="help-block with-errors"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Drawer</button>
                </div>
            </form>
        </div>
    </div>
</div>