@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-sm uppercase text-slate-500 tracking-[0.3em]">Админ</p>
            <h1 class="text-3xl font-semibold mt-1">События</h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.artists.index') }}" class="px-4 py-2 rounded-lg border border-slate-700 text-sm hover:border-slate-500 transition">Исполнители</a>
            <a href="{{ route('admin.venues.index') }}" class="px-4 py-2 rounded-lg border border-slate-700 text-sm hover:border-slate-500 transition">Площадки</a>
            <a href="{{ route('admin.events.create') }}" class="px-4 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-400 transition text-white text-sm font-medium">Создать событие</a>
        </div>
    </div>

    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl shadow-slate-950/40">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-900/80 text-slate-400 uppercase tracking-wide text-xs">
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
                    <tr class="border-t border-slate-800/80">
                        <td class="px-6 py-4">
                            <div class="font-medium text-white">{{ $event->title }}</div>
                            <div class="text-xs text-slate-500">{{ $event->subtitle }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ $event->venue?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-300">
                            {{ optional($event->start_at)->format('d.m.Y H:i') ?? '—' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
                            {{ $event->status === 'published' ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/20' : 'bg-amber-500/15 text-amber-200 border border-amber-500/20' }}">
                                {{ strtoupper($event->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('events.show', $event) }}" class="text-xs text-slate-400 hover:text-white transition" target="_blank">Просмотр</a>
                                <a href="{{ route('admin.events.edit', $event) }}" class="text-xs text-indigo-400 hover:text-indigo-200 transition">Редактировать</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-slate-500">Событий пока нет</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $events->links() }}
    </div>
@endsection

