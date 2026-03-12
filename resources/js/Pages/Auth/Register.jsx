import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <AppLayout title="Регистрация">
            <Head title="Регистрация" />
            <div className="max-w-md mx-auto">
                <h1 className="hero-giant text-white text-3xl mb-6">Регистрация</h1>
                <div className="border-2 border-white/10 bg-[#0a0a0a] p-6">
                    <form onSubmit={submit} className="space-y-4">
                        <div>
                            <label htmlFor="name" className="block text-xs font-black text-zinc-500 mb-1 uppercase tracking-wider">Имя</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                required
                                autoFocus
                                autoComplete="name"
                                className="w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                            />
                            {errors.name && <p className="mt-1 text-sm text-rose-400">{errors.name}</p>}
                        </div>
                        <div>
                            <label htmlFor="email" className="block text-xs font-black text-zinc-500 mb-1 uppercase tracking-wider">Email</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                required
                                autoComplete="username"
                                className="w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                            />
                            {errors.email && <p className="mt-1 text-sm text-rose-400">{errors.email}</p>}
                        </div>
                        <div>
                            <label htmlFor="password" name="password" className="block text-xs font-black text-zinc-500 mb-1 uppercase tracking-wider">Пароль</label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                required
                                autoComplete="new-password"
                                className="w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                            />
                            {errors.password && <p className="mt-1 text-sm text-rose-400">{errors.password}</p>}
                        </div>
                        <div>
                            <label htmlFor="password_confirmation" className="block text-xs font-black text-zinc-500 mb-1 uppercase tracking-wider">Подтверждение</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                value={data.password_confirmation}
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                required
                                autoComplete="new-password"
                                className="w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                            />
                            {errors.password_confirmation && <p className="mt-1 text-sm text-rose-400">{errors.password_confirmation}</p>}
                        </div>
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-black uppercase tracking-wider transition-colors disabled:opacity-50"
                        >
                            Зарегистрироваться
                        </button>
                    </form>
                    <p className="mt-4 text-sm text-zinc-600">
                        Уже есть аккаунт? <Link href={route('login')} className="text-violet-400 hover:text-violet-300 font-bold">Войти</Link>
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
