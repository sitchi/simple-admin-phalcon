<div class="card">
    <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>
        {{ flash.output() }}
        {{ flashSession.output() }}
        {{ form() }}
        <div class="input-group mb-3">
            {{ form.render('email', ["class":"form-control"]) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            {{ form.render('password', ["class":"form-control"]) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    {{ form.render('remember') }}
                    {{ form.label('remember') }}
                </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
                {{ form.render('Login') }}
            </div>
            <!-- /.col -->
            {{ form.render('csrf', ['value': security.getToken()]) }}
        </div>
        </form>
        <hr>
        <p class="mb-1">
            <a href="forgotPassword">I forgot my password</a>
        </p>
        <p class="mb-0">
            <a href="signup" class="text-center">Register a new membership</a>
        </p>
    </div>
</div>
