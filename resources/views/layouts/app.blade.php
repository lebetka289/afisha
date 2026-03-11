<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Афиша событий' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="preload" href="https://unpkg.com/alpinejs@3.13.3/dist/cdn.min.js" as="script">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen antialiased bg-black text-white font-sans" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen max-w-[1920px] mx-auto relative overflow-x-hidden">

        {{-- ══════ SIDEBAR (desktop: fixed, mobile: slide-over) ══════ --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
             class="sidebar-overlay lg:hidden" x-transition.opacity></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed left-0 top-0 bottom-0 w-56 flex flex-col bg-black border-r border-white/10 z-50
                      transition-transform duration-300 lg:transition-none">

            {{-- Logo --}}
            <div class="p-5 pb-2">
                <a href="{{ route('events.index') }}" class="flex items-center gap-3 group" @click="sidebarOpen = false">
                    @if(file_exists(public_path('images/logo.mp4')))
                        <video src="{{ asset('images/logo.mp4') }}" autoplay loop muted playsinline
                               class="h-10 w-10 object-contain rounded-lg group-hover:opacity-80 transition-opacity"></video>
                    @else
                        <span class="text-3xl">🎭</span>
                    @endif
                    <div>
                        <div class="text-[10px] text-violet-400 uppercase tracking-[0.2em] font-bold">Events</div>
                        <div class="font-extrabold text-white text-lg leading-tight">Афиша</div>
                    </div>
                </a>
            </div>

            {{-- Checkerboard decoration --}}
            <div class="checkerboard-sm h-3 mx-5 mb-4 opacity-60"></div>

            {{-- Navigation --}}
            @php
                $categories = ['concert' => 'Концерты', 'theater' => 'Театр', 'show' => 'Шоу', 'standup' => 'Стендап'];
            @endphp
            <nav class="flex-1 flex flex-col gap-0.5 py-2">
                <a href="{{ route('events.index') }}" @click="sidebarOpen = false"
                   class="sidebar-link {{ !request('category') && request()->routeIs('events.index') ? 'active' : '' }}">
                    Главная
                </a>
                @foreach($categories as $key => $label)
                    <a href="{{ route('events.index', array_merge(request()->query(), ['category' => $key])) }}"
                       @click="sidebarOpen = false"
                       class="sidebar-link {{ request('category') === $key ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            {{-- Sidebar footer --}}
            <div class="p-5 pt-2 border-t border-white/10">
                <div class="checkerboard-sm h-2 mb-3 opacity-40"></div>
                <p class="text-[10px] text-zinc-600 uppercase tracking-wider">{{ date('Y') }} · Афиша</p>
            </div>
        </aside>

        {{-- ══════ MAIN CONTENT ══════ --}}
        <div class="flex-1 lg:ml-56 flex flex-col min-h-screen overflow-x-hidden">

            {{-- Marquee running text --}}
            <div class="marquee-track bg-violet-600 text-white font-extrabold text-xs py-1.5 uppercase tracking-[0.15em] select-none">
                <div class="marquee-content">
                    <span>✦ СОБЫТИЯ </span><span>✦ БИЛЕТЫ </span><span>✦ КОНЦЕРТЫ </span><span>✦ ШОУ </span><span>✦ СТЕНДАП </span><span>✦ ТЕАТР </span><span>✦ АРТИСТЫ </span><span>✦ ПРЕМЬЕРЫ </span>
                    <span>✦ СОБЫТИЯ </span><span>✦ БИЛЕТЫ </span><span>✦ КОНЦЕРТЫ </span><span>✦ ШОУ </span><span>✦ СТЕНДАП </span><span>✦ ТЕАТР </span><span>✦ АРТИСТЫ </span><span>✦ ПРЕМЬЕРЫ </span>
                </div>
            </div>

            {{-- Top header --}}
            <header class="sticky top-0 z-30 border-b border-white/10 bg-black/90 backdrop-blur-xl">
                <div class="px-4 lg:px-8 py-3">
                    <div class="flex items-center gap-4 flex-wrap">
                        {{-- Mobile hamburger --}}
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg border border-white/10 text-white hover:bg-white/5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                        </button>

                        {{-- Search --}}
                        <form action="{{ route('events.index') }}" method="get" class="flex-1 min-w-0 max-w-lg relative" x-data="searchSuggest('{{ route('events.suggest') }}')">
                            @if(request('date_from'))<input type="hidden" name="date_from" value="{{ request('date_from') }}">@endif
                            @if(request('date_to'))<input type="hidden" name="date_to" value="{{ request('date_to') }}">@endif
                            @if(request('city'))<input type="hidden" name="city" value="{{ request('city') }}">@endif
                            @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                            <label class="relative block">
                                <span class="sr-only">Поиск</span>
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-600 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                </span>
                                <input type="search" name="q" value="{{ request('q') }}" placeholder="События, артисты и места"
                                       x-model="query"
                                       @focus="openDropdown()"
                                       @input.debounce.250ms="fetchSuggestions()"
                                       class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-white/[0.06] border border-white/10 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 text-white placeholder-zinc-600 transition-colors text-sm">
                            </label>
                            <div x-show="open && items.length" @click.outside="open = false" x-cloak
                                 class="absolute left-0 right-0 top-full mt-2 rounded-xl border border-white/10 bg-[#111] shadow-2xl shadow-black/50 p-3 z-50">
                                <p class="text-xs text-zinc-500 px-3 pb-2 uppercase tracking-wider" x-text="dropdownTitle"></p>
                                <div class="space-y-1">
                                    <template x-for="item in items" :key="item.url">
                                        <a :href="item.url" class="block px-3 py-2 rounded-lg hover:bg-violet-500/10 transition-colors">
                                            <div class="text-sm text-white font-medium" x-text="item.title"></div>
                                            <div class="text-xs text-zinc-500" x-text="[item.date, item.venue].filter(Boolean).join(' · ')"></div>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </form>

                        {{-- Right controls --}}
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- City --}}
                            <div class="relative" x-data="{ open: false, citySearch: '' }">
                                <button type="button" @click="open = !open"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white/[0.06] border border-white/10 hover:border-violet-500/30 text-sm text-zinc-400 hover:text-white transition-colors">
                                    <span class="text-xs">📍</span>
                                    <span class="max-w-[90px] truncate">{{ $currentCityName ?? 'Город' }}</span>
                                    <span class="text-zinc-600 text-xs">▼</span>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-cloak
                                     class="absolute right-0 top-full mt-1.5 w-56 max-h-72 overflow-y-auto rounded-xl border border-white/10 bg-[#111] shadow-2xl shadow-black/50 py-1 z-50 scrollbar-thin">
                                    <div class="px-3 pb-2 pt-2">
                                        <input type="search" x-model="citySearch" placeholder="Поиск города"
                                               class="w-full rounded-lg bg-white/[0.06] border border-white/10 px-3 py-2 text-sm text-white placeholder-zinc-600 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20">
                                    </div>
                                    @foreach($headerCities ?? [] as $c)
                                        <a href="{{ route('events.index', array_merge(request()->query(), ['city' => $c->slug])) }}"
                                           x-show="'{{ mb_strtolower($c->name) }}'.includes(citySearch.toLowerCase())"
                                           class="block px-4 py-2.5 hover:bg-violet-500/10 text-sm text-zinc-400 hover:text-white {{ (request('city') === $c->slug) ? 'bg-violet-500/10 text-white' : '' }}">
                                            {{ $c->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Auth --}}
                            @auth
                                <div class="relative" x-data="{ open: false }">
                                    <button type="button" @click="open = !open"
                                            class="w-9 h-9 rounded-lg overflow-hidden bg-violet-600/30 border border-violet-500/30 font-bold text-sm flex items-center justify-center text-white shrink-0">
                                        @if(auth()->user()->avatar)
                                            @if(auth()->user()->avatar_is_video)
                                                <video src="{{ auth()->user()->avatar_src }}" autoplay loop muted playsinline class="w-full h-full object-cover"></video>
                                            @else
                                                <img src="{{ auth()->user()->avatar_src }}" alt="" class="w-full h-full object-cover">
                                            @endif
                                        @else
                                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                                        @endif
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-cloak
                                         class="absolute right-0 top-full mt-1.5 w-64 rounded-xl border border-white/10 bg-[#111] shadow-2xl shadow-black/50 py-2 z-50">
                                        <div class="px-4 py-2 border-b border-white/10">
                                            <p class="font-bold text-white truncate">{{ auth()->user()->name }}</p>
                                            <p class="text-xs text-zinc-500 truncate">{{ auth()->user()->email }}</p>
                                        </div>
                                        <nav class="py-1">
                                            @if(auth()->user()->is_admin)
                                                <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                    <span class="w-5 text-center text-xs">⚙</span> Админ-панель
                                                </a>
                                            @endif
                                            <a href="{{ route('cabinet.favorites') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                <span class="w-5 text-center text-xs">♥</span> Избранное
                                            </a>
                                            <a href="{{ route('cabinet.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                <span class="w-5 text-center text-xs">🎫</span> Мои билеты
                                            </a>
                                            <a href="{{ route('cabinet.index') }}#city" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                <span class="w-5 text-center text-xs">📍</span> Город
                                            </a>
                                            <a href="{{ route('cabinet.index') }}#tickets" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                <span class="w-5 text-center text-xs">↩</span> Вернуть билет
                                            </a>
                                            <a href="{{ route('cabinet.account') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                <span class="w-5 text-center text-xs">👤</span> Аккаунт
                                            </a>
                                        </nav>
                                        <div class="border-t border-white/10 mt-1 pt-1">
                                            <form method="post" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors text-left">
                                                    <span class="w-5 text-center text-xs">→</span> Выйти
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg text-sm text-zinc-400 hover:text-white transition-colors">Войти</a>
                                <a href="{{ route('register') }}" class="btn-primary text-xs">Регистрация</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            {{-- Alerts --}}
            <div class="px-4 lg:px-8">
                @if(session('status'))
                    <div class="mt-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 text-emerald-200 px-4 py-3 text-sm">
                        {{ session('status') }}
                    </div>
                @endif
                @if(session('errors'))
                    <div class="mt-4 rounded-lg border border-red-500/30 bg-red-500/10 text-red-200 px-4 py-3 text-sm">
                        {{ session('errors')->first() }}
                    </div>
                @endif
            </div>

            {{-- Page content --}}
            <main class="flex-1 px-4 lg:px-8 py-8">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="border-t border-white/10 py-6 mt-auto">
                <div class="px-4 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-600">
                    <div class="flex items-center gap-3">
                        <div class="checkerboard-sm w-6 h-6 opacity-40"></div>
                        <span class="font-bold text-zinc-400 uppercase tracking-wider">Афиша</span>
                    </div>
                    <span>{{ date('Y') }} · События и билеты</span>
                </div>
            </footer>
        </div>
    </div>

    <script defer src="https://unpkg.com/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script>
        function searchSuggest(endpoint) {
            return {
                query: @js(request('q', '')),
                items: [],
                open: false,
                dropdownTitle: 'Популярно сейчас',
                async fetchSuggestions() {
                    try {
                        const response = await fetch(`${endpoint}?q=${encodeURIComponent(this.query)}`, {
                            headers: { Accept: 'application/json' },
                        });
                        const data = await response.json();
                        this.items = data.items || [];
                        this.dropdownTitle = data.title || 'Найденные мероприятия';
                        this.open = this.items.length > 0;
                    } catch (e) {
                        this.items = [];
                        this.open = false;
                    }
                },
                openDropdown() {
                    this.fetchSuggestions();
                },
            };
        }
    </script>
    @stack('scripts')
</body>
</html>
