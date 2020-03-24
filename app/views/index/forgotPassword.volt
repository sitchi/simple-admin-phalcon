<div class="card">
    <div class="card-body register-card-body">
        <p class="login-box-msg">Forgot Password?</p>
        {{ flash.output() }}
        {{ flashSession.output() }}
        {{ form() }}
        <div class="input-group mb-3">
            {{ form.render('email', ["class":"form-control", 'id': 'forgot-email-input']) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                {{ form.render('csrf', ['value': security.getToken()]) }}

                {{ form.render('Send') }}
            </div>
        </div>
        <!-- /.col -->
        </form>
        <hr>
        <p class="mb-1">
            <a href="/">Login</a>
        </p>
        <p class="mb-0">
            <a href="signup" class="text-center">Register a new membership</a>
        </p>
    </div>
</div>