import React, { useRef } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Account({ user }) {
    const fileInput = useRef();
    const { data, setData, post, processing, errors } = useForm({
        name: user.name || '',
        email: user.email || '',
        password: '',
        password_confirmation: '',
        avatar: null,
        notify_email: user.notify_email !== false,
        notify_push: user.notify_push === true,
        _method: 'PUT',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('cabinet.account.update'), {
            forceFormData: true,
        });
    };

    return (
        <AppLayout>
            <Head title="Аккаунт" />
            
            <h1 className="hero-giant text-white text-3xl sm:text-4xl mb-8">Аккаунт</h1>

            <div className="max-w-xl">
                <form onSubmit={submit} className="border-2 border-white/10 bg-[#0a0a0a] p-6 space-y-6">
                    <div>
                        <label className="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Фото</label>
                        <div className="flex items-center gap-4">
                            {user.avatar ? (
                                user.avatar_is_video ? (
                                    <video src={user.avatar_src} autoPlay loop muted playsInline className="w-20 h-20 rounded-full object-cover border-2 border-violet-500/30"></video>
                                ) : (
                                    <img src={user.avatar_src} alt="" className="w-20 h-20 rounded-full object-cover border-2 border-violet-500/30" />
                                )
                            ) : (
                                <div className="w-20 h-20 rounded-full bg-violet-600/20 border-2 border-violet-500/30 flex items-center justify-center text-2xl text-violet-400 font-black">
                                    {(user.name || '').substring(0, 1)}
                                </div>
                            )}
                            <label className="cursor-pointer">
                                <span className="inline-flex items-center gap-2 px-4 py-2 bg-white/[0.04] border border-white/10 text-sm text-zinc-400 hover:border-violet-500/30 hover:text-white transition-colors font-bold">
                                    Выбрать файл
                                </span>
                                <input 
                                    type="file" 
                                    ref={fileInput}
                                    onChange={e => setData('avatar', e.target.files[0])}
                                    accept="image/png,image/jpeg,image/gif,image/webp,video/mp4,video/webm,video/quicktime" 
                                    className="hidden" 
                                />
                            </label>
                        </div>
                        <p className="text-xs text-zinc-600 mt-1">JPG, PNG, GIF, WEBP, MP4, WEBM или MOV, до 50 МБ</p>
                        {errors.avatar && <div className="text-sm text-red-400 mt-1">{errors.avatar}</div>}
                    </div>

                    <label className="block">
                        <span className="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Имя</span>
                        <input 
                            type="text" 
                            value={data.name}
                            onChange={e => setData('name', e.target.value)}
                            required
                            className="w-full bg-black border border-white/10 px-4 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                        />
                        {errors.name && <div className="text-sm text-red-400 mt-1">{errors.name}</div>}
                    </label>

                    <label className="block">
                        <span className="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Email</span>
                        <input 
                            type="email" 
                            value={data.email}
                            onChange={e => setData('email', e.target.value)}
                            required
                            className="w-full bg-black border border-white/10 px-4 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                        />
                        {errors.email && <div className="text-sm text-red-400 mt-1">{errors.email}</div>}
                    </label>

                    <div className="border-t border-white/10 pt-6">
                        <h3 className="text-xs font-black text-zinc-500 mb-3 uppercase tracking-wider">Уведомления</h3>
                        <label className="flex items-center gap-3 mb-2 cursor-pointer">
                            <input type="checkbox" checked={data.notify_email} onChange={e => setData('notify_email', e.target.checked)} className="rounded border-white/20 bg-black text-violet-500 focus:ring-violet-500" />
                            <span className="text-sm text-zinc-400">Уведомления по email (новые концерты, напоминания)</span>
                        </label>
                        <label className="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" checked={data.notify_push} onChange={e => setData('notify_push', e.target.checked)} className="rounded border-white/20 bg-black text-violet-500 focus:ring-violet-500" />
                            <span className="text-sm text-zinc-400">Push-уведомления (при расширении интеграции)</span>
                        </label>
                    </div>

                    <div className="border-t border-white/10 pt-6">
                        <p className="text-xs text-zinc-600 mb-3 font-bold">Оставьте пустым, если не хотите менять пароль.</p>
                        <label className="block mb-4">
                            <span className="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Новый пароль</span>
                            <input 
                                type="password" 
                                value={data.password}
                                onChange={e => setData('password', e.target.value)}
                                autoComplete="new-password" 
                                placeholder="••••••••"
                                className="w-full bg-black border border-white/10 px-4 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                            />
                            {errors.password && <div className="text-sm text-red-400 mt-1">{errors.password}</div>}
                        </label>
                        <label className="block">
                            <span className="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Подтверждение</span>
                            <input 
                                type="password" 
                                value={data.password_confirmation}
                                onChange={e => setData('password_confirmation', e.target.value)}
                                autoComplete="new-password" 
                                placeholder="••••••••"
                                className="w-full bg-black border border-white/10 px-4 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                            />
                        </label>
                    </div>

                    <div className="flex gap-3">
                        <button 
                            type="submit" 
                            disabled={processing}
                            className="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-black uppercase tracking-wider text-sm transition-colors disabled:opacity-50"
                        >
                            Сохранить
                        </button>
                        <Link href={route('cabinet.index')} className="px-6 py-2.5 border border-white/10 text-zinc-400 hover:text-white hover:border-white/20 transition-colors text-sm font-bold">Отмена</Link>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
