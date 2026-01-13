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
        <!-- Log on to codeastro.com for more projects! -->
        <!-- search form (Optional) -->
        <!-- <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
            </div>
        </form> -->
        <!-- /.search form -->
        <hr/>
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">


            <li class="{{ Request::is('home*') ? 'active' : '' }}">
                <a href="{{ url('/home') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            <li class="{{ Request::routeIs('categories.*') ? 'active' : '' }}">
                <a href="{{ route('categories.index') }}">
                    <i class="fa fa-list"></i> <span>Category</span>
                </a>
            </li>
            <li class="{{ Request::routeIs('items.*') ? 'active' : '' }}">
                <a href="{{ route('items.index') }}">
                    <i class="fa fa-cubes"></i> <span>Item</span>
                </a>
            </li>
            <li class="{{ Request::routeIs('damages.*') ? 'active' : '' }}">
                <a href="{{ route('damages.index') }}">
                    <i class="fa fa-cart-plus"></i> <span>Damage & Losses</span>
                </a>
            </li>
            <li class="{{ Request::routeIs('suppliers.*') ? 'active' : '' }}">
                <a href="{{ route('suppliers.index') }}">
                    <i class="fa fa-truck"></i> <span>Supplier</span>
                </a>
            </li>
            <li class="{{ Request::routeIs('customers.*') ? 'active' : '' }}">
                <a href="{{ route('customers.index') }}">
                    <i class="fa fa-users"></i> <span>Customer</span>
                </a>
            </li>
            <li class="{{ Request::routeIs('itemsOut.*') ? 'active' : '' }}">
                <a href="{{ route('itemsOut.index') }}">
                    <i class="fa fa-minus"></i> <span>Sale Items</span>
                </a>
            </li>

            <li class="{{ Request::routeIs('itemsIn.*') ? 'active' : '' }}">
                <a href="{{ route('itemsIn.index') }}">
                    <i class="fa fa-cart-plus"></i> <span>Purchase Items</span>
                </a>
            </li>

            <hr/>

            <li class="{{ Request::routeIs('cabinets.*') ? 'active' : '' }}">
                <a href="{{ route('cabinets.index') }}">
                    <i class="fa fa-cart-plus"></i> <span>5S</span>
                </a>
            </li>
            <hr/>
            <li class="{{ Request::routeIs('groceries.*') ? 'active' : '' }}">
                <a href="{{ route('groceries.index') }}">
                    <i class="fa fa-shopping-bag"></i> <span>Groceries</span>
                </a>
            </li>
            <li class="{{ Request::routeIs('recipes.*') ? 'active' : '' }}">
                <a href="{{ route('recipes.index') }}">
                 <i class="fa fa-list"></i> <span>Recipes</span>
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
