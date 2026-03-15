import React, { useRef } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function ArtistProfile({ artist, stats }) {
    const { data, setData, post, processing, errors } = useForm({
        name: artist.name || '',
        slug: artist.slug || '',
        description: artist.description || '',
        photo: null,
        links_json: artist.links ? JSON.stringify(artist.links, null, 2) : '',
        _method: 'PUT',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('cabinet.artist.update'), { forceFormData: true });
    };

    return (
        <AppLayout>
            <Head title="Профиль артиста" />

            <h1 className="hero-giant text-white text-3xl sm:text-4xl mb-6">Профиль артиста</h1>
            <p className="text-zinc-500 text-sm mb-8">Управление своим профилем и просмотр статистики мероприятий (ТЗ: артист/представитель).</p>

            <div className="grid gap-8 lg:grid-cols-[1fr,320px]">
                <form onSubmit={submit} className="border-2 border-white/10 bg-[#0a0a0a] p-6 space-y-6">
                    {Object.keys(errors).length > 0 && (
                        <div className="rounded-lg border border-rose-500/40 bg-rose-500/10 text-sm text-rose-100 px-4 py-3">
                            {Object.values(errors).map((err, i) => <div key={i}>{err}</div>)}
                        </div>
                    )}

                    <div className="flex items-center gap-4">
                        {artist.photo_src ? (
                            artist.photo_is_video ? (
                                <video src={artist.photo_src} className="w-24 h-24 rounded-full object-cover border-2 border-violet-500/30" muted loop playsInline />
                            ) : (
                                <img src={artist.photo_src} alt="" className="w-24 h-24 rounded-full object-cover border-2 border-violet-500/30" />
                            )
                        ) : (
                            <div className="w-24 h-24 rounded-full bg-violet-600/20 border-2 border-violet-500/30 flex items-center justify-center text-2xl text-violet-400">♪</div>
                        )}
                        <label className="cursor-pointer">
                            <span className="inline-flex items-center gap-2 px-4 py-2 bg-white/[0.04] border border-white/10 text-sm text-zinc-400 hover:border-violet-500/30 hover:text-white transition-colors font-bold">
                                Загрузить фото
                            </span>
                            <input type="file" onChange={e => setData('photo', e.target.files[0])} accept="image/*,video/mp4,video/webm,video/quicktime" className="hidden" />
                        </label>
                    </div>

                    <label className="block">
                        <span className="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Имя</span>
                        <input type="text" value={data.name} onChange={e => setData('name', e.target.value)} required
                            className="w-full bg-black border border-white/10 px-4 py-2.5 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                    </label>
                    <label className="block">
                        <span className="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Slug (URL)</span>
                        <input type="text" value={data.slug} onChange={e => setData('slug', e.target.value)}
                            className="w-full bg-black border border-white/10 px-4 py-2.5 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                    </label>
                    <label className="block">
                        <span className="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">Описание / биография</span>
                        <textarea value={data.description} onChange={e => setData('description', e.target.value)} rows={5}
                            className="w-full bg-black border border-white/10 px-4 py-2.5 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                    </label>
                    <label className="block">
                        <span className="block text-xs font-black text-zinc-500 mb-2 uppercase tracking-wider">{'Ссылки (JSON: {"vk": "url", "spotify": "url"})'}</span>
                        <textarea value={data.links_json} onChange={e => setData('links_json', e.target.value)} rows={3}
                            className="w-full bg-black border border-white/10 px-4 py-2.5 text-white font-mono text-sm focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                    </label>
                    <div className="flex gap-3">
                        <button type="submit" disabled={processing}
                            className="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-black uppercase tracking-wider text-sm transition-colors disabled:opacity-50">
                            Сохранить
                        </button>
                        <Link href={route('artists.show', artist)} className="px-6 py-2.5 border border-white/10 text-zinc-400 hover:text-white text-sm font-bold">
                            Открыть профиль
                        </Link>
                    </div>
                </form>

                <div className="space-y-4">
                    <div className="border-2 border-white/10 bg-[#0a0a0a] p-6">
                        <h2 className="text-sm font-black text-white uppercase tracking-wider mb-4">Статистика мероприятий</h2>
                        <ul className="space-y-3 text-sm">
                            <li className="flex justify-between text-zinc-400">
                                <span>Всего мероприятий</span>
                                <span className="font-bold text-white">{stats.events_count ?? 0}</span>
                            </li>
                            <li className="flex justify-between text-zinc-400">
                                <span>Предстоящих</span>
                                <span className="font-bold text-white">{stats.upcoming_count ?? 0}</span>
                            </li>
                            <li className="flex justify-between text-zinc-400">
                                <span>Просмотров страниц</span>
                                <span className="font-bold text-white">{stats.total_views ?? 0}</span>
                            </li>
                            <li className="flex justify-between text-zinc-400">
                                <span>Проданных билетов</span>
                                <span className="font-bold text-white">{stats.total_bookings ?? 0}</span>
                            </li>
                        </ul>
                    </div>
                    <Link href={route('admin.events.index')} className="block text-center py-3 border-2 border-violet-500/50 text-violet-400 hover:bg-violet-500/10 font-bold text-sm uppercase tracking-wider">
                        Создать мероприятие (админ)
                    </Link>
                </div>
            </div>
        </AppLayout>
    );
}
