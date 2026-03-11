@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto">
        <h1 class="hero-giant text-white text-3xl mb-6">Вход</h1>
        <div class="border-2 border-white/10 bg-[#0a0a0a] p-6">
            <form method="post" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-black text-zinc-500 mb-1 uppercase tracking-wider">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30">
                    @error('email')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="block text-xs font-black text-zinc-500 mb-1 uppercase tracking-wider">Пароль</label>
                    <input type="password" name="password" id="password" required
                           class="w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30">
                    @error('password')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>
                <label class="flex items-center gap-2 text-zinc-500">
                    <input type="checkbox" name="remember" class="border-white/20 bg-black text-violet-500 focus:ring-violet-500/50">
                    <span class="text-sm">Запомнить</span>
                </label>
                <button type="submit" class="w-full py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-black uppercase tracking-wider transition-colors">Войти</button>
            </form>
            <p class="mt-4 text-sm text-zinc-600">
                Нет аккаунта? <a href="{{ route('register') }}" class="text-violet-400 hover:text-violet-300 font-bold">Регистрация</a>
            </p>
        </div>
    </div>
@endsection
