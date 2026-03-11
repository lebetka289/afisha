@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-xs uppercase text-zinc-600 tracking-[0.2em] font-black">Админ</p>
            <h1 class="hero-giant text-white text-3xl mt-1">События</h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.artists.index') }}" class="px-4 py-2 border border-white/10 text-xs font-bold uppercase tracking-wider text-zinc-400 hover:border-violet-500/30 hover:text-white transition">Исполнители</a>
            <a href="{{ route('admin.venues.index') }}" class="px-4 py-2 border border-white/10 text-xs font-bold uppercase tracking-wider text-zinc-400 hover:border-violet-500/30 hover:text-white transition">Площадки</a>
            <a href="{{ route('admin.events.create') }}" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-black uppercase tracking-wider transition">Создать</a>
        </div>
    </div>

    <div class="border-2 border-white/10 bg-[#0a0a0a] overflow-hidden">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-black/50 text-zinc-600 uppercase tracking-wider text-xs font-black">
                <tr>
                    <th class="px-6 py-4">Название</th>
                    <th class="px-6 py-4">Площадка</th>
                    <th class="px-6 py-4">Дата</th>
                    <th class="px-6 py-4">Статус</th>
                    <th class="px-6 py-4 text-right">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr class="border-t border-white/10 hover:bg-violet-500/[0.03] transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($event->poster_src)
                                    @if($event->poster_is_video)
                                        <video src="{{ $event->poster_src }}" autoplay loop muted playsinline class="w-12 h-12 object-cover border border-white/10 bg-black"></video>
                                    @else
                                        <img src="{{ $event->poster_src }}" alt="" class="w-12 h-12 object-cover border border-white/10">
                                    @endif
                                @endif
                                <div>
                                    <div class="font-bold text-white">{{ $event->title }}</div>
                                    <div class="text-xs text-zinc-600">{{ $event->subtitle }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-zinc-400">{{ $event->venue?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-zinc-400">{{ optional($event->start_at)->format('d.m.Y H:i') ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-black uppercase tracking-wider
                                {{ $event->status === 'published' ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/20' : 'bg-amber-500/15 text-amber-200 border border-amber-500/20' }}">
                                {{ strtoupper($event->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('events.show', $event) }}" class="text-xs text-zinc-500 hover:text-white transition font-bold" target="_blank">Просмотр</a>
                                <a href="{{ route('admin.events.edit', $event) }}" class="text-xs text-violet-400 hover:text-violet-300 transition font-bold">Изменить</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-zinc-600">Событий пока нет</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 pagination-wrap">
        {{ $events->links() }}
    </div>
@endsection
