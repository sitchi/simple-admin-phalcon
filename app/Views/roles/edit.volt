<div class="row">
    <div class="col-12">
        {{ flash.output() }}
        {{ flashSession.output() }}
        <div class="card">
            <div class="card-header">
                <h4>Edit Role: {{role.name}}</h4>
            </div>
            <div class="card-body">
                {{ form() }}
                {{ form.render("id") }}
                <div class="mb-3">
                    <label for="name">Name</label>
                    {{ form.render("name", ["class": "form-control"]) }}
                </div>
                <div class="mb-3">
                    <label for="active">Active?</label>
                    {{ form.render("active", ["class": "form-control"]) }}
                </div>
                <div class="mb-3">
                    {{ submit_button('Save', 'class': 'btn btn-success', 'value':'Save') }}
                    {{ link_to("/roles", 'Cancel', "class": "btn btn-warning") }}
                </div>
                {{ form.render('csrf', ['value': security.getToken()]) }}
                {{ end_form() }}
            </div>
        </div>
    </div>
    <!-- /.col-12 -->
</div>
