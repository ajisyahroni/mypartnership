<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            {{-- <li class="nav-item d-block d-xl-none"> --}}
            <li class="nav-item d-block">
                <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
        </ul>
        <div class="px-0 navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center justify-content-between w-100 d-flex">
                <div class="role-selector">
                    <button type="button" class="btn gradient-blue me-3" id="roleButton">
                        Role: {{ ucfirst(session('current_role')) }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" id="roleDropdown">
                        @foreach (Auth::user()->roles->pluck('name') as $role)
                            <a class="dropdown-item role-switch" href="#"
                                data-role="{{ @$role }}">{{ ucwords(@$role) }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-primary d-none d-md-block">Hai, {{ ucwords(Auth::user()->name) }}!</button>
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->avatar_google ? Auth::user()->avatar_google : asset('/assets/images/profile/user-1.jpg') }}" alt="" width="35"
                                height="35" class="rounded-circle">
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="drop2">
                            <div class="message-body">
                                <button class="btn btn-primary d-md-none w-100">
                                    Hai, {{ ucwords(Auth::user()->name) }}!
                                </button>
                                <form action="{{ route('logout') }}" method="get">
                                    @csrf
                                    <button type="submit" class=" mt-2 btn btn-outline-primary d-block w-100"
                                        id="logout">Logout</button>
                                </form>
                            </div>
                        </div>
                    </li>
                </div>
            </ul>
        </div>
    </nav>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleButton = document.getElementById('roleButton');
        const roleDropdown = document.getElementById('roleDropdown');

        roleButton.addEventListener('click', function() {
            roleDropdown.classList.toggle('show');
        });

        // Close the dropdown if clicked outside
        window.addEventListener('click', function(event) {
            if (!event.target.matches('#roleButton')) {
                if (roleDropdown.classList.contains('show')) {
                    roleDropdown.classList.remove('show');
                }
            }
        });

        // Handle role selection
        const dropdownItems = document.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            item.addEventListener('click', function(event) {
                event.preventDefault();
                const selectedRole = this.getAttribute('data-role');
                roleButton.textContent =
                    `Role: ${selectedRole.charAt(0).toUpperCase() + selectedRole.slice(1)}`;
                roleDropdown.classList.remove('show');
                // You can add additional logic here to handle role change
            });
        });
    });
</script>
