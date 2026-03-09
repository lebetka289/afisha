@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto">
        <h1 class="text-2xl font-bold text-white mb-6">Регистрация</h1>
        <div class="gradient-border p-6">
            <form method="post" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-zinc-400 mb-1">Имя</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                           class="w-full rounded-xl bg-white/[0.06] border border-white/[0.08] px-3 py-2.5 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 text-white placeholder-zinc-500">
                    @error('name')
                        <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-zinc-400 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full rounded-xl bg-white/[0.06] border border-white/[0.08] px-3 py-2.5 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 text-white placeholder-zinc-500">
                    @error('email')
                        <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-zinc-400 mb-1">Пароль</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-xl bg-white/[0.06] border border-white/[0.08] px-3 py-2.5 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 text-white placeholder-zinc-500">
                    @error('password')
                        <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-zinc-400 mb-1">Подтверждение пароля</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full rounded-xl bg-white/[0.06] border border-white/[0.08] px-3 py-2.5 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 text-white placeholder-zinc-500">
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl btn-primary font-medium">Зарегистрироваться</button>
            </form>
            <p class="mt-4 text-sm text-zinc-500">
                Уже есть аккаунт? <a href="{{ route('login') }}" class="text-violet-400 hover:text-violet-300 hover:underline">Войти</a>
            </p>
        </div>
    </div>
@endsection
