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

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Category</label>
                                    {!! Form::select('category_id', $category, null, ['class' => 'form-control select', 'placeholder' => '-- Choose Category --', 'id' => 'category_id', 'required']) !!}
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label>Price</label>
                                    <input type="text" class="form-control" id="price" name="price" required>
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
                                    <label>Instructions</label>
                                    <textarea class="form-control" id="instructions" name="instructions" rows="3"></textarea>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Condition</label>
                                    <select class="form-control" id="condition" name="condition">
                                        <option value="" selected disabled>-- Select Condition --</option>
                                        <option value="New">New</option>
                                        <option value="Old">Old</option>
                                        <option value="Fresh">Fresh</option>
                                        <option value="Fair">Fair</option>
                                        <option value="Like New">Like New</option>
                                    </select>
                                    <span class="help-block with-errors"></span>
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
