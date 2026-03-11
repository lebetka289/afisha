@extends('layouts.app')

@section('content')
    <h1 class="hero-giant text-white text-3xl sm:text-4xl mb-8">Избранное <span class="sparkle text-xl">✦</span></h1>

    @if($events->isEmpty())
        <div class="border-2 border-white/10 bg-[#0a0a0a] p-10 text-center">
            <p class="text-zinc-600">Вы пока не добавили ни одного мероприятия в избранное.</p>
            <a href="{{ route('events.index') }}" class="inline-block mt-3 text-violet-400 hover:text-violet-300 font-bold text-sm uppercase">Перейти к афише →</a>
        </div>
    @else
        <ul class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($events as $event)
                <li class="border-2 border-white/10 bg-[#0a0a0a] overflow-hidden hover:border-violet-500/30 transition-colors group">
                    <a href="{{ route('events.show', $event) }}" class="block">
                        @if($event->poster_src)
                            <div class="aspect-[16/10] bg-black overflow-hidden">
                                @if($event->poster_is_video)
                                    <video src="{{ $event->poster_src }}" autoplay loop muted playsinline class="w-full h-full object-cover group-hover:scale-105 transition duration-500 grayscale-[20%] group-hover:grayscale-0"></video>
                                @else
                                    <img src="{{ $event->poster_src }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 grayscale-[20%] group-hover:grayscale-0">
                                @endif
                            </div>
                        @endif
                        <div class="p-4">
                            <p class="text-xs text-zinc-600 font-bold uppercase tracking-wider">{{ $event->start_at?->format('d.m.Y H:i') }}</p>
                            <p class="font-black text-white mt-1 group-hover:text-violet-400 transition-colors">{{ $event->title }}</p>
                            <p class="text-sm text-zinc-600">{{ $event->venue?->name }}</p>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
