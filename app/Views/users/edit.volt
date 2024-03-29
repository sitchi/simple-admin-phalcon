<div class="row">
  <div class="col-12">
    {{ flash.output() }}
    {{ flashSession.output() }}
    <div class='row'>
      <div class='col-lg-7 col-md-7 col-sm-7'>
        <div class="card">
          <div class="card-header">
            <h4>User: {{user.name}}</h4>
          </div>
          <div class="card-body">
            {{ form() }}
            <div class="mb-3">
              <label for="name">Full Name</label>
              {{ form.render("name", ["class": "form-control"]) }}
            </div>
            <div class="mb-3">
              <label for="email">E-Mail</label>
              {{ form.render("email", ["class": "form-control"]) }}
            </div>
            <div class="mb-3">
                <label>Change Password</label>
                {{ form.render("newPassword") }}
            </div>
            <div class="mb-3">
              <label for="roleID">Roles</label>
              {{ form.render("rolesID[]", ["class": "form-control select2", "data-width": "100%"]) }}
            </div>
            <div class="mb-3">
              <label for="active">Active?</label>
              {{ form.render("active", ["class": "form-control"]) }}
            </div>
            <div class="mb-3">
              {{ submit_button('Save', 'class': 'btn btn-success', 'value':'Save') }}
              {{ link_to("/users", 'Cancel', "class": "btn btn-warning") }}
            </div>
            {{ form.render('csrf', ['value': security.getToken()]) }}
            {{ end_form() }}
          </div>
        </div>
      </div>

      <div class='col-lg-5 col-md-5 col-sm-5'>
        <div class="card">
          <div class="card-header">
            <h4>User Statistics</h4>
          </div>
          <div class="card-body">
            <p><b>Last Visit</b>: Unknow</p>
            <p><b>Registration Date</b>: 2020-03-01 23:00:00</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.col-12 -->
</div>
