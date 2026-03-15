import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Link, router } from '@inertiajs/react';

export default function Index({ venues }) {
    const [deletingId, setDeletingId] = useState(null);

    const deleteVenue = (id) => {
        if (deletingId === id) {
            router.delete(route('admin.venues.destroy', id));
            setDeletingId(null);
        } else {
            setDeletingId(id);
        }
    };

    return (
        <AppLayout title="Площадки">
            <div className="flex items-center justify-between mb-8">
                <div>
                    <p className="text-sm uppercase text-slate-500 tracking-[0.3em]">Админ</p>
                    <h1 className="text-3xl font-semibold mt-1">Площадки</h1>
                </div>
                <Link href={route('admin.venues.create')} className="px-4 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-400 transition text-white text-sm font-medium">Новая площадка</Link>
            </div>

            <div className="grid gap-4">
                {venues.data.length > 0 ? venues.data.map((venue) => (
                    <div key={venue.id} className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div className="text-xl font-semibold text-white">{venue.name}</div>
                            <div className="text-sm text-slate-400">{venue.city} · {venue.address}</div>
                            <div className="text-xs uppercase tracking-wide text-slate-500 mt-2">Макс. вместимость: {venue.max_capacity}</div>
                        </div>
                        <div className="flex gap-3 items-center">
                            <Link href={route('admin.venues.edit', venue.id)} className="text-sm text-indigo-400 hover:text-indigo-200 transition">Редактировать</Link>
                            {deletingId === venue.id ? (
                                <>
                                    <span className="text-sm text-slate-500">Удалить?</span>
                                    <button type="button" onClick={() => deleteVenue(venue.id)} className="text-sm text-rose-400 hover:text-rose-200 font-medium">Да</button>
                                    <button type="button" onClick={() => setDeletingId(null)} className="text-sm text-slate-400 hover:text-slate-200">Отмена</button>
                                </>
                            ) : (
                                <button type="button" onClick={() => setDeletingId(venue.id)} className="text-sm text-rose-400 hover:text-rose-200 transition">Удалить</button>
                            )}
                        </div>
                    </div>
                )) : (
                    <div className="rounded-2xl border border-dashed border-slate-800 text-center text-slate-500 py-10">
                        Площадок пока нет
                    </div>
                )}
            </div>

            {/* Pagination */}
            {venues.links && venues.links.length > 3 && (
                <div className="mt-6 flex justify-center gap-1">
                    {venues.links.map((link, i) => (
                        <Link
                            key={i}
                            href={link.url}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                            className={`px-3 py-1 text-xs border ${link.active ? 'bg-violet-600 border-violet-600 text-white' : 'border-white/10 text-zinc-400 hover:border-white/20'}`}
                        />
                    ))}
                </div>
            )}
        </AppLayout>
    );
}
