import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Login() {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });
    const [showPassword, setShowPassword] = useState(false);

    const submit = (e) => {
        e.preventDefault();
        post(route('login'), { onFinish: () => reset('password') });
    };

    return (
        <AppLayout title="Вход">
            <Head title="Вход" />
            <div className="max-w-md mx-auto">
                <h1 className="hero-giant text-white text-3xl mb-6">Вход</h1>
                <div className="border-2 border-white/10 bg-[#0a0a0a] p-6">
                    <form onSubmit={submit} className="space-y-4">
                        <div>
                            <label htmlFor="email" className="block text-xs font-black text-zinc-500 mb-1 uppercase tracking-wider">Email</label>
                            <input type="email" id="email" value={data.email} onChange={e => setData('email', e.target.value.toLowerCase().trim())} required autoFocus autoComplete="username" placeholder="you@example.com"
                                className="w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                            {errors.email && <p className="mt-1 text-sm text-rose-400">{errors.email}</p>}
                        </div>
                        <div>
                            <label htmlFor="password" className="block text-xs font-black text-zinc-500 mb-1 uppercase tracking-wider">Пароль</label>
                            <div className="relative">
                                <input type={showPassword ? 'text' : 'password'} id="password" value={data.password} onChange={e => setData('password', e.target.value)} required autoComplete="current-password" placeholder="Введите пароль"
                                    className="w-full bg-black border border-white/10 px-3 py-2.5 pr-12 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                                <button type="button" onClick={() => setShowPassword(!showPassword)}
                                    className="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-zinc-400 text-xs font-bold select-none">
                                    {showPassword ? 'Скрыть' : 'Показать'}
                                </button>
                            </div>
                            {errors.password && <p className="mt-1 text-sm text-rose-400">{errors.password}</p>}
                        </div>
                        <label className="flex items-center gap-2 text-zinc-500">
                            <input type="checkbox" checked={data.remember} onChange={e => setData('remember', e.target.checked)}
                                className="rounded border-white/20 bg-black text-violet-500 focus:ring-violet-500/50" />
                            <span className="text-sm">Запомнить</span>
                        </label>
                        <button type="submit" disabled={processing}
                            className="w-full py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-black uppercase tracking-wider transition-colors disabled:opacity-50">
                            Войти
                        </button>
                    </form>
                    <p className="mt-4 text-sm text-zinc-600">
                        Нет аккаунта? <Link href={route('register')} className="text-violet-400 hover:text-violet-300 font-bold">Регистрация</Link>
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
