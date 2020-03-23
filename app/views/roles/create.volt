<div class="row">
    <div class="col-12">
        {{ flash.output() }}
        {{ flashSession.output() }}
        <div class="card">
            <div class="card-header">
                <h4>Create a Role</h4>
            </div>
            <div class="card-body">
                {{ form() }}
                <div class="form-group">
                    <label for="name">Name</label>
                    {{ form.render("name", ["class": "form-control"]) }}
                </div>
                <div class="form-group">
                    <label for="active">Active?</label>
                    {{ form.render("active", ["class": "form-control"]) }}
                </div>
                <div class="btn-group">
                    {{ submit_button("Save", "class": "btn btn-success", 'value':'Save') }}
                    {{ link_to("/roles", 'Cancel', "class": "btn btn-warning") }}
                </div>
                {{ form.render('csrf', ['value': security.getToken()]) }}
                {{ end_form() }}
            </div>
        </div>
    </div>
    <!-- /.col-12 -->
</div>
