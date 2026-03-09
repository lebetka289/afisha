@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-sm uppercase text-slate-500 tracking-[0.3em]">Админ</p>
            <h1 class="text-3xl font-semibold mt-1">Площадки</h1>
        </div>
        <a href="{{ route('admin.venues.create') }}" class="px-4 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-400 transition text-white text-sm font-medium">Новая площадка</a>
    </div>

    <div class="grid gap-4">
        @forelse($venues as $venue)
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="text-xl font-semibold">{{ $venue->name }}</div>
                    <div class="text-sm text-slate-400">{{ $venue->city }} · {{ $venue->address }}</div>
                    <div class="text-xs uppercase tracking-wide text-slate-500 mt-2">Макс. вместимость: {{ $venue->max_capacity }}</div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.venues.edit', $venue) }}" class="text-sm text-indigo-400 hover:text-indigo-200 transition">Редактировать</a>
                    <form method="POST" action="{{ route('admin.venues.destroy', $venue) }}" onsubmit="return confirm('Удалить площадку?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-rose-400 hover:text-rose-200 transition">Удалить</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-800 text-center text-slate-500 py-10">
                Площадок пока нет
            </div>
        @endforelse
    </div>
@endsection

