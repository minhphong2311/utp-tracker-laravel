{{-- <li class="nav-item menu-open">
  <a href="#" class="nav-link active">
    <i class="nav-icon fas fa-tachometer-alt"></i>
    <p>
      Starter Pages
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="#" class="nav-link active">
        <i class="fa fa-circle nav-icon"></i>
        <p>Active Page</p>
      </a>
    </li>
  </ul>
</li> --}}
<li class="nav-item">
  <a href="{{ url('/admincp') }}"
    class="nav-link {{ Request::is('admincp') || Request::is('admincp/clocks*') || Request::is('admincp/breaks*') ? 'active' : '' }}">
    <i class="nav-icon fas fa-tachometer-alt"></i>
    <p>Timesheets</p>
  </a>
</li>
{{-- <li class="nav-item">
  <a href="{{ url('/admincp/clocks') }}" class="nav-link {{ Request::is('admincp/clocks') ? 'active' : '' }}">
    <i class="nav-icon fas fa-user-clock"></i>
    <p>Clocks</p>
  </a>
</li> --}}
<li class="nav-item">
  <a href="{{ url('/admincp/change-clocks') }}"
    class="nav-link {{ Request::is('admincp/change-clocks*') ? 'active' : '' }}">
    <i class="nav-icon fas fa-clock"></i>
    <p>Change Time Requests</p>
  </a>
</li>
<li class="nav-item">
  <a href="{{ url('/admincp/employee') }}" class="nav-link {{ Request::is('admincp/employee*') ? 'active' : '' }}">
    <i class="nav-icon fas fa-users"></i>
    <p>Employees</p>
  </a>
</li>
<li class="nav-item">
  <a href="{{ url('/admincp/users') }}" class="nav-link {{ Request::is('admincp/users*') ? 'active' : '' }}">
    <i class="nav-icon fas fa-user-shield"></i>
    <p>Users</p>
  </a>
</li>

<li class="nav-item">
  <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
    <i class="nav-icon fas fa-sign-out-alt"></i>
    <p>{{ __('Logout') }}</p>
  </a>

  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
  </form>
</li>
