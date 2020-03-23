<div class="row">
    <div class="col-md-3">

        <!-- Profile Image -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="{{ gravatar.getAvatar(auth.getEmail()) }}" alt="img">
                </div>

                <h3 class="profile-username text-center">{{ auth.getName() }}</h3>

                <p class="text-muted text-center">
                    <small>Roles:
                        {% for role in auth.getRole() %}
                            {{ role ~ ' | ' }}
                        {% endfor %}
                    </small>
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <a href="/changePassword"><b>Change Password</b></a>
                    </li>
                </ul>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

    </div>
</div>