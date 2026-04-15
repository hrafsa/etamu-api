<aside
    x-data="{ collapsed: false }"
    class="hidden md:flex bg-gray-900/80 backdrop-blur border-r border-gray-800 min-h-screen flex-col transition-[width] duration-300 ease-in-out overflow-y-auto"
    :class="collapsed ? 'w-20' : 'w-64'"
>
    <!-- Header/Brand + Hamburger -->
    <div class="flex items-center justify-between px-4 h-16 border-b border-gray-800" :class="collapsed ? 'justify-center' : 'justify-between'" x-transition.opacity>
        <div class="flex items-center gap-3">
            <span
                x-show="!collapsed"
                x-cloak
                x-transition.opacity.duration.200ms
                class="text-sm font-semibold tracking-wide text-gray-100"
            >
                Admin Panel
            </span>
        </div>

        <!-- Tombol Hamburger -->
        <button
            @click="collapsed = !collapsed"
            class="inline-flex items-center justify-center p-2 rounded-md bg-gray-800 text-gray-200 hover:bg-gray-700 transition cursor-pointer"
            :aria-expanded="(!collapsed).toString()"
        >
            <!-- Ikon close -->
            <svg x-show="!collapsed" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <!-- Ikon hamburger -->
            <svg x-show="collapsed" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <!-- Menu Navigasi -->
    <nav class="flex-1 py-3 space-y-1" role="navigation">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="group flex items-center text-sm rounded-md mx-2 transition
          {{ request()->routeIs('dashboard')
                ? 'bg-gray-800 text-white'
                : 'text-gray-300 hover:text-white hover:bg-gray-800' }}"
           :class="collapsed ? 'justify-center px-0 py-2' : 'gap-3 px-3 py-2'">
            <svg class="w-5 h-5 {{ request()->is('dashboard') ? 'text-indigo-300' : 'text-indigo-400 group-hover:text-indigo-300' }}"
                 viewBox="0 0 24 24" fill="none">
                <path d="M3 10.5 12 4l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9.5Z"
                      class="stroke-current" stroke-width="1.4" stroke-linejoin="round"></path>
            </svg>
            <span x-show="!collapsed" x-cloak x-transition.opacity>Dashboard</span>
        </a>

        {{-- Pengajuan --}}
        <a href="{{ route('admin.pengajuan.index') }}"
           class="group flex items-center text-sm rounded-md mx-2 transition
          {{ request()->routeIs('admin.pengajuan.*')
                ? 'bg-gray-800 text-white'
                : 'text-gray-300 hover:text-white hover:bg-gray-800' }}"
           :class="collapsed ? 'justify-center px-0 py-2' : 'gap-3 px-3 py-2'">
            <svg class="w-5 h-5 {{ request()->routeIs('admin.pengajuan.*') ? 'text-indigo-300' : 'text-indigo-400 group-hover:text-indigo-300' }}"
                 fill="none" viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h16M4 18h7"
                      class="stroke-current" stroke-width="1.4" stroke-linecap="round"></path>
            </svg>
            <span x-show="!collapsed" x-cloak x-transition.opacity>Pengajuan</span>
        </a>

        {{-- Categories --}}
        <a href="{{ route('admin.categories.index') }}"
           class="group flex items-center text-sm rounded-md mx-2 transition
          {{ request()->routeIs('admin.categories.*')
                ? 'bg-gray-800 text-white'
                : 'text-gray-300 hover:text-white hover:bg-gray-800' }}"
           :class="collapsed ? 'justify-center px-0 py-2' : 'gap-3 px-3 py-2'">
            <svg class="w-5 h-5 {{ request()->routeIs('admin.categories.*') ? 'text-indigo-300' : 'text-indigo-400 group-hover:text-indigo-300' }}" fill="none" viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h10M4 18h7" class="stroke-current" stroke-width="1.4" stroke-linecap="round" />
            </svg>
            <span x-show="!collapsed" x-cloak x-transition.opacity>Categories</span>
        </a>

        {{-- Management User --}}
        <a href="{{ url('/user-management') }}"
           class="group flex items-center text-sm rounded-md mx-2 transition
          {{ request()->is('user-management')
                ? 'bg-gray-800 text-white'
                : 'text-gray-300 hover:text-white hover:bg-gray-800' }}"
           :class="collapsed ? 'justify-center px-0 py-2' : 'gap-3 px-3 py-2'">
            <svg class="w-5 h-5 {{ request()->is('management-user*') ? 'text-indigo-300' : 'text-indigo-400 group-hover:text-indigo-300' }}"
                 fill="none" viewBox="0 0 24 24">
                <path d="M16 12a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm6 8a8 8 0 1 0-16 0h16Z"
                      class="stroke-current" stroke-width="1.4" stroke-linecap="round"></path>
            </svg>
            <span x-show="!collapsed" x-cloak x-transition.opacity>User Management</span>
        </a>
    </nav>

    <!-- Footer Sidebar -->
    <div class="p-3 border-t border-gray-800 mt-auto">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="group flex items-center text-sm rounded-md text-red-300 hover:text-white hover:bg-red-500/10 transition w-full"
                    :class="collapsed ? 'justify-center px-0 py-2' : 'gap-3 px-3 py-2 text-left'">
                <svg class="w-5 h-5 text-red-400 group-hover:text-red-300" viewBox="0 0 24 24" fill="none">
                    <path d="M15 17l5-5-5-5M20 12H9" class="stroke-current" stroke-width="1.6"
                          stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M4 19V5a2 2 0 0 1 2-2h5" class="stroke-current" stroke-width="1.4"
                          stroke-linecap="round"></path>
                </svg>
                <span x-show="!collapsed" x-cloak x-transition.opacity>Log Out</span>
            </button>
        </form>
    </div>
</aside>
