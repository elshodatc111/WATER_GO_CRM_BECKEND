<li class="nav-item">
  <a class="nav-link {{ request()->routeIs(['home']) ? '' : 'collapsed' }}" href="{{ route('home') }}">
    <i class="bi bi-house-heart"></i>
    <span>Bosh sahifa</span>
  </a>
</li>

<li class="nav-item">
  <a class="nav-link {{ request()->routeIs(['companyee','companye_show','product_show']) ? '' : 'collapsed' }}" href="{{ route('companyee') }}">
    <i class="bi bi-house-heart"></i>
    <span>Firmalar</span>
  </a>
</li>

<li class="nav-item">
  <a class="nav-link {{ request()->routeIs(['orders_new','orders_active','orders_end','orders_show']) ? '' : 'collapsed' }}" data-bs-target="#buyurtmalar-nav" data-bs-toggle="collapse" href="#">
    <i class="bi bi-calendar2-check"></i><span>Buyurtmalar</span><i class="bi bi-chevron-down ms-auto"></i>
  </a>
  <ul id="buyurtmalar-nav" class="nav-content collapse {{ request()->routeIs(['orders_new','orders_active','orders_end','orders_show']) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
    <li>
      <a href="{{ route('orders_new') }}" class="nav-link {{ request()->routeIs(['orders_new']) ? '' : 'collapsed' }}">
        <i class="bi bi-dot"></i><span>Qabul qilinmagan</span>
      </a>
    </li>
    <li>
      <a href="{{ route('orders_active') }}" class="nav-link {{ request()->routeIs(['orders_active','orders_show']) ? '' : 'collapsed' }}">
        <i class="bi bi-dot"></i><span>Aktiv buyurtmalar</span>
      </a>
    </li>
    <li>
      <a href="{{ route('orders_end') }}" class="nav-link {{ request()->routeIs(['orders_end']) ? '' : 'collapsed' }}">
        <i class="bi bi-dot"></i><span>Yakunlangan buyurtmalar</span>
      </a>
    </li>
  </ul>
</li>


<li class="nav-item">
  <a class="nav-link collapsed" href="#">
    <i class="bi bi-house-heart"></i>
    <span>Firmalar</span>
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