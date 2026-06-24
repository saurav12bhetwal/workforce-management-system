<!-- resources/views/partials/header.blade.php -->
<header class="header">
    <div class="d-flex justify-content-between align-items-center">
        <!-- Left Side -->
        <div class="d-flex align-items-center">
            <button class="btn btn-link text-white toggle-sidebar" id="sidebarToggle">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <h5 class="text-white ms-3 mb-0">
                @yield('page-title', 'Dashboard')
            </h5>
        </div>
        
        <!-- Right Side -->
        <div class="d-flex align-items-center">
            <!-- Notifications -->
            <div class="dropdown me-3">
                <button class="btn btn-link text-white position-relative" data-bs-toggle="dropdown">
                    <i class="fas fa-bell fa-lg"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        5
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">New leave request</a></li>
                    <li><a class="dropdown-item" href="#">Attendance reminder</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center" href="#">View all</a></li>
                </ul>
            </div>
            
            <!-- User Profile -->
            <div class="dropdown">
                <button class="btn btn-link text-white dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D6EFD&color=fff&size=32" 
                         class="rounded-circle me-2" alt="User">
                    <span>{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    {{-- <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>
                    </li> --}}
                    {{-- <li>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-cog me-2"></i> Settings
                        </a>
                    </li> --}}
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>