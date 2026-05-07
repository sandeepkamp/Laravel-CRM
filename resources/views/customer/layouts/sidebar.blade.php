<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <div class="app-brand demo">
        <a href="{{ route('clients.dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-semibold ms-2">
                <img src="{{ url('assets/img/new-logo-final.webp') }}" width="200">
            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="mdi menu-toggle-icon d-xl-block align-middle mdi-20px"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ in_array(Route::currentRouteName(), ['clients.dashboard']) ? 'active' : '' }}">
            <a href="{{ route('clients.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-google-analytics"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <li class="menu-item {{ in_array(Route::currentRouteName(), ['clients.projects']) ? 'active' : '' }}">
            <a href="{{ route('clients.projects') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-chart-tree"></i>
                <div data-i18n="Dashboard">Projects</div>
            </a>
        </li>
    </ul>

</aside>    