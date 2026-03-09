@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('cabinet.index') }}" class="text-sm text-violet-400 hover:text-violet-300">← Назад к билетам</a>
                <h1 class="text-2xl font-bold text-white mt-2">Билет {{ $booking->reference }}</h1>
                <p class="text-zinc-500 mt-1">{{ $booking->event->title }} · {{ $booking->event->start_at?->format('d.m.Y H:i') }}</p>
            </div>
            @if($booking->status !== 'refunded')
                <form method="POST" action="{{ route('cabinet.bookings.refund', $booking) }}" onsubmit="return confirm('Вернуть билет?')">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 hover:bg-rose-500/20 transition-colors">
                        Вернуть билет
                    </button>
                </form>
            @endif
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.4fr,0.8fr]">
            <div class="space-y-6">
                <div class="gradient-border p-6">
                    <h2 class="text-lg font-semibold text-white mb-4">Информация о событии</h2>
                    <div class="space-y-2 text-sm text-zinc-300">
                        <p><span class="text-zinc-500">Событие:</span> {{ $booking->event->title }}</p>
                        <p><span class="text-zinc-500">Площадка:</span> {{ $booking->event->venue?->name }}</p>
                        <p><span class="text-zinc-500">Адрес:</span> {{ $booking->event->venue?->address }}</p>
                        <p><span class="text-zinc-500">Дата:</span> {{ $booking->event->start_at?->format('d.m.Y H:i') }}</p>
                        <p><span class="text-zinc-500">Покупатель:</span> {{ $booking->customer_name }} · {{ $booking->customer_email }}</p>
                        <p><span class="text-zinc-500">Статус:</span> {{ $booking->status === 'refunded' ? 'Возвращён' : 'Активен' }}</p>
                    </div>
                </div>

                <div class="gradient-border p-6">
                    <h2 class="text-lg font-semibold text-white mb-4">Какие места куплены</h2>
                    <div class="space-y-3">
                        @foreach($booking->items as $item)
                            <div class="flex items-center justify-between gap-4 rounded-xl border border-white/[0.08] bg-white/[0.03] p-4">
                                <div>
                                    <p class="text-white font-medium">{{ $item->seat_label }}</p>
                                    <p class="text-sm text-zinc-500">{{ $item->section?->name }}</p>
                                </div>
                                <div class="text-right text-sm">
                                    <p class="text-zinc-500">Цена</p>
                                    <p class="text-white font-semibold">{{ number_format($item->price, 0, '', ' ') }} ₽</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($booking->addons->isNotEmpty())
                    <div class="gradient-border p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">Дополнения к заказу</h2>
                        <div class="space-y-3">
                            @foreach($booking->addons as $addon)
                                <div class="flex items-center justify-between gap-4 rounded-xl border border-white/[0.08] bg-white/[0.03] p-4">
                                    <div>
                                        <p class="text-white font-medium">{{ $addon->eventAddon?->name }}</p>
                                        <p class="text-sm text-zinc-500">Количество: {{ $addon->quantity }}</p>
                                    </div>
                                    <div class="text-right text-sm">
                                        <p class="text-zinc-500">Сумма</p>
                                        <p class="text-white font-semibold">{{ number_format($addon->price * $addon->quantity, 0, '', ' ') }} ₽</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="gradient-border p-6 text-center">
                    <h2 class="text-lg font-semibold text-white mb-4">QR-код билета</h2>
                    <div class="rounded-2xl bg-white p-4 inline-block">
                        <img src="{{ $qrUrl }}" alt="QR-код билета" class="w-52 h-52">
                    </div>
                    <p class="text-xs text-zinc-500 mt-4">Покажите этот QR-код на входе.</p>
                </div>

                <div class="gradient-border p-6">
                    <h2 class="text-lg font-semibold text-white mb-4">Итог</h2>
                    <div class="space-y-2 text-sm text-zinc-300">
                        <p><span class="text-zinc-500">Билетов:</span> {{ $booking->tickets_count }}</p>
                        <p><span class="text-zinc-500">Сумма:</span> {{ number_format($booking->total_amount, 0, '', ' ') }} ₽</p>
                        <p><span class="text-zinc-500">Код заказа:</span> {{ $booking->reference }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
