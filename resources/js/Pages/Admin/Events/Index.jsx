import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Link, router } from '@inertiajs/react';

export default function Index({ events }) {
    const deleteEvent = (event) => {
        
            router.delete(route('admin.events.destroy', event));
        
    };

    const formatDate = (dateString) => {
        if (!dateString) return '—';
        const date = new Date(dateString);
        return date.toLocaleString('ru-RU', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    return (
        <AppLayout title="События">
            <div className="flex items-center justify-between mb-8">
                <div>
                    <p className="text-xs uppercase text-zinc-600 tracking-[0.2em] font-black">Админ</p>
                    <h1 className="hero-giant text-white text-3xl mt-1">События</h1>
                </div>
                <div className="flex gap-3">
                    <Link href={route('admin.artists.index')} className="px-4 py-2 border border-white/10 text-xs font-bold uppercase tracking-wider text-zinc-400 hover:border-violet-500/30 hover:text-white transition">Исполнители</Link>
                    <Link href={route('admin.venues.index')} className="px-4 py-2 border border-white/10 text-xs font-bold uppercase tracking-wider text-zinc-400 hover:border-violet-500/30 hover:text-white transition">Площадки</Link>
                    <Link href={route('admin.events.create')} className="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-black uppercase tracking-wider transition">Создать</Link>
                </div>
            </div>

            <div className="border-2 border-white/10 bg-[#0a0a0a] overflow-hidden">
                <table className="min-w-full text-left text-sm">
                    <thead className="bg-black/50 text-zinc-600 uppercase tracking-wider text-xs font-black">
                        <tr>
                            <th className="px-6 py-4">Название</th>
                            <th className="px-6 py-4">Площадка</th>
                            <th className="px-6 py-4">Дата</th>
                            <th className="px-6 py-4">Статус</th>
                            <th className="px-6 py-4 text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        {events.data.length > 0 ? events.data.map((event) => (
                            <tr key={event.id} className="border-t border-white/10 hover:bg-violet-500/[0.03] transition-colors">
                                <td className="px-6 py-4">
                                    <div className="flex items-center gap-3">
                                        {event.poster_src && (
                                            event.poster_is_video ? (
                                                <video src={event.poster_src} autoPlay loop muted playsInline className="w-12 h-12 object-cover border border-white/10 bg-black"></video>
                                            ) : (
                                                <img src={event.poster_src} alt="" className="w-12 h-12 object-cover border border-white/10" />
                                            )
                                        )}
                                        <div>
                                            <div className="font-bold text-white">{event.title}</div>
                                            <div className="text-xs text-zinc-600">{event.subtitle}</div>
                                        </div>
                                    </div>
                                </td>
                                <td className="px-6 py-4 text-zinc-400">{event.venue?.name || '—'}</td>
                                <td className="px-6 py-4 text-zinc-400">{formatDate(event.start_at)}</td>
                                <td className="px-6 py-4">
                                    <span className={`inline-flex px-2 py-1 text-xs font-black uppercase tracking-wider border ${
                                        event.status === 'published' 
                                            ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/20' 
                                            : event.status === 'archived'
                                            ? 'bg-zinc-500/15 text-zinc-400 border-zinc-500/20'
                                            : 'bg-amber-500/15 text-amber-200 border-amber-500/20'
                                    }`}>
                                        {event.status === 'draft' ? 'Черновик' : event.status === 'published' ? 'Опубликовано' : event.status === 'archived' ? 'Архив' : event.status}
                                    </span>
                                </td>
                                <td className="px-6 py-4 text-right">
                                    <div className="flex justify-end gap-2">
                                        <a href={route('events.show', event)} className="text-xs text-zinc-500 hover:text-white transition font-bold" target="_blank">Просмотр</a>
                                        <Link href={route('admin.events.edit', event)} className="text-xs text-violet-400 hover:text-violet-300 transition font-bold">Изменить</Link>
                                        <button onClick={() => deleteEvent(event)} className="text-xs text-rose-400 hover:text-rose-300 transition font-bold">Удалить</button>
                                    </div>
                                </td>
                            </tr>
                        )) : (
                            <tr>
                                <td colSpan="5" className="px-6 py-8 text-center text-zinc-600">Событий пока нет</td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* Pagination */}
            {events.links && events.links.length > 3 && (
                <div className="mt-6 flex justify-center gap-1">
                    {events.links.map((link, i) => (
                        <Link
                            key={i}
                            href={link.url}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                            className={`px-3 py-1 text-xs border transition ${
                                link.active 
                                    ? 'bg-violet-600 border-violet-600 text-white' 
                                    : link.url 
                                        ? 'border-white/10 text-zinc-400 hover:border-white/20' 
                                        : 'border-white/5 text-zinc-700 pointer-events-none'
                            }`}
                        />
                    ))}
                </div>
            )}
        </AppLayout>
    );
}
