<body class="sidebar-mini layout-fixed sidebar-close control-sidebar-open">
<!-- Site wrapper -->
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
            </li>
        </ul>

        <!-- SEARCH FORM -->
        <form class="form-inline ml-3" action="/users" method="post">
            <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" name="searchAccount" id="search"
                       placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">

            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <img src="{{ gravatar.getAvatar(auth.getEmail()) }}" class="user-image img-circle elevation-2"
                         alt="img">
                    <span class="d-none d-md-inline">{{ auth.getName() }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <!-- User image -->
                    <li class="user-header bg-primary">
                        <img src="{{ gravatar.getAvatar(auth.getEmail()) }}" class="img-circle elevation-2" alt="img">
                        <p>
                            {{ auth.getName() }}
                            <small>Roles:
                                {% for role in auth.getRole() %}
                                    {{ role ~ ' | ' }}
                                {% endfor %}
                            </small>
                        </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <a href="/profile" class="btn btn-default btn-flat">Profile</a>
                        <a href="/logout" class="btn btn-default btn-flat float-right">Logout</a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="/dashboard" class="brand-link">
             <img src="/img/simple-admin.png" alt="PS" class="brand-image ">
            <span class="brand-text font-weight-light"> Simple Admin</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{ gravatar.getAvatar(auth.getEmail()) }}" class="img-circle elevation-2" alt="img">
                </div>
                <div class="info">
                    <a href="/profile" class="d-block">{{ auth.getName() }}</a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2 text-sm">
                <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview"
                    role="menu" data-accordion="false">

                    {% set urlm = dispatcher.getControllerName() ~ "/" ~ dispatcher.getActionName() %}

                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link {{ 'dashboard/index' == urlm ? 'active': null }}">
                            <i class="fas fa-fw fa-tachometer-alt nav-icon"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/changeHistory"
                           class="nav-link {{ 'changeHistory' == dispatcher.getControllerName() ? 'active': null }}">
                            <i class="fas fa-history nav-icon"></i>
                            <p>Change History</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/roles"
                           class="nav-link {{ 'roles' == dispatcher.getControllerName() ? 'active': null }}">
                            <i class="fas fa-layer-group nav-icon"></i>
                            <p>Roles</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/users"
                           class="nav-link {{ 'users' == dispatcher.getControllerName() ? 'active': null }}">
                            <i class="fas fa-user-secret nav-icon"></i>
                            <p>Users</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/logout">
                            <i class="fas fa-sign-out-alt nav-icon"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                </ul>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>


    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <h2>{{ get_title(false) }}</h2>
                    </div>
                    <div class="col-sm-8">
                        <ol class="breadcrumb float-sm-right">
                            {% if breadcrumbs is not empty %}
                                {{ breadcrumbs }}
                            {% endif %}
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            <div class="container-fluid">
                {{ flash.output() }}
                {{ flashSession.output() }}
                {{ content() }}
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>v1.0.0</b>
        </div>
        <strong>&copy; 2020 - Simple Admin. </strong>
    </footer>

</div>
<!-- ./wrapper -->
</body>
