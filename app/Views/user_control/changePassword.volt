<div class="row">
    <div class="col-12">
        {{ flash.output() }}
        {{ flashSession.output() }}
        <div class="card">
            <div class="card-header">
                <h4>Change Password</h4>
            </div>
            <div class="card-body">
                {{ form() }}
                <div class="mb-3">
                    <label>New Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        {{ form.render("password", ["class": "form-control"]) }}
                    </div>
                </div>
                <div class="mb-3">
                    <label>Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        {{ form.render("confirmPassword", ["class": "form-control"]) }}
                    </div>
                </div>
                <div class="btn-group" role="group">
                    {{ submit_button("Save", "class": "btn btn-success") }}
                    {{ link_to("/dashboard", 'Cencel', "class": "btn btn-warning") }}
                </div>
                {{ form.render('csrf', ['value': security.getToken()]) }}
                {{ end_form() }}
            </div>
        </div>
    </div>
</div>
