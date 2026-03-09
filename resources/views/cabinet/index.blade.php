@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-white mb-8">Личный кабинет</h1>

    <div class="grid gap-8 lg:grid-cols-[1fr,2fr]">
        <div class="gradient-border p-6" id="city">
            <h2 class="font-semibold text-white mb-4">Настройки</h2>
            <p class="text-sm text-zinc-500 mb-2">Город для афиши</p>
            <form method="post" action="{{ route('cabinet.city.update') }}" class="flex gap-2">
                @csrf
                <select name="city_id" class="flex-1 rounded-xl bg-white/[0.06] border border-white/[0.08] px-3 py-2.5 text-white focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20">
                    @foreach($cities as $c)
                        <option value="{{ $c->id }}" {{ $user->city_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2.5 rounded-xl btn-primary text-sm font-medium">Сохранить</button>
            </form>
        </div>

        <div id="tickets">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <h2 class="font-semibold text-lg text-white">Мои билеты</h2>
                <span class="px-4 py-2 rounded-xl bg-white/[0.04] border border-white/[0.08] text-sm text-zinc-300">Правила возврата</span>
            </div>
            @if($bookings->isEmpty())
                <div class="gradient-border p-8 text-center">
                    <p class="text-zinc-500">У вас пока нет купленных билетов.</p>
                    <a href="{{ route('events.index') }}" class="inline-block mt-3 text-violet-400 hover:text-violet-300 hover:underline">Перейти к афише →</a>
                </div>
            @else
                <ul class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($bookings as $booking)
                        @php
                            $isPast = $booking->event?->start_at?->isPast();
                        @endphp
                        <li class="rounded-2xl overflow-hidden border transition-colors {{ $isPast ? 'border-white/[0.06] bg-zinc-800/40' : 'border-white/[0.08] bg-[#1c1c21] hover:border-violet-500/20' }}">
                            <div class="text-xs px-4 pt-4 text-zinc-400">{{ $booking->event->start_at?->format('d F Y, D, H:i') }}</div>
                            <div class="aspect-[16/10] mt-3 overflow-hidden {{ $isPast ? 'grayscale opacity-70' : '' }}">
                                <img src="{{ $booking->event->poster_url }}" alt="{{ $booking->event->title }}" class="w-full h-full object-cover">
                            </div>
                            <div class="p-4 space-y-2">
                                <p class="text-xl font-semibold {{ $isPast ? 'text-zinc-300' : 'text-white' }}">{{ $booking->event->title }}</p>
                                <p class="text-sm text-zinc-400">{{ $booking->tickets_count }} билет{{ $booking->tickets_count > 1 ? 'а' : '' }}</p>
                                <p class="text-sm text-zinc-500">{{ $booking->event->venue?->name }}</p>
                                <p class="text-xs text-zinc-500">Код: <strong class="text-zinc-300">{{ $booking->reference }}</strong> · {{ number_format($booking->total_amount, 0, '', ' ') }} ₽</p>
                            </div>
                            <div class="px-4 pb-4 flex items-center gap-2">
                                <a href="{{ route('cabinet.bookings.show', $booking) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-violet-500/20 border border-violet-500/30 text-violet-300 hover:bg-violet-500/30 hover:text-white transition-colors">Подробнее</a>
                                @if($booking->status !== 'refunded')
                                    <form method="POST" action="{{ route('cabinet.bookings.refund', $booking) }}" onsubmit="return confirm('Вернуть билет?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-rose-500/10 border border-rose-500/30 text-rose-300 hover:bg-rose-500/20 hover:text-white transition-colors">Вернуть билет</button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
