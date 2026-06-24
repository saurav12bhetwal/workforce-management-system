<!-- resources/views/partials/sidebar.blade.php -->
<nav class="sidebar">
    <div class="sidebar-header">
        <h3 class="text-white">
            <i class="fas fa-users-cog"></i> WMS
        </h3>
        <span class="text-white-50 small">v1.0</span>
    </div>

    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Admin Menu -->
        @if (auth()->user()->hasRole('Admin'))
            <li class="menu-header">MANAGEMENT</li>



            <li class="menu-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <a href="{{ route('admin.departments.index') }}">
                    <i class="fas fa-building"></i>
                    <span>Departments</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.designations.*') ? 'active' : '' }}">
                <a href="{{ route('admin.designations.index') }}">
                    <i class="fas fa-briefcase"></i>
                    <span>Designations</span>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                <a href="{{ route('admin.employees.index') }}">
                    <i class="fas fa-users"></i>
                    <span>Employees</span>
                </a>
            </li>

            <li class="menu-header">ATTENDANCE & LEAVES</li>

            <li class="menu-item {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                <a href="{{ route('admin.attendance.index') }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Attendance</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}">
                <a href="{{ route('admin.leaves.index') }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Leaves</span>
                    @php
                        $pendingCount = App\Models\LeaveRequest::where('status', 'Pending')->count();
                    @endphp
                    @if ($pendingCount > 0)
                        <span class="badge bg-danger float-end">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            
        @endif

        <!-- Manager Menu -->
        <!-- In resources/views/partials/sidebar.blade.php -->

        @if (auth()->user()->hasRole('Manager'))
            <li class="menu-header">TEAM MANAGEMENT</li>

            <li class="menu-item {{ request()->routeIs('manager.team.*') ? 'active' : '' }}">
                <a href="{{ route('manager.team.index') }}">
                    <i class="fas fa-users"></i>
                    <span>My Team</span>
                    <span class="badge bg-info float-end">{{ auth()->user()->subordinates()->count() }}</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('manager.attendance.*') ? 'active' : '' }}">
                <a href="{{ route('manager.attendance.index') }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Team Attendance</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('manager.leaves.*') ? 'active' : '' }}">
                <a href="{{ route('manager.leaves.index') }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Team Leaves</span>
                    @php
                        $pendingCount = auth()
                            ->user()
                            ->subordinates()
                            ->withCount([
                                'leaveRequests as pending_count' => function ($q) {
                                    $q->where('status', 'Pending');
                                },
                            ])
                            ->get()
                            ->sum('pending_count');
                    @endphp
                    @if ($pendingCount > 0)
                        <span class="badge bg-danger float-end">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
        @endif

        <!-- Employee Menu -->
        @if (auth()->user()->hasRole('Employee') || auth()->user()->hasRole('Manager'))
            <li class="menu-header">MY ACTIVITIES</li>

            <li class="menu-item {{ request()->routeIs('employee.attendance.*') ? 'active' : '' }}">
                <a href="{{ route('employee.attendance.index') }}">
                    <i class="fas fa-fingerprint"></i>
                    <span>Attendance</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('employee.leaves.*') ? 'active' : '' }}">
                <a href="{{ route('employee.leaves.index') }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span>My Leaves</span>
                    @php
                        $pendingCount = auth()->user()->leaveRequests()->pending()->count();
                    @endphp
                    @if ($pendingCount > 0)
                        <span class="badge bg-danger float-end">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>

           
        @endif

        <li class="menu-header">ACCOUNT</li>

         <li class="menu-item {{ request()->routeIs('user.profile.*') ? 'active' : '' }}">
                <a href="{{ route('user.profile.index') }}">
                    <i class="fas fa-user-cog"></i>
                    <span>My Profile</span>
                </a>
            </li>

        <li class="menu-item">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt text-danger"></i>
                <span class="text-danger">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</nav>
