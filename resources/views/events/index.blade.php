@extends('layouts.app')

@section('content')
    @php
        $cityLabel = $filters['city'] ? ($cities->firstWhere('slug', $filters['city'])?->name ?? '') : '';
        $pageTitle = $cityLabel ? "Афиша событий в {$cityLabel}" : 'Афиша событий';
    @endphp

    <div x-data="{ view: 'cards' }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $pageTitle }}</h1>
        <div class="view-toggle flex rounded-xl bg-white/[0.06] border border-white/[0.08] p-1 gap-0.5">
            <button type="button" @click="view = 'table'; $refs.tbl.classList.remove('hidden'); $refs.cards.classList.add('hidden')"
                    :class="view === 'table' ? 'active' : ''"
                    class="px-3 py-1.5 rounded-lg text-sm text-zinc-400">Таблица</button>
            <button type="button" @click="view = 'cards'; $refs.tbl.classList.add('hidden'); $refs.cards.classList.remove('hidden')"
                    :class="view === 'cards' ? 'active' : ''"
                    class="px-3 py-1.5 rounded-lg text-sm text-zinc-400">Карточки</button>
        </div>
    </div>

    {{-- Рулетка дат --}}
    <div class="mb-8 gradient-border p-4" x-data="dateRoulette()">
        <form method="get" action="{{ route('events.index') }}" id="date-filter-form">
            @if(request('city'))<input type="hidden" name="city" value="{{ request('city') }}">@endif
            @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
            @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
            <input type="hidden" name="date_from" :value="dateFrom">
            <input type="hidden" name="date_to" :value="dateTo">
        </form>
        <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 mb-3" x-text="currentMonthLabel"></p>
        <div class="flex gap-2 overflow-x-auto pb-2 -mx-1 scrollbar-thin">
            @foreach($rouletteDates as $d)
                @php
                    $isFrom = ($filters['date_from'] ?? '') === $d['date'];
                    $isTo = ($filters['date_to'] ?? '') === $d['date'];
                    $inRange = $filters['date_from'] && $filters['date_to'] && $d['date'] >= $filters['date_from'] && $d['date'] <= $filters['date_to'];
                @endphp
                <button type="button"
                        class="date-chip shrink-0 flex flex-col items-center justify-center w-14 py-2.5 rounded-xl border-2 transition select-none
                            {{ $d['is_weekend'] ? 'text-rose-400' : 'text-zinc-400' }}
                            {{ $isFrom || $isTo || $inRange ? 'border-violet-500 bg-violet-500/20 text-white' : 'border-white/10 hover:border-violet-500/40 hover:bg-violet-500/10 hover:text-zinc-200' }}"
                        data-date="{{ $d['date'] }}"
                        @click="toggleDate('{{ $d['date'] }}')">
                    <span class="text-lg font-semibold leading-none">{{ $d['day'] }}</span>
                    <span class="text-xs mt-0.5 opacity-80">{{ $d['weekday'] }}</span>
                </button>
            @endforeach
        </div>
        <p class="text-sm text-zinc-500 mt-3" x-show="dateFrom || dateTo">
            <span x-show="dateFrom && dateTo && dateFrom === dateTo">Выбран день: <span x-text="formatDate(dateFrom)" class="text-zinc-300"></span></span>
            <span x-show="dateFrom && dateTo && dateFrom !== dateTo">Диапазон: <span x-text="formatDate(dateFrom)" class="text-zinc-300"></span> - <span x-text="formatDate(dateTo)" class="text-zinc-300"></span></span>
            <button type="button" @click="clearDates()" class="ml-2 text-violet-400 hover:text-violet-300 hover:underline">Сбросить</button>
        </p>
    </div>

    {{-- Фильтры --}}
    @if($filters['date_from'] || $filters['date_to'] || $filters['city'] || $filters['q'])
        <div class="mb-6">
            <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-white/10 text-sm text-zinc-400 hover:bg-white/5 hover:text-zinc-200 transition-colors">
                Сбросить фильтры
            </a>
        </div>
    @endif

    {{-- Таблица событий --}}
    <div x-ref="tbl" id="events-table-wrap" class="hidden rounded-2xl border border-white/[0.08] bg-[#1c1c21] overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="events-table">
                <thead>
                    <tr>
                        <th class="poster-cell rounded-tl-2xl">Фото</th>
                        <th>Событие</th>
                        <th>Дата и время</th>
                        <th>Площадка</th>
                        <th class="text-right">Цена от</th>
                        <th class="rounded-tr-2xl w-28"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        @php $minPrice = $event->sections->min('price') ?? 0; @endphp
                        <tr>
                            <td class="poster-cell">
                                <img src="{{ $event->poster_url }}" alt="" class="poster-thumb" loading="lazy">
                            </td>
                            <td>
                                <a href="{{ route('events.show', $event) }}" class="font-semibold text-white hover:bg-gradient-to-r hover:from-violet-400 hover:to-cyan-400 hover:bg-clip-text hover:text-transparent transition-all">
                                    {{ $event->title }}
                                </a>
                                @if($event->subtitle)
                                    <p class="text-xs text-zinc-500 mt-0.5">{{ $event->subtitle }}</p>
                                @endif
                            </td>
                            <td class="text-zinc-400 text-sm whitespace-nowrap">
                                {{ $event->start_at ? $event->start_at->format('d.m.Y · H:i') : '—' }}
                            </td>
                            <td class="text-zinc-400 text-sm">{{ $event->venue?->name ?? '—' }}</td>
                            <td class="text-right text-zinc-300 font-medium whitespace-nowrap">
                                @if($minPrice > 0)от {{ number_format($minPrice, 0, '', ' ') }} ₽@else—@endif
                            </td>
                            <td>
                                <a href="{{ route('events.show', $event) }}" class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gradient-to-r from-violet-500/20 to-cyan-500/20 border border-violet-500/30 text-violet-300 hover:from-violet-500/30 hover:to-cyan-500/30 hover:text-white transition-all">
                                    Билеты
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-zinc-500">
                                По выбранным фильтрам событий не найдено. Измените даты или город.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Карточки (скрыто по умолчанию) --}}
    <div x-ref="cards" id="events-cards-wrap" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($events as $event)
            @php $minPrice = $event->sections->min('price') ?? 0; @endphp
            <a href="{{ route('events.show', $event) }}" class="group rounded-2xl border border-white/[0.08] bg-[#1c1c21] overflow-hidden hover:border-violet-500/30 transition-all duration-300 flex flex-col">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <img src="{{ $event->poster_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    @if($minPrice > 0)
                        <span class="absolute bottom-2 left-2 px-2 py-1 rounded-lg bg-black/60 text-white text-xs font-medium">от {{ number_format($minPrice, 0, '', ' ') }} ₽</span>
                    @endif
                </div>
                <div class="p-4 flex flex-col gap-1">
                    <h2 class="font-semibold text-lg leading-tight text-white group-hover:text-violet-300 transition-colors">{{ $event->title }}</h2>
                    <p class="text-sm text-zinc-500">{{ $event->start_at ? $event->start_at->format('d.m · H:i') : '—' }}</p>
                    <p class="text-sm text-zinc-400">{{ $event->venue?->name ?? '—' }}</p>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-12 text-zinc-500 rounded-2xl border border-white/[0.08] bg-[#1c1c21]">
                По выбранным фильтрам событий не найдено.
            </div>
        @endforelse
    </div>

    @if($events->hasMorePages())
        <div class="mt-8">
            <a href="{{ $events->nextPageUrl() }}" class="block w-full text-center px-6 py-4 rounded-full bg-amber-400 hover:bg-amber-300 text-zinc-900 font-semibold transition-colors">
                Показать еще
            </a>
        </div>
    @endif
    </div>
@endsection

@push('scripts')
<script>
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
