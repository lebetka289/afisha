@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-white mb-8">Понравившиеся мероприятия</h1>

    @if($events->isEmpty())
        <div class="gradient-border p-8 text-center">
            <p class="text-zinc-500">Вы пока не добавили ни одного мероприятия в избранное.</p>
            <a href="{{ route('events.index') }}" class="inline-block mt-3 text-violet-400 hover:text-violet-300 hover:underline">Перейти к афише →</a>
        </div>
    @else
        <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($events as $event)
                <li class="rounded-xl border border-white/[0.08] bg-[#1c1c21] overflow-hidden hover:border-violet-500/30 transition-colors">
                    <a href="{{ route('events.show', $event) }}" class="block">
                        @if($event->poster_url)
                            <div class="aspect-[16/10] bg-zinc-800 overflow-hidden">
                                <img src="{{ $event->poster_url }}" alt="" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div class="p-4">
                            <p class="text-xs text-zinc-500">{{ $event->start_at?->format('d.m.Y H:i') }}</p>
                            <p class="font-semibold text-white mt-1">{{ $event->title }}</p>
                            <p class="text-sm text-zinc-500">{{ $event->venue?->name }}</p>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
