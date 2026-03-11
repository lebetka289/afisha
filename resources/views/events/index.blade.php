@extends('layouts.app')

@section('content')
    @php
        $cityLabel = $filters['city'] ? ($cities->firstWhere('slug', $filters['city'])?->name ?? '') : '';
        $pageTitle = $cityLabel ? "Афиша в {$cityLabel}" : 'Афиша событий';
        $featured = $events->take(2);
    @endphp

    {{-- ══════ HERO SECTION ══════ --}}
    <section class="relative overflow-hidden mb-8">
        {{-- Diagonal running text (homepage only) --}}
        <div class="diagonal-banner bg-white text-black w-[140%] -ml-[20%] rotate-[-2deg] z-10 py-2 mt-2 mb-6 torn-edge-bottom">
            <div class="marquee-track">
                <div class="marquee-content text-[11px] font-black tracking-[0.2em]">
                    СОБЫТИЯ ✦ БИЛЕТЫ ✦ КОНЦЕРТЫ ✦ ШОУ ✦ СТЕНДАП ✦ ТЕАТР ✦ ПРЕМЬЕРЫ ✦ АФИША ✦
                    СОБЫТИЯ ✦ БИЛЕТЫ ✦ КОНЦЕРТЫ ✦ ШОУ ✦ СТЕНДАП ✦ ТЕАТР ✦ ПРЕМЬЕРЫ ✦ АФИША ✦
                </div>
            </div>
        </div>

        {{-- Hero grid: 2 featured images + promo --}}
        <div class="relative grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_220px] gap-3 mt-6">
            <div class="checkerboard absolute -top-4 -right-4 w-16 h-16 opacity-50 hidden md:block z-0"></div>

            @foreach($featured as $i => $feat)
                <a href="{{ route('events.show', $feat) }}" class="img-frame aspect-[4/3] sm:aspect-[3/4] lg:aspect-auto lg:h-[280px] block group" data-poster-hover>
                    @if($feat->poster_is_video)
                        <video src="{{ $feat->poster_src }}" loop muted playsinline preload="metadata"
                               class="w-full h-full object-cover" data-hover-video></video>
                    @elseif($feat->poster_src)
                        <img src="{{ $feat->poster_src }}" alt="{{ $feat->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-zinc-900 flex items-center justify-center text-zinc-700 text-4xl">🎭</div>
                    @endif
                </a>
            @endforeach

            <div class="flex flex-col justify-center items-start gap-3 px-2 sm:col-span-2 lg:col-span-1">
                <div class="border-2 border-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    Новые события
                </div>
                <p class="text-sm text-zinc-400 leading-relaxed">
                    Лучшие концерты, шоу, спектакли и стендап в вашем городе.
                </p>
                <a href="#events-list" class="btn-white text-xs">Смотреть все</a>
                <div class="flex gap-3 mt-1">
                    <span class="sparkle text-lg">✦</span>
                    <span class="sparkle text-xs mt-1">✦</span>
                </div>
            </div>
        </div>

        {{-- Giant text --}}
        <div class="mt-6 mb-2">
            <h1 class="hero-giant text-white text-3xl sm:text-5xl lg:text-7xl">
                <span class="block">Лучшие</span>
                <span class="block">События <span class="text-violet-500">города</span></span>
            </h1>
            <span class="sparkle text-xl inline-block ml-3 -mt-3">✦</span>
        </div>
        <div class="checkerboard w-12 h-12 opacity-40 -ml-2 mt-1"></div>
    </section>

    <div class="h-px bg-white/10 my-4"></div>

    <div x-data="{ view: 'cards' }">

    {{-- ══════ DATE ROULETTE ══════ --}}
    <div class="mb-8 rounded-xl border border-white/10 bg-[#0a0a0a] p-4" x-data="dateRoulette()">
        <form method="get" action="{{ route('events.index') }}" id="date-filter-form">
            @if(request('city'))<input type="hidden" name="city" value="{{ request('city') }}">@endif
            @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
            @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
            <input type="hidden" name="date_from" :value="dateFrom">
            <input type="hidden" name="date_to" :value="dateTo">
        </form>
        <p class="text-xs font-bold uppercase tracking-[0.15em] text-zinc-600 mb-3" x-text="currentMonthLabel"></p>
        <div class="flex gap-2 overflow-x-auto pb-2 -mx-1 scrollbar-thin">
            @foreach($rouletteDates as $d)
                @php
                    $isFrom = ($filters['date_from'] ?? '') === $d['date'];
                    $isTo = ($filters['date_to'] ?? '') === $d['date'];
                    $inRange = $filters['date_from'] && $filters['date_to'] && $d['date'] >= $filters['date_from'] && $d['date'] <= $filters['date_to'];
                @endphp
                <button type="button"
                        class="date-chip shrink-0 flex flex-col items-center justify-center w-14 py-2.5 rounded-lg border-2 transition select-none
                            {{ $d['is_weekend'] ? 'text-rose-400' : 'text-zinc-500' }}
                            {{ $isFrom || $isTo || $inRange ? 'border-violet-500 bg-violet-500/20 text-white' : 'border-white/10 hover:border-violet-500/40 hover:bg-violet-500/10 hover:text-zinc-300' }}"
                        data-date="{{ $d['date'] }}"
                        @click="toggleDate('{{ $d['date'] }}')">
                    <span class="text-lg font-bold leading-none">{{ $d['day'] }}</span>
                    <span class="text-xs mt-0.5 opacity-80">{{ $d['weekday'] }}</span>
                </button>
            @endforeach
        </div>
        <p class="text-sm text-zinc-500 mt-3" x-show="dateFrom || dateTo">
            <span x-show="dateFrom && dateTo && dateFrom === dateTo">Выбран: <span x-text="formatDate(dateFrom)" class="text-zinc-300 font-medium"></span></span>
            <span x-show="dateFrom && dateTo && dateFrom !== dateTo">Диапазон: <span x-text="formatDate(dateFrom)" class="text-zinc-300 font-medium"></span> — <span x-text="formatDate(dateTo)" class="text-zinc-300 font-medium"></span></span>
            <button type="button" @click="clearDates()" class="ml-2 text-violet-400 hover:text-violet-300 hover:underline text-xs uppercase">Сбросить</button>
        </p>
    </div>

    {{-- ══════ FILTERS ══════ --}}
    @if($filters['date_from'] || $filters['date_to'] || $filters['city'] || $filters['q'])
        <div class="mb-6">
            <a href="{{ route('events.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-white/10 text-xs text-zinc-500 hover:bg-white/5 hover:text-zinc-300 uppercase tracking-wider font-bold transition-colors">
                × Сбросить фильтры
            </a>
        </div>
    @endif

    {{-- ══════ VIEW TOGGLE ══════ --}}
    <div class="flex items-center justify-between gap-4 mb-4" id="events-list">
        <h2 class="text-base font-bold text-white uppercase tracking-wider">
            {{ $pageTitle }}
            <span class="sparkle text-sm ml-1">✦</span>
        </h2>
        <div class="flex items-center gap-2">
            <div class="view-toggle flex rounded-lg bg-white/[0.04] border border-white/10 p-0.5 gap-0.5">
                <button type="button" @click="view = 'table'; $refs.tbl.classList.remove('hidden'); $refs.slider.classList.add('hidden')"
                        :class="view === 'table' ? 'active' : ''"
                        class="px-2.5 py-1 rounded text-[11px] font-bold text-zinc-500 uppercase">Таблица</button>
                <button type="button" @click="view = 'cards'; $refs.tbl.classList.add('hidden'); $refs.slider.classList.remove('hidden')"
                        :class="view === 'cards' ? 'active' : ''"
                        class="px-2.5 py-1 rounded text-[11px] font-bold text-zinc-500 uppercase">Карточки</button>
            </div>
            <div class="hidden sm:flex items-center gap-1">
                <button type="button" class="slider-nav" data-slider-prev aria-label="Назад">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button type="button" class="slider-nav" data-slider-next aria-label="Вперёд">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════ TABLE VIEW ══════ --}}
    <div x-ref="tbl" id="events-table-wrap" class="hidden rounded-xl border border-white/10 bg-[#0a0a0a] overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="events-table">
                <thead>
                    <tr>
                        <th class="poster-cell">Фото</th>
                        <th>Событие</th>
                        <th>Дата и время</th>
                        <th>Площадка</th>
                        <th class="text-right">Цена от</th>
                        <th class="w-24"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        @php $minPrice = $event->sections->min('price') ?? 0; @endphp
                        <tr>
                            <td class="poster-cell" data-poster-hover>
                                @if($event->poster_is_video)
                                    <video src="{{ $event->poster_src }}" loop muted playsinline preload="metadata"
                                           class="poster-thumb object-cover" data-hover-video></video>
                                @elseif($event->poster_src)
                                    <img src="{{ $event->poster_src }}" alt="" class="poster-thumb" loading="lazy">
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('events.show', $event) }}" class="font-bold text-white hover:text-violet-400 transition-colors text-sm">
                                    {{ $event->title }}
                                </a>
                                @if($event->subtitle)
                                    <p class="text-xs text-zinc-600 mt-0.5">{{ $event->subtitle }}</p>
                                @endif
                            </td>
                            <td class="text-zinc-500 text-xs whitespace-nowrap">
                                {{ $event->start_at ? $event->start_at->format('d.m.Y · H:i') : '—' }}
                            </td>
                            <td class="text-zinc-500 text-xs">{{ $event->venue?->name ?? '—' }}</td>
                            <td class="text-right text-zinc-300 font-bold whitespace-nowrap text-xs">
                                @if($minPrice > 0)от {{ number_format($minPrice, 0, '', ' ') }} ₽@else—@endif
                            </td>
                            <td>
                                <a href="{{ route('events.show', $event) }}" class="btn-outline-purple text-[11px] py-1 px-2">
                                    Билеты
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-zinc-600 text-sm">
                                По выбранным фильтрам событий не найдено.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════ SLIDER VIEW ══════ --}}
    <div x-ref="slider" id="events-slider-wrap">
        <div class="event-slider" data-event-slider>
            @forelse($events as $event)
                @php $minPrice = $event->sections->min('price') ?? 0; @endphp
                <a href="{{ route('events.show', $event) }}"
                   class="event-slider-card group block border border-white/10 bg-[#0a0a0a] overflow-hidden hover:border-violet-500/50">
                    <div class="aspect-[3/4] relative overflow-hidden" data-poster-hover>
                        @if($event->poster_is_video)
                            <video src="{{ $event->poster_src }}" loop muted playsinline preload="metadata"
                                   class="w-full h-full object-cover group-hover:scale-105 transition duration-500 grayscale-[20%] group-hover:grayscale-0"
                                   data-hover-video></video>
                        @elseif($event->poster_src)
                            <img src="{{ $event->poster_src }}" alt="{{ $event->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500 grayscale-[20%] group-hover:grayscale-0">
                        @else
                            <div class="w-full h-full bg-zinc-900 flex items-center justify-center text-zinc-800 text-3xl">🎭</div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        @if($minPrice > 0)
                            <span class="absolute bottom-2 left-2 px-2 py-0.5 bg-violet-600 text-white text-[10px] font-bold uppercase tracking-wider">
                                от {{ number_format($minPrice, 0, '', ' ') }} ₽
                            </span>
                        @endif
                    </div>
                    <div class="p-3">
                        <p class="text-[10px] text-zinc-600 font-bold uppercase tracking-wider">
                            {{ $event->start_at ? $event->start_at->format('d.m · H:i') : '—' }}
                        </p>
                        <h3 class="font-extrabold text-sm text-white mt-0.5 group-hover:text-violet-400 transition-colors leading-tight line-clamp-2">
                            {{ $event->title }}
                        </h3>
                        <p class="text-xs text-zinc-500 mt-0.5 truncate">{{ $event->venue?->name ?? '—' }}</p>
                    </div>
                </a>
            @empty
                <div class="flex-1 text-center py-12 text-zinc-600 border border-white/10 bg-[#0a0a0a] min-w-full">
                    По выбранным фильтрам событий не найдено.
                </div>
            @endforelse
        </div>
    </div>

    @if($events->hasMorePages())
        <div class="mt-6 text-center">
            <a href="{{ $events->nextPageUrl() }}"
               class="inline-block px-8 py-3 bg-violet-600 hover:bg-violet-700 text-white font-black text-xs uppercase tracking-wider transition-colors">
                Показать ещё
            </a>
        </div>
    @endif
    </div>

    <div class="flex justify-end mt-8">
        <div class="checkerboard w-16 h-6 opacity-30"></div>
    </div>
@endsection

@push('scripts')
<script>
(function() {
    const slider = document.querySelector('[data-event-slider]');
    if (!slider) return;
    const prevBtn = document.querySelector('[data-slider-prev]');
    const nextBtn = document.querySelector('[data-slider-next]');
    const scrollAmount = 280;
    prevBtn?.addEventListener('click', () => slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' }));
    nextBtn?.addEventListener('click', () => slider.scrollBy({ left: scrollAmount, behavior: 'smooth' }));
})();

(function() {
    document.querySelectorAll('[data-poster-hover]').forEach(container => {
        const video = container.querySelector('[data-hover-video]');
        if (!video) return;
        container.addEventListener('mouseenter', () => {
            video.play().catch(() => {});
        });
        container.addEventListener('mouseleave', () => {
            video.pause();
        });
    });
})();

function dateRoulette() {
    const urlParams = new URLSearchParams(window.location.search);
    return {
        dateFrom: urlParams.get('date_from') || '',
        dateTo: urlParams.get('date_to') || '',
        get currentMonthLabel() {
            if (this.dateFrom) {
                const d = new Date(this.dateFrom);
                const months = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
                return months[d.getMonth()].toUpperCase();
            }
            return 'Выберите даты';
        },
        toggleDate(date) {
            if (!this.dateFrom && !this.dateTo) {
                this.dateFrom = date;
                this.dateTo = date;
            } else if (this.dateFrom && this.dateTo && this.dateFrom === this.dateTo && date !== this.dateFrom) {
                this.dateTo = date;
                if (this.dateTo < this.dateFrom) {
                    [this.dateFrom, this.dateTo] = [this.dateTo, this.dateFrom];
                }
            } else {
                this.dateFrom = date;
                this.dateTo = date;
            }
            const form = document.getElementById('date-filter-form');
            form.querySelector('input[name="date_from"]').value = this.dateFrom;
            form.querySelector('input[name="date_to"]').value = this.dateTo;
            form.submit();
        },
        clearDates() {
            this.dateFrom = '';
            this.dateTo = '';
            const form = document.getElementById('date-filter-form');
            form.querySelector('input[name="date_from"]').value = '';
            form.querySelector('input[name="date_to"]').value = '';
            form.submit();
        },
        formatDate(ymd) {
            if (!ymd) return '';
            const [y, m, d] = ymd.split('-').map(Number);
            const months = ['янв','фев','мар','апр','май','июн','июл','авг','сен','окт','ноя','дек'];
            return d + ' ' + months[m - 1];
        }
    };
}
</script>
@endpush
