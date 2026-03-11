@extends('layouts.app')

@section('content')
    <h1 class="hero-giant text-white text-3xl sm:text-4xl mb-8">Личный кабинет</h1>

    <div class="grid gap-8 lg:grid-cols-[1fr,2fr]">
        {{-- Settings --}}
        <div class="border-2 border-white/10 bg-[#0a0a0a] p-6" id="city">
            <h2 class="font-black text-white mb-4 uppercase tracking-wider text-sm">Настройки</h2>
            <p class="text-xs text-zinc-600 mb-2 font-bold uppercase tracking-wider">Город для афиши</p>
            <form method="post" action="{{ route('cabinet.city.update') }}" class="flex gap-2">
                @csrf
                <select name="city_id" class="flex-1 bg-black border border-white/10 px-3 py-2.5 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 text-sm">
                    @foreach($cities as $c)
                        <option value="{{ $c->id }}" {{ $user->city_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-xs font-black uppercase tracking-wider transition-colors">Сохранить</button>
            </form>
        </div>

        {{-- Tickets --}}
        <div id="tickets">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <h2 class="font-black text-lg text-white uppercase tracking-wider">
                    Мои билеты <span class="sparkle text-sm">✦</span>
                </h2>
                <span class="px-4 py-2 border border-white/10 text-xs text-zinc-400 font-bold uppercase tracking-wider">Правила возврата</span>
            </div>
            @if($bookings->isEmpty())
                <div class="border-2 border-white/10 bg-[#0a0a0a] p-10 text-center">
                    <p class="text-zinc-600">У вас пока нет купленных билетов.</p>
                    <a href="{{ route('events.index') }}" class="inline-block mt-3 text-violet-400 hover:text-violet-300 font-bold text-sm uppercase">Перейти к афише →</a>
                </div>
            @else
                <ul class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($bookings as $booking)
                        @php $isPast = $booking->event?->start_at?->isPast(); @endphp
                        <li class="border-2 overflow-hidden transition-colors {{ $isPast ? 'border-white/[0.05] bg-zinc-900/50' : 'border-white/10 bg-[#0a0a0a] hover:border-violet-500/30' }}">
                            <div class="text-xs px-4 pt-4 text-zinc-500 font-bold uppercase tracking-wider">{{ $booking->event->start_at?->format('d F Y, D, H:i') }}</div>
                            <div class="aspect-[16/10] mt-3 overflow-hidden {{ $isPast ? 'grayscale opacity-60' : '' }}">
                                @if($booking->event->poster_is_video)
                                    <video src="{{ $booking->event->poster_src }}" autoplay loop muted playsinline class="w-full h-full object-cover"></video>
                                @elseif($booking->event->poster_src)
                                    <img src="{{ $booking->event->poster_src }}" alt="{{ $booking->event->title }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="p-4 space-y-2">
                                <p class="text-xl font-black {{ $isPast ? 'text-zinc-400' : 'text-white' }}">{{ $booking->event->title }}</p>
                                <p class="text-sm text-zinc-500 font-bold">{{ $booking->tickets_count }} билет{{ $booking->tickets_count > 1 ? 'а' : '' }}</p>
                                <p class="text-sm text-zinc-600">{{ $booking->event->venue?->name }}</p>
                                <p class="text-xs text-zinc-600">Код: <strong class="text-zinc-400">{{ $booking->reference }}</strong> · {{ number_format($booking->total_amount, 0, '', ' ') }} ₽</p>
                            </div>
                            <div class="px-4 pb-4 flex items-center gap-2">
                                <a href="{{ route('cabinet.bookings.show', $booking) }}" class="px-3 py-1.5 text-xs font-black uppercase tracking-wider bg-violet-600/20 border border-violet-500/30 text-violet-300 hover:bg-violet-600/30 hover:text-white transition-colors">Подробнее</a>
                                @if($booking->status !== 'refunded')
                                    <form method="POST" action="{{ route('cabinet.bookings.refund', $booking) }}" onsubmit="return confirm('Вернуть билет?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-black uppercase tracking-wider bg-rose-500/10 border border-rose-500/30 text-rose-300 hover:bg-rose-500/20 hover:text-white transition-colors">Возврат</button>
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
