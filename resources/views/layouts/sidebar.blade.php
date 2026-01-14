<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('user-profile.png') }} " class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ \Auth::user()->name  }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <hr/>

        <ul class="sidebar-menu" data-widget="tree">


            <li class="{{ Request::is('home*') ? 'active' : '' }}">
                <a href="{{ url('/home') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>


            <li class="{{ Request::routeIs('items.*') ? 'active' : '' }}">
                <a href="{{ route('items.index') }}">
                    <i class="fa fa-cubes"></i> <span>Item</span>
                </a>
            </li>

            <li class="{{ Request::routeIs('locations.*') ? 'active' : '' }}">
                <a href="{{ route('locations.index') }}">
                    <i class="fa fa-map"></i> <span>Locations</span>
                </a>
            </li>


            <li class="{{ Request::routeIs('cabinets.*') ? 'active' : '' }}">
                <a href="{{ route('cabinets.index') }}">
                    <i class="fa fa-cart-plus"></i> <span>5S</span>
                </a>
            </li>


            <hr/>
            <li class="{{ Request::routeIs('user.*') ? 'active' : '' }}">
                <a href="{{ route('user.index') }}">
                    <i class="fa fa-user-secret"></i> <span>System Users</span>
                </a>
            </li>
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
