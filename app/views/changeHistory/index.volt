<div class="row">
    <div class="col-12">
        {{ content() }}
        <div class="card">
            <div class="card-header">
                Change History
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-hover responsive" id="dataTables" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Date</th>
                            <th>Model</th>
                            <th>Action</th>
                            <th>value ID</th>
                            <th>IP</th>
                            <th>user</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="card-footer small text-muted">updated {{date("Y-m-d H:i:s")}}</div>
        </div>
    </div>
    <!-- /.col-12 -->
</div>
<!-- detail modal -->
<div class="modal fade" id="detail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="modal-detail"></div>
        </div>
    </div>
</div>
<!-- / detail modal -->
