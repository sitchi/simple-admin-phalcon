<div class="row">
    <div class="col-12">
        {{ flash.output() }}
        {{ flashSession.output() }}
        <div class="card">
            <div class="card-header">
                <h4>Create a User</h4>
            </div>
            <div class="card-body">
                {{ form() }}
                <div class="form-group">
                    <label for="name">Full Name</label>
                    {{ form.render("name", ["class": "form-control"]) }}
                </div>
                <div class="form-group">
                    <label for="email">E-Mail</label>
                    {{ form.render("email", ["class": "form-control"]) }}
                </div>
                <div class="form-group">
                    <label>Password</label>
                    {{ form.render("password") }}
                </div>
                <div class="form-group">
                    <label for="roleID">Roles</label>
                    {{ form.render("rolesID[]", ["class": "form-control select2", "multiple": "multiple", "multiple": "multiple"]) }}
                </div>
                <div class="btn-group">
                    {{ submit_button("Save", "class": "btn btn-success", 'value':'Save') }}
                    {{ link_to("/users", 'Cancel', "class": "btn btn-warning") }}
                </div>
                {{ form.render('csrf', ['value': security.getToken()]) }}
                {{ end_form() }}
            </div>
        </div>
    </div>
    <!-- /.col-12 -->
</div>
