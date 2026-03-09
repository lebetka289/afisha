@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-white mb-8">Управление аккаунтом</h1>

    <div class="max-w-xl">
        <form method="post" action="{{ route('cabinet.account.update') }}" enctype="multipart/form-data" class="gradient-border p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">Фото</label>
                <div class="flex items-center gap-4">
                    @if($user->avatar)
                        <img src="{{ route('media.show', ['path' => $user->avatar]) }}" alt="" class="w-20 h-20 rounded-full object-cover border-2 border-white/10">
                    @else
                        <div class="w-20 h-20 rounded-full bg-violet-500/20 border-2 border-violet-500/30 flex items-center justify-center text-2xl text-violet-400 font-semibold">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    <label class="cursor-pointer">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/[0.06] border border-white/[0.08] text-sm text-zinc-300 hover:border-violet-500/30 hover:text-white transition-colors">
                            Выбрать файл
                        </span>
                        <input type="file" name="avatar" accept="image/png,image/jpeg,image/gif,image/webp" class="hidden">
                    </label>
                </div>
                <p class="text-xs text-zinc-500 mt-1">JPG, PNG, GIF или WEBP, не более 5 МБ</p>
            </div>

            <label class="block">
                <span class="block text-sm font-medium text-zinc-300 mb-2">Имя</span>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full rounded-xl bg-white/[0.06] border border-white/[0.08] px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20">
                @error('name')
                    <span class="text-sm text-red-400 mt-1">{{ $message }}</span>
                @enderror
            </label>

            <label class="block">
                <span class="block text-sm font-medium text-zinc-300 mb-2">Email</span>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full rounded-xl bg-white/[0.06] border border-white/[0.08] px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20">
                @error('email')
                    <span class="text-sm text-red-400 mt-1">{{ $message }}</span>
                @enderror
            </label>

            <div class="border-t border-white/[0.08] pt-6">
                <p class="text-sm text-zinc-500 mb-3">Оставьте пустым, если не хотите менять пароль.</p>
                <label class="block mb-4">
                    <span class="block text-sm font-medium text-zinc-300 mb-2">Новый пароль</span>
                    <input type="password" name="password" autocomplete="new-password"
                           class="w-full rounded-xl bg-white/[0.06] border border-white/[0.08] px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20"
                           placeholder="••••••••">
                    @error('password')
                        <span class="text-sm text-red-400 mt-1">{{ $message }}</span>
                    @enderror
                </label>
                <label class="block">
                    <span class="block text-sm font-medium text-zinc-300 mb-2">Подтверждение пароля</span>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                           class="w-full rounded-xl bg-white/[0.06] border border-white/[0.08] px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20"
                           placeholder="••••••••">
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2.5 rounded-xl btn-primary font-medium">Сохранить</button>
                <a href="{{ route('cabinet.index') }}" class="px-5 py-2.5 rounded-xl border border-white/[0.08] text-zinc-300 hover:text-white hover:border-white/20 transition-colors">Отмена</a>
            </div>
        </form>
    </div>
@endsection
