<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <div class="app-brand demo">

        <a href="{{ route('dashboard') }}" class="app-brand-link">

            <!-- <span class="app-brand-logo demo me-1">

                <span style="color: var(--bs-primary)">

                    <img src="{{ url('assets/img/logo.png') }}" width="30" />

                </span>

            </span> -->

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

        @if(auth()->user()->role == 1 || auth()->user()->role == 7 || auth()->user()->role == 8 || auth()->user()->role == 4)

        <li class="menu-item {{ in_array(Route::currentRouteName(), ['dashboard']) ? 'active' : '' }}">

            <a href="{{ route('dashboard') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-google-analytics"></i>

                <div data-i18n="Dashboard">Dashboard</div>

            </a>

        </li>
        @endif

        
        @if(auth()->user()->role == 1)
        <li class="menu-item {{ in_array(Route::currentRouteName(), ['user.list', 'user.create', 'user.store', 'user.edit', 'user.update', 'user.delete']) ? 'active' : '' }}">

            <a href="{{ route('user.list') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>

                <div data-i18n="Dashboard">Users</div>

            </a>

        </li>

        <!-- <li class="menu-item {{ in_array(Route::currentRouteName(), ['group.list', 'group.create', 'group.store', 'group.edit', 'group.update', 'group.delete']) ? 'active' : '' }}">

            <a href="{{ route('group.list') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>

                <div data-i18n="Dashboard">Groups</div>

            </a>

        </li> -->

        @endif

        @if(auth()->user()->role == 2 || auth()->user()->role == 1)

        <li class="menu-header fw-medium mt-4">

            <span class="menu-header-text" data-i18n="Sales">Sales</span>

        </li>

        <li class="menu-item {{ in_array(Route::currentRouteName(), ['sales_dashboard']) ? 'active' : '' }}">

            <a href="{{ route('sales_dashboard') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-trending-up"></i>

                <div data-i18n="Dashboard">Dashboard</div>

            </a>

        </li>

        <li class="menu-item {{ in_array(Route::currentRouteName(), ['lead.list', 'lead.new', 'lead.edit', 'lead.show', 'lead.activity.new', 'lead.activity.add', 'lead.activity.edit', 'lead.activity.update', 'lead.activity.delete']) ? 'active' : '' }}">

            <a href="{{ route('lead.list') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-crosshairs-gps"></i>

                <div data-i18n="Leads">Leads</div>

            </a>

        </li>

        <li class="menu-item {{ in_array(Route::currentRouteName(), ['deal.list', 'deal.new', 'deal.edit', 'deal.show']) ? 'active' : '' }}">

            <a href="{{ route('deal.list') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-currency-gbp"></i>

                <div data-i18n="Deals">Deals</div>

            </a>

         

        </li>

        @endif

        <!--New Module-->

         @if(auth()->user()->role == 1)

        <li class="menu-header fw-medium mt-4">

            <span class="menu-header-text" data-i18n="Sales">Hosting Management</span>

        </li>

        <li class="menu-item {{ in_array(Route::currentRouteName(), ['hosting_dashboard']) ? 'active' : '' }}">

            <a href="{{ route('hosting_dashboard') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-trending-up"></i>

                <div data-i18n="Dashboard">Dashboard</div>

            </a>

        </li>

         <li class="menu-item {{ in_array(Route::currentRouteName(), ['hosting.list', 'hosting.new', 'hosting.edit', 'hosting.show']) ? 'active' : '' }}">

            <a href="{{ route('hosting.list') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-server"></i>

                <div data-i18n="Deals">Hosting Details</div>

            </a>

        </li>

        @endif
        
        @if(auth()->user()->role == 1)

        <li class="menu-header fw-medium mt-4">

            <span class="menu-header-text" data-i18n="Sales">SEO Management</span>

        </li>

        <li class="menu-item {{ in_array(Route::currentRouteName(), ['seo.dashboard']) ? 'active' : '' }}">

            <a href="{{ route('seo.dashboard') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-trending-up"></i>

                <div data-i18n="Dashboard">Dashboard</div>

            </a>

        </li>

         <li class="menu-item {{ in_array(Route::currentRouteName(), ['seo.list', 'seo.create', 'seo.edit']) ? 'active' : '' }}">

            <a href="{{ route('seo.list') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-web"></i>

                <div data-i18n="Deals">SEO Details</div>

            </a>

        </li>

        @endif

        @if(auth()->user()->role == 1 || auth()->user()->role == 7 || auth()->user()->role == 8 || auth()->user()->role == 4)

        <li class="menu-header fw-medium mt-4">

            <span class="menu-header-text" data-i18n="Sales">{{ __('Project Management') }}</span>

        </li>
        @endif

        @if(auth()->user()->role == 1 || auth()->user()->role == 7)
        <li class="menu-item {{ in_array(Route::currentRouteName(), ['project.dashboard']) ? 'active' : '' }}">

            <a href="{{ route('project.dashboard') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-trending-up"></i>

                <div data-i18n="Dashboard">Dashboard</div>

            </a>

        </li>
        @endif

        @if(auth()->user()->role == 1 || auth()->user()->role == 7 || auth()->user()->role == 8 || auth()->user()->role == 4)
        <li class="menu-item {{ in_array(Route::currentRouteName(), ['project.', 'project.details', 'project.list', 'project.edit', 'project.add', 'project.store', 'project.task.list', 'project.task.add', 'project.task.detail', 'project.bug.list', 'project.bug.add', 'project.bug.detail', 'project.bug.edit', 'project.milestones.list', 'project.milestones.add', 'project.milestones.detail', 'project.milestone.edit']) ? 'active' : '' }}">

            <a href="{{ route('project.list') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-chart-tree"></i>

                <div data-i18n="Projects">{{ __('Projects') }}</div>

            </a>

        </li>
        @endif

        @if(auth()->user()->role == 1 || auth()->user()->role == 7)
        <li class="menu-item {{ in_array(Route::currentRouteName(), ['client.', 'client.list', 'client.edit', 'client.add', 'client.store']) ? 'active' : '' }}">

            <a href="{{ route('client.list') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-account-multiple-outline"></i>

                <div data-i18n="Projects">{{ __('Clients') }}</div>

            </a>

        </li>

        @endif

        <!--New Module-->
        <li class="menu-header fw-medium mt-4">
            <span class="menu-header-text" data-i18n="Sales">Resource Management</span>
        </li>
        <li class="menu-item {{ in_array(Route::currentRouteName(), ['employee.list', 'employee.create', 'employee.edit', 'employee.show']) ? 'active' : '' }}">
            <a href="{{ route('employee.list') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-account"></i>
                <div data-i18n="Employees">Teams</div>
            </a>
        </li>
        <!-- HR/Resource Management -->
        @if(in_array(auth()->user()->role, [1, 3, 7, 8, 4]) && in_array(auth()->user()->department_id, [1, 2, 4]))
            <li class="menu-header fw-medium mt-4">
                <span class="menu-header-text" data-i18n="HR">HR Management</span>
            </li>
        @endif
            
        @if(auth()->user()->role == 1 || auth()->user()->role == 3)
            <li class="menu-item {{ in_array(Route::currentRouteName(), ['hr.dashboard']) ? 'active' : '' }}">
                <a href="{{ route('hr.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-trending-up"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>
        @endif
            
        @if(in_array(auth()->user()->role, [1, 3, 7, 8]) && in_array(auth()->user()->department_id, [1, 2, 4]))
            <li class="menu-item {{ in_array(Route::currentRouteName(), ['hr.configuration', 'hr.holiday.management.create', 'hr.holiday.management.edit', 'hr.leave.type.create', 'hr.leave.type.edit']) ? 'active' : '' }}">
                <a href="{{ route('hr.configuration') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-cog-outline"></i>
                    <div data-i18n="Configuration">Configuration</div>
                </a>
            </li>
        @endif
            
        @if(in_array(auth()->user()->role, [1, 3]) && in_array(auth()->user()->department_id, [1, 4]))
            <li class="menu-item {{ in_array(Route::currentRouteName(), ['hr.recruitment', 'hr.job.posting.create', 'hr.job.posting.edit', 'hr.applicant.create', 'hr.interview.create', 'hr.interview.edit', 'hr.offer.letter.create', 'hr.offer.letter.edit']) ? 'active' : '' }}">
                <a href="{{ route('hr.recruitment') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-search-outline"></i>
                    <div data-i18n="Recruitment">Recruitment</div>
                </a>
            </li>
        @endif
            
        @if(in_array(auth()->user()->role, [1, 3, 4]) && in_array(auth()->user()->department_id, [1, 2, 4]))
            <li class="menu-item {{ in_array(Route::currentRouteName(), ['hr.employee.management', 'hr.performance.appraisal.create', 'hr.employee.feedback.create', 'hr.employee.history.create']) ? 'active' : '' }}">
                <a href="{{ route('hr.employee.management') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>
                    <div data-i18n="Employee">Employee</div>
                </a>
            </li>
        @endif
            
        @if(in_array(auth()->user()->role, [1, 3, 4]) && in_array(auth()->user()->department_id, [1, 2, 4]))
            <li class="menu-item {{ in_array(Route::currentRouteName(), ['hr.attendance']) ? 'active' : '' }}">
                <a href="{{ route('hr.attendance') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-account-multiple-check-outline"></i>
                    <div data-i18n="Attendance">Attendance</div>
                </a>
            </li>
        @endif
            
        @if(in_array(auth()->user()->role, [1, 3, 4]) && in_array(auth()->user()->department_id, [1, 2, 4]))
            <li class="menu-item {{ in_array(Route::currentRouteName(), ['hr.payroll.management', 'hr.bank.statement.create', 'hr.bank.statement.edit', 'hr.salary.management.create']) ? 'active' : '' }}">
                <a href="{{ route('hr.payroll.management') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-wallet-outline"></i>
                    <div data-i18n="Payroll">Payroll</div>
                </a>
            </li>
        @endif
            
        @if(in_array(auth()->user()->role, [1, 3, 4]) && in_array(auth()->user()->department_id, [1, 2, 4]))
            <li class="menu-item {{ in_array(Route::currentRouteName(), ['hr.exit.process', 'hr.resignation.create', 'hr.asset.handover.create']) ? 'active' : '' }}">
                <a href="{{ route('hr.exit.process') }}" class="menu-link">
                    <i class="menu-icon tf-icons mdi mdi-human-dolly"></i>
                    <div data-i18n="ExitProcess">Exit Process</div>
                </a>
            </li>
        @endif

        <!--Leave Management-->
       <li class="menu-header fw-medium mt-4">
            <span class="menu-header-text" data-i18n="Sales">{{ __('Leave Management') }}</span>
        </li>
        <li class="menu-item {{ in_array(Route::currentRouteName(), ['leave.list', 'leave.request', 'leave.edit']) ? 'active' : '' }}">

            <a href="{{ route('leave.list') }}" class="menu-link">

                <i class="menu-icon tf-icons mdi mdi-calendar"></i>

                <div data-i18n="Submit Leave">Submit Leave</div>

            </a>

        </li>

    </ul>

</aside>    