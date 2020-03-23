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
                <div class="form-group">
                    <label>New Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        {{ form.render("password", ["class": "form-control"]) }}
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        {{ form.render("confirmPassword", ["class": "form-control"]) }}
                    </div>
                </div>
                <div class="btn-group">
                    {{ submit_button("Save", "class": "btn btn-success") }}
                    {{ link_to("/dashboard", 'Cencel', "class": "btn btn-warning") }}
                </div>
                {{ form.render('csrf', ['value': security.getToken()]) }}
                {{ end_form() }}

            </div>
        </div>
    </div>
</div>
