@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-sm uppercase text-slate-500 tracking-[0.3em]">Админ</p>
            <h1 class="text-3xl font-semibold mt-1">Исполнители</h1>
        </div>
        <a href="{{ route('admin.artists.create') }}" class="px-4 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-400 transition text-white text-sm font-medium">Новый исполнитель</a>
    </div>

    <div class="grid gap-4">
        @forelse($artists as $artist)
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if($artist->photo)
                        <img src="{{ route('media.show', ['path' => $artist->photo]) }}" alt="" class="w-16 h-16 rounded-xl object-cover">
                    @endif
                    <div>
                        <div class="text-xl font-semibold">{{ $artist->name }}</div>
                        <div class="text-sm text-slate-400">{{ $artist->slug }}</div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.artists.edit', $artist) }}" class="text-sm text-indigo-400 hover:text-indigo-200 transition">Редактировать</a>
                    <form method="POST" action="{{ route('admin.artists.destroy', $artist) }}" onsubmit="return confirm('Удалить исполнителя?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-rose-400 hover:text-rose-200 transition">Удалить</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-800 text-center text-slate-500 py-10">
                Исполнителей пока нет
            </div>
        @endforelse
    </div>
@endsection
