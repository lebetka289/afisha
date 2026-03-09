<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Афиша событий' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
    <style>html{scroll-behavior:smooth}</style>
</head>
<body class="min-h-screen antialiased bg-[#0f0f12] text-zinc-100 font-sans">
    <header class="sticky top-0 z-30 border-b border-white/[0.08] bg-[#0f0f12]/90 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex items-center gap-4 sm:gap-6 flex-wrap">
                <a href="{{ route('events.index') }}" class="flex items-center gap-2 shrink-0 group">
                    @if(file_exists(public_path('images/logo.mp4')))
                        <video src="{{ asset('images/logo.mp4') }}" autoplay loop muted playsinline class="h-11 w-auto object-contain group-hover:opacity-90 transition-opacity" aria-hidden="true"></video>
                    @else
                        <span class="gradient-text text-2xl font-bold">А</span>
                    @endif
                    <span class="font-semibold text-lg hidden sm:inline bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">Афиша</span>
                </a>

                <form action="{{ route('events.index') }}" method="get" class="flex-1 min-w-0 max-w-md relative" x-data="searchSuggest('{{ route('events.suggest') }}')">
                    @if(request('date_from'))<input type="hidden" name="date_from" value="{{ request('date_from') }}">@endif
                    @if(request('date_to'))<input type="hidden" name="date_to" value="{{ request('date_to') }}">@endif
                    @if(request('city'))<input type="hidden" name="city" value="{{ request('city') }}">@endif
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    <label class="relative block">
                        <span class="sr-only">Поиск</span>
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none">🔍</span>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="События, артисты и места"
                               x-model="query"
                               @focus="openDropdown()"
                               @input.debounce.250ms="fetchSuggestions()"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white/[0.06] border border-white/[0.08] focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 text-white placeholder-zinc-500 transition-colors">
                    </label>
                    <div x-show="open && items.length" @click.outside="open = false" x-cloak class="absolute left-0 right-0 top-full mt-2 rounded-2xl border border-white/[0.08] bg-[#18181c] shadow-xl shadow-black/30 p-3 z-50">
                        <p class="text-sm text-zinc-500 px-3 pb-2" x-text="dropdownTitle"></p>
                        <div class="space-y-1">
                            <template x-for="item in items" :key="item.url">
                                <a :href="item.url" class="block px-3 py-2 rounded-xl hover:bg-white/5 transition-colors">
                                    <div class="text-sm text-white" x-text="item.title"></div>
                                    <div class="text-xs text-zinc-500" x-text="[item.date, item.venue].filter(Boolean).join(' · ')"></div>
                                </a>
                            </template>
                        </div>
                    </div>
                </form>

                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <div class="relative" x-data="{ open: false, citySearch: '' }">
                        <button type="button" @click="open = !open" class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/[0.06] border border-white/[0.08] hover:border-violet-500/30 text-sm text-zinc-300 hover:text-white transition-colors">
                            <span>📍</span>
                            <span class="max-w-[100px] sm:max-w-[120px] truncate">{{ $currentCityName ?? 'Город' }}</span>
                            <span class="text-zinc-500 text-xs">▼</span>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 top-full mt-1.5 w-56 max-h-72 overflow-y-auto rounded-xl border border-white/[0.08] bg-[#18181c] shadow-xl shadow-black/30 py-1 z-50 scrollbar-thin">
                            <div class="px-3 pb-2">
                                <input type="search" x-model="citySearch" placeholder="Поиск города"
                                       class="w-full rounded-xl bg-white/[0.06] border border-white/[0.08] px-3 py-2 text-sm text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20">
                            </div>
                            @foreach($headerCities ?? [] as $c)
                                <a href="{{ route('events.index', array_merge(request()->query(), ['city' => $c->slug])) }}"
                                   x-show="'{{ mb_strtolower($c->name) }}'.includes(citySearch.toLowerCase())"
                                   class="block px-4 py-2.5 hover:bg-violet-500/10 text-sm text-zinc-300 hover:text-white {{ (request('city') === $c->slug) ? 'bg-violet-500/10 text-white' : '' }}">
                                    {{ $c->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="w-9 h-9 rounded-xl overflow-hidden bg-gradient-to-br from-violet-500/30 to-cyan-500/30 border border-violet-500/30 font-semibold text-sm flex items-center justify-center text-white/90 shrink-0">
                                @if(auth()->user()->avatar)
                                    <img src="{{ route('media.show', ['path' => auth()->user()->avatar]) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                                @endif
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 top-full mt-1.5 w-64 rounded-xl border border-white/[0.08] bg-[#18181c] shadow-xl shadow-black/30 py-2 z-50">
                                <div class="px-4 py-2 border-b border-white/[0.08]">
                                    <p class="font-medium text-white truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-zinc-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <nav class="py-1">
                                    @if(auth()->user()->is_admin)
                                        <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-300 hover:bg-white/5 hover:text-white transition-colors">
                                            <span class="text-zinc-500 w-5 text-center">⚙</span> Вход в админ-панель
                                        </a>
                                    @endif
                                    <a href="{{ route('cabinet.favorites') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-300 hover:bg-white/5 hover:text-white transition-colors">
                                        <span class="text-zinc-500 w-5 text-center">♥</span> Понравившиеся мероприятия
                                    </a>
                                    <a href="{{ route('cabinet.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-300 hover:bg-white/5 hover:text-white transition-colors">
                                        <span class="text-zinc-500 w-5 text-center">🎫</span> Мои билеты
                                    </a>
                                    <a href="{{ route('cabinet.index') }}#city" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-300 hover:bg-white/5 hover:text-white transition-colors">
                                        <span class="text-zinc-500 w-5 text-center">📍</span> Город
                                    </a>
                                    <a href="{{ route('cabinet.index') }}#tickets" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-300 hover:bg-white/5 hover:text-white transition-colors">
                                        <span class="text-zinc-500 w-5 text-center">↩</span> Вернуть билет
                                    </a>
                                    <a href="{{ route('cabinet.account') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-300 hover:bg-white/5 hover:text-white transition-colors">
                                        <span class="text-zinc-500 w-5 text-center">👤</span> Управление аккаунтом
                                    </a>
                                </nav>
                                <div class="border-t border-white/[0.08] mt-1 pt-1">
                                    <form method="post" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-zinc-300 hover:bg-white/5 hover:text-white transition-colors text-left">
                                            <span class="text-zinc-500 w-5 text-center">→</span> Выйти
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 rounded-xl text-sm text-zinc-400 hover:text-white transition-colors">Войти</a>
                        <a href="{{ route('register') }}" class="btn-primary text-sm rounded-xl inline-flex items-center">Регистрация</a>
                    @endauth
                </div>
            </div>
        </div>
        @php
            $categories = ['concert' => 'Концерты', 'theater' => 'Театр', 'show' => 'Шоу', 'standup' => 'Стендап'];
        @endphp
        <div class="max-w-7xl mx-auto px-4 pb-3">
            <div class="flex items-center gap-3 overflow-x-auto whitespace-nowrap text-sm text-zinc-400">
                @foreach($categories as $key => $label)
                    <a href="{{ route('events.index', array_merge(request()->query(), ['category' => $key])) }}"
                       class="px-2 py-1 rounded-lg transition-colors {{ request('category') === $key ? 'text-white bg-white/[0.08]' : 'hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
                @if(request('category'))
                    <a href="{{ route('events.index', array_filter(request()->query(), fn ($value, $queryKey) => $queryKey !== 'category', ARRAY_FILTER_USE_BOTH)) }}"
                       class="px-2 py-1 rounded-lg text-violet-400 hover:text-violet-300">
                        Сбросить
                    </a>
                @endif
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @if(session('status'))
            <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-200 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        @endif
        @if(session('errors'))
            <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 text-red-200 px-4 py-3 text-sm">
                {{ session('errors')->first() }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-16 border-t border-white/[0.08] py-8">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-zinc-500">
            <span class="gradient-text font-semibold">Афиша</span>
            <span>{{ date('Y') }} · События и билеты</span>
        </div>
    </footer>
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
