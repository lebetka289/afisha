import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link, useForm } from '@inertiajs/react';

function PasswordStrength({ password }) {
    const checks = [
        { label: 'От 8 символов', ok: password.length >= 8 },
        { label: 'Не более 30', ok: password.length > 0 && password.length <= 30 },
        { label: 'Буквы', ok: /[a-zA-Zа-яА-Я]/.test(password) },
        { label: 'Цифры', ok: /[0-9]/.test(password) },
        { label: 'Спецсимволы (!@#$...)', ok: /[@$!%*?&#^()_\-+=\[\]{}|\\:;"'<>,.\/~`]/.test(password) },
    ];
    const passed = checks.filter(c => c.ok).length;
    const color = passed <= 2 ? 'bg-rose-500' : passed <= 3 ? 'bg-amber-500' : passed <= 4 ? 'bg-yellow-400' : 'bg-emerald-500';
    if (!password) return null;
    return (
        <div className="mt-2 space-y-1.5">
            <div className="flex gap-1">
                {[1,2,3,4,5].map(i => (
                    <div key={i} className={`h-1 flex-1 rounded ${i <= passed ? color : 'bg-zinc-800'}`} />
                ))}
            </div>
            <div className="flex flex-wrap gap-x-3 gap-y-1">
                {checks.map((c, i) => (
                    <span key={i} className={`text-[11px] ${c.ok ? 'text-emerald-400' : 'text-zinc-600'}`}>
                        {c.ok ? '✓' : '○'} {c.label}
                    </span>
                ))}
            </div>
        </div>
    );
}

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });
    const [showPassword, setShowPassword] = useState(false);

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
                            <input type="text" id="name" value={data.name} onChange={e => setData('name', e.target.value)} required autoFocus autoComplete="name" placeholder="Ваше имя"
                                className="w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                            {errors.name && <p className="mt-1 text-sm text-rose-400">{errors.name}</p>}
                        </div>
                        <div>
                            <label htmlFor="email" className="block text-xs font-black text-zinc-500 mb-1 uppercase tracking-wider">Email</label>
                            <input type="email" id="email" value={data.email} onChange={e => setData('email', e.target.value.toLowerCase().trim())} required autoComplete="username" placeholder="you@example.com"
                                className="w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                            {errors.email && <p className="mt-1 text-sm text-rose-400">{errors.email}</p>}
                        </div>
                        <div>
                            <label htmlFor="password" className="block text-xs font-black text-zinc-500 mb-1 uppercase tracking-wider">Пароль</label>
                            <div className="relative">
                                <input type={showPassword ? 'text' : 'password'} id="password" value={data.password}
                                    onChange={e => { if (e.target.value.length <= 30) setData('password', e.target.value); }}
                                    required autoComplete="new-password" placeholder="Мин. 8 символов, буквы + цифры + спецзнаки"
                                    className="w-full bg-black border border-white/10 px-3 py-2.5 pr-12 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                                <button type="button" onClick={() => setShowPassword(!showPassword)}
                                    className="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-zinc-400 text-xs font-bold select-none">
                                    {showPassword ? 'Скрыть' : 'Показать'}
                                </button>
                            </div>
                            <PasswordStrength password={data.password} />
                            {errors.password && <p className="mt-1 text-sm text-rose-400">{errors.password}</p>}
                        </div>
                        <div>
                            <label htmlFor="password_confirmation" className="block text-xs font-black text-zinc-500 mb-1 uppercase tracking-wider">Подтверждение пароля</label>
                            <input type={showPassword ? 'text' : 'password'} id="password_confirmation" value={data.password_confirmation}
                                onChange={e => { if (e.target.value.length <= 30) setData('password_confirmation', e.target.value); }}
                                required autoComplete="new-password" placeholder="Повторите пароль"
                                className="w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                            {data.password_confirmation && data.password !== data.password_confirmation && (
                                <p className="mt-1 text-xs text-amber-400">Пароли не совпадают</p>
                            )}
                        </div>
                        <button type="submit" disabled={processing}
                            className="w-full py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-black uppercase tracking-wider transition-colors disabled:opacity-50">
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
