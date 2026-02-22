<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'delegasi_user' ? 'active' : '' }}"
        href="{{ route('user-management.home') }}"><i class="bx bx-user me-2"></i> Delegasi
        User</a>
</li>

<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'manajemen_roles' ? 'active' : '' }}"
        href="{{ route('role-permission.home') }}"><i class="fa-solid fa-shield-halved me-2"></i>
        Manajemen Roles</a>
</li>

<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'backup' ? 'active' : '' }}" href="{{ route('backup.home') }}"><i
            class="fa-solid fa-database me-2"></i> Backup</a>
</li>
