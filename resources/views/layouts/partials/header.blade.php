<nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center list-unstyled mb-0">
        <li class="nav-item dropdown pe-3">
            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-4"></i>
                <span class="d-none d-md-block dropdown-toggle ps-2 fw-bold text-dark">
                    {{ Auth()->user()->role }}
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile shadow mt-3 border-0">
                <li class="dropdown-header text-center p-3">
                    <h6 class="mb-0 text-dark">{{ Auth()->user()->name }}</h6>
                    <small class="text-primary">{{ Auth()->user()->phone }}</small>
                </li>
                <li>
                    <hr class="dropdown-divider m-0"></li>
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2" href="#">
                        <i class="bi bi-person-vcard me-2 text-primary"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="#" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        <span>Chiqish</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </li> 
    </ul>
</nav>