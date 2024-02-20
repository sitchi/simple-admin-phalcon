<div class="row">
    <div class="col-12">
        {{ flash.output() }}
        {{ flashSession.output() }}
        <div class="card">
            <div class="card-header">
                <h4>User - {{ user.name }}</h4>
            </div>

            <div class="card-body">
                <table class="table table-hover responsive" id="dataTables" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>IP</th>
                        <th>Agent</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    {% for login in user.usersAuths %}
                        <tr>
                            <td>{{ login.id }}</td>
                            <td>{{ login.ipAddress }}</td>
                            <td>{{ login.userAgent }}</td>
                            <td>{{ login.createdAt }}</td>
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
            </div>
            <div class="card-footer small text-muted">Updated {{ date("Y-m-d H:i:s") }}</div>
        </div>
    </div>
    <!-- /.col-12 -->
</div>
