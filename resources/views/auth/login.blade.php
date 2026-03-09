@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto">
        <h1 class="text-2xl font-bold text-white mb-6">Вход</h1>
        <div class="gradient-border p-6">
            <form method="post" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-zinc-400 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
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
                <label class="flex items-center gap-2 text-zinc-400">
                    <input type="checkbox" name="remember" class="rounded border-white/20 bg-white/5 text-violet-500 focus:ring-violet-500/50">
                    <span class="text-sm">Запомнить меня</span>
                </label>
                <button type="submit" class="w-full py-2.5 rounded-xl btn-primary font-medium">Войти</button>
            </form>
            <p class="mt-4 text-sm text-zinc-500">
                Нет аккаунта? <a href="{{ route('register') }}" class="text-violet-400 hover:text-violet-300 hover:underline">Зарегистрироваться</a>
            </p>
        </div>
    </div>
@endsection
