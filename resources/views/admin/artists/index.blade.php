@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-xs uppercase text-zinc-600 tracking-[0.2em] font-black">Админ</p>
            <h1 class="hero-giant text-white text-3xl mt-1">Исполнители</h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.events.index') }}" class="px-4 py-2 border border-white/10 text-xs font-bold uppercase tracking-wider text-zinc-400 hover:border-violet-500/30 hover:text-white transition">← События</a>
            <a href="{{ route('admin.artists.create') }}" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-black uppercase tracking-wider transition">Добавить</a>
        </div>
    </div>

    <div class="border-2 border-white/10 bg-[#0a0a0a] overflow-hidden">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-black/50 text-zinc-600 uppercase tracking-wider text-xs font-black">
                <tr>
                    <th class="px-6 py-4">Фото</th>
                    <th class="px-6 py-4">Имя</th>
                    <th class="px-6 py-4">Slug</th>
                    <th class="px-6 py-4 text-right">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($artists as $artist)
                    <tr class="border-t border-white/10 hover:bg-violet-500/[0.03] transition-colors">
                        <td class="px-6 py-4">
                            @if($artist->photo)
                                @if($artist->photo_is_video)
                                    <video src="{{ $artist->photo_src }}" autoplay loop muted playsinline class="w-14 h-14 object-cover border border-white/10 bg-black"></video>
                                @else
                                    <img src="{{ $artist->photo_src }}" alt="" class="w-14 h-14 object-cover border border-white/10">
                                @endif
                            @else
                                <div class="w-14 h-14 bg-zinc-900 border border-white/10 flex items-center justify-center text-zinc-700 text-lg font-black">{{ mb_substr($artist->name, 0, 1) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-white">{{ $artist->name }}</td>
                        <td class="px-6 py-4 text-zinc-500 text-xs font-mono">{{ $artist->slug }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.artists.edit', $artist) }}" class="text-xs text-violet-400 hover:text-violet-300 font-bold">Изменить</a>
                                <form method="POST" action="{{ route('admin.artists.destroy', $artist) }}" onsubmit="return confirm('Удалить?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 font-bold">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-zinc-600">Исполнителей пока нет</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
