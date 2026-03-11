@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('cabinet.index') }}" class="text-sm text-violet-400 hover:text-violet-300 font-bold uppercase tracking-wider">← Назад</a>
                <h1 class="hero-giant text-white text-2xl mt-2">Билет {{ $booking->reference }}</h1>
                <p class="text-zinc-500 mt-1 text-sm">{{ $booking->event->title }} · {{ $booking->event->start_at?->format('d.m.Y H:i') }}</p>
            </div>
            @if($booking->status !== 'refunded')
                <form method="POST" action="{{ route('cabinet.bookings.refund', $booking) }}" onsubmit="return confirm('Вернуть билет?')">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-rose-500/10 border-2 border-rose-500/30 text-rose-300 hover:bg-rose-500/20 font-black text-xs uppercase tracking-wider transition-colors">
                        Вернуть билет
                    </button>
                </form>
            @endif
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.4fr,0.8fr]">
            <div class="space-y-6">
                <div class="border-2 border-white/10 bg-[#0a0a0a] p-6">
                    <h2 class="text-lg font-black text-white mb-4 uppercase tracking-wider">О событии</h2>
                    <div class="space-y-2 text-sm text-zinc-400">
                        <p><span class="text-zinc-600 font-bold">Событие:</span> {{ $booking->event->title }}</p>
                        <p><span class="text-zinc-600 font-bold">Площадка:</span> {{ $booking->event->venue?->name }}</p>
                        <p><span class="text-zinc-600 font-bold">Адрес:</span> {{ $booking->event->venue?->address }}</p>
                        <p><span class="text-zinc-600 font-bold">Дата:</span> {{ $booking->event->start_at?->format('d.m.Y H:i') }}</p>
                        <p><span class="text-zinc-600 font-bold">Покупатель:</span> {{ $booking->customer_name }} · {{ $booking->customer_email }}</p>
                        <p><span class="text-zinc-600 font-bold">Статус:</span> {{ $booking->status === 'refunded' ? 'Возвращён' : 'Активен' }}</p>
                    </div>
                </div>

                <div class="border-2 border-white/10 bg-[#0a0a0a] p-6">
                    <h2 class="text-lg font-black text-white mb-4 uppercase tracking-wider">Места</h2>
                    <div class="space-y-3">
                        @foreach($booking->items as $item)
                            <div class="flex items-center justify-between gap-4 border border-white/10 bg-black/50 p-4">
                                <div>
                                    <p class="text-white font-bold">{{ $item->seat_label }}</p>
                                    <p class="text-sm text-zinc-600">{{ $item->section?->name }}</p>
                                </div>
                                <div class="text-right text-sm">
                                    <p class="text-zinc-600 text-xs font-bold uppercase">Цена</p>
                                    <p class="text-white font-black">{{ number_format($item->price, 0, '', ' ') }} ₽</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($booking->addons->isNotEmpty())
                    <div class="border-2 border-white/10 bg-[#0a0a0a] p-6">
                        <h2 class="text-lg font-black text-white mb-4 uppercase tracking-wider">Дополнения</h2>
                        <div class="space-y-3">
                            @foreach($booking->addons as $addon)
                                <div class="flex items-center justify-between gap-4 border border-white/10 bg-black/50 p-4">
                                    <div>
                                        <p class="text-white font-bold">{{ $addon->eventAddon?->name }}</p>
                                        <p class="text-sm text-zinc-600">Кол-во: {{ $addon->quantity }}</p>
                                    </div>
                                    <div class="text-right text-sm">
                                        <p class="text-zinc-600 text-xs font-bold uppercase">Сумма</p>
                                        <p class="text-white font-black">{{ number_format($addon->price * $addon->quantity, 0, '', ' ') }} ₽</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="border-2 border-violet-500/30 bg-[#0a0a0a] p-6 text-center">
                    <h2 class="text-lg font-black text-white mb-4 uppercase tracking-wider">QR-код</h2>
                    <div class="bg-white p-4 inline-block">
                        <img src="{{ $qrUrl }}" alt="QR" class="w-52 h-52">
                    </div>
                    <p class="text-xs text-zinc-600 mt-4 font-bold">Покажите на входе</p>
                </div>

                <div class="border-2 border-white/10 bg-[#0a0a0a] p-6">
                    <h2 class="text-lg font-black text-white mb-4 uppercase tracking-wider">Итог</h2>
                    <div class="space-y-2 text-sm text-zinc-400">
                        <p><span class="text-zinc-600 font-bold">Билетов:</span> {{ $booking->tickets_count }}</p>
                        <p><span class="text-zinc-600 font-bold">Сумма:</span> {{ number_format($booking->total_amount, 0, '', ' ') }} ₽</p>
                        <p><span class="text-zinc-600 font-bold">Код:</span> {{ $booking->reference }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
