<body class="layout-fixed sidebar-expand-lg bg-body-tertiary sidebar-open app-loaded">
<!-- Site wrapper -->
<main class="app-wrapper">
    <!-- Navbar -->
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="fa fa-bars"></i></a>
                </li>

                <!-- SEARCH FORM -->
                <form class="form-inline ml-3" action="/users" method="post">
                    <div class="input-group">
                        <input class="form-control form-control-navbar" type="search" name="searchAccount" id="search"
                               placeholder="Search" aria-label="Search">
                        <button class="btn btn-navbar" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ gravatar.getAvatar(auth.getEmail()) }}" class="user-image rounded-circle shadow"
                             alt="img">
                        <span class="d-none d-md-inline">{{ auth.getName() }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end" data-bs-popper="static">
                        <!-- User image -->
                        <li class="user-header text-bg-primary">
                            <img src="{{ gravatar.getAvatar(auth.getEmail()) }}" class="rounded-circle shadow"
                                 alt="img">
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
                            <a href="/logout" class="btn btn-default btn-flat float-end">Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!-- Brand Logo -->
        <div class="sidebar-brand">
            <a href="/dashboard" class="brand-link">
                <img src="/img/simple-admin.png" alt="PS" class="brand-image">
                <span class="brand-text font-weight-light"> Simple Admin</span>
            </a>
        </div>

        <!-- Sidebar -->
        <div class="sidebar-wrapper">
            <!-- Sidebar Menu -->
            <nav class="mt-2 text-sm">
                <ul class="nav sidebar-menu flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">

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


    <main class="app-main">

        <!-- Content Header (Page header) -->
        <section class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-4">
                        <h3>{{ tag.title().get() }}</h3>
                    </div>
                    <div class="col-sm-8">
                        <ol class="breadcrumb float-sm-end">
                            {{ breadcrumbs is not empty ? breadcrumbs : null }}
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="app-content">
            <div class="container-fluid">
                {{ flash.output() }}
                {{ flashSession.output() }}
                {{ content() }}
            </div>
        </section>
        <!-- /.content -->
    </main>
    <!-- /.content-wrapper -->

    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">
            <b>v2.0.0</b>
        </div>
        <strong>&copy; 2020 - {{ date("Y") }} - Simple Admin. </strong>
    </footer>

</main>
<!-- ./wrapper -->
</body>
