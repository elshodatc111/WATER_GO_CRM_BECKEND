<li class="nav-item">
  <a class="nav-link {{ request()->routeIs(['home']) ? '' : 'collapsed' }}" href="{{ route('home') }}">
    <i class="bi bi-house-heart"></i>
    <span>Bosh sahifa</span>
  </a>
</li>

<li class="nav-item">
  <a class="nav-link {{ request()->routeIs(['companyee','companye_show']) ? '' : 'collapsed' }}" href="{{ route('companyee') }}">
    <i class="bi bi-house-heart"></i>
    <span>FIrmalar</span>
  </a>
</li>

<li class="nav-item">
  <a class="nav-link collapsed" data-bs-target="#davomad-nav" data-bs-toggle="collapse" href="#">
    <i class="bi bi-calendar2-check"></i><span>Menu</span><i class="bi bi-chevron-down ms-auto"></i>
  </a>
  <ul id="davomad-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
    <li>
      <a href="#" class="nav-link ">
        <i class="bi bi-dot"></i><span>menu1</span>
      </a>
    </li>
    <li>
      <a href="#" class="nav-link ">
        <i class="bi bi-dot"></i><span>menu2</span>
      </a>
    </li>
  </ul>
</li>