<div class="row">
    <div class="col-12">
        {{ flash.output() }}
        {{ flashSession.output() }}
        <div class="card">
            <div class="card-header">
                {{ link_to("roles/create", "Create a Role", "class": "btn btn-primary btn-sm") }}
            </div>

            <div class="card-body">
                <table class="table table-hover responsive" id="dataTables" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    {% for role in roles %}
                        <tr>
                            <td>{{ role.id }}</td>
                            <td>{{ role.name }}</td>
                            {{ role.active == 1 ? '<td class="text-success">Active</td>' : '<td class="text-danger">Passive</td>' }}
                            <td>
                                <div class="btn-group" role="group" aria-label="">
                                    {{ link_to("/roles/edit/" ~ role.id, '<i class="fas fa-edit"></i>', "class": "btn btn-primary btn-sm") }}
                                    {{ link_to("/roles/editPermission/" ~ role.id, '<i class="fas fa-lock"></i>', "class": "btn btn-warning btn-sm") }}
                                    <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete"
                                       onclick="deleteRole({{ role.id }})"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            </td>
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

{% if acl.isAllowed(auth.getRole(), 'roles', 'delete') != null %}
    <div class="modal fade" id="delete" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="modal-delete"></div>
            </div>
        </div>
    </div>
{% endif %}

