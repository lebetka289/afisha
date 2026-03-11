@extends('layouts.app')

@section('content')
    <h1 class="hero-giant text-white text-3xl sm:text-4xl mb-8">Аккаунт</h1>

    <div class="max-w-xl">
        <form method="post" action="{{ route('cabinet.account.update') }}" enctype="multipart/form-data" class="border-2 border-white/10 bg-[#0a0a0a] p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Фото</label>
                <div class="flex items-center gap-4">
                    @if($user->avatar)
                        @if($user->avatar_is_video)
                            <video src="{{ $user->avatar_src }}" autoplay loop muted playsinline class="w-20 h-20 rounded-full object-cover border-2 border-violet-500/30"></video>
                        @else
                            <img src="{{ $user->avatar_src }}" alt="" class="w-20 h-20 rounded-full object-cover border-2 border-violet-500/30">
                        @endif
                    @else
                        <div class="w-20 h-20 rounded-full bg-violet-600/20 border-2 border-violet-500/30 flex items-center justify-center text-2xl text-violet-400 font-black">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    <label class="cursor-pointer">
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/[0.04] border border-white/10 text-sm text-zinc-400 hover:border-violet-500/30 hover:text-white transition-colors font-bold">
                            Выбрать файл
                        </span>
                        <input type="file" name="avatar" accept="image/png,image/jpeg,image/gif,image/webp,video/mp4,video/webm,video/quicktime" class="hidden">
                    </label>
                </div>
                <p class="text-xs text-zinc-600 mt-1">JPG, PNG, GIF, WEBP, MP4, WEBM или MOV, до 50 МБ</p>
            </div>

            <label class="block">
                <span class="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Имя</span>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full bg-black border border-white/10 px-4 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30">
                @error('name')<span class="text-sm text-red-400 mt-1">{{ $message }}</span>@enderror
            </label>

            <label class="block">
                <span class="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Email</span>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full bg-black border border-white/10 px-4 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30">
                @error('email')<span class="text-sm text-red-400 mt-1">{{ $message }}</span>@enderror
            </label>

            <div class="border-t border-white/10 pt-6">
                <p class="text-xs text-zinc-600 mb-3 font-bold">Оставьте пустым, если не хотите менять пароль.</p>
                <label class="block mb-4">
                    <span class="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Новый пароль</span>
                    <input type="password" name="password" autocomplete="new-password" placeholder="••••••••"
                           class="w-full bg-black border border-white/10 px-4 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30">
                    @error('password')<span class="text-sm text-red-400 mt-1">{{ $message }}</span>@enderror
                </label>
                <label class="block">
                    <span class="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Подтверждение</span>
                    <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="••••••••"
                           class="w-full bg-black border border-white/10 px-4 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30">
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-black uppercase tracking-wider text-sm transition-colors">Сохранить</button>
                <a href="{{ route('cabinet.index') }}" class="px-6 py-2.5 border border-white/10 text-zinc-400 hover:text-white hover:border-white/20 transition-colors text-sm font-bold">Отмена</a>
            </div>
        </form>
    </div>
@endsection
