import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Link, router } from '@inertiajs/react';

export default function Index({ artists }) {
    const deleteArtist = (id) => {
        if (confirm('Удалить?')) {
            router.delete(route('admin.artists.destroy', id));
        }
    };

    return (
        <AppLayout title="Исполнители">
            <div className="flex items-center justify-between mb-8">
                <div>
                    <p className="text-xs uppercase text-zinc-600 tracking-[0.2em] font-black">Админ</p>
                    <h1 className="hero-giant text-white text-3xl mt-1">Исполнители</h1>
                </div>
                <div className="flex gap-3">
                    <Link href={route('admin.events.index')} className="px-4 py-2 border border-white/10 text-xs font-bold uppercase tracking-wider text-zinc-400 hover:border-violet-500/30 hover:text-white transition">← События</Link>
                    <Link href={route('admin.artists.create')} className="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-black uppercase tracking-wider transition">Добавить</Link>
                </div>
            </div>

            <div className="border-2 border-white/10 bg-[#0a0a0a] overflow-hidden">
                <table className="min-w-full text-left text-sm">
                    <thead className="bg-black/50 text-zinc-600 uppercase tracking-wider text-xs font-black">
                        <tr>
                            <th className="px-6 py-4">Фото</th>
                            <th className="px-6 py-4">Имя</th>
                            <th className="px-6 py-4">Slug</th>
                            <th className="px-6 py-4 text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        {artists.data.length > 0 ? artists.data.map((artist) => (
                            <tr key={artist.id} className="border-t border-white/10 hover:bg-violet-500/[0.03] transition-colors">
                                <td className="px-6 py-4">
                                    {artist.photo_src ? (
                                        artist.photo_is_video ? (
                                            <video src={artist.photo_src} autoPlay loop muted playsInline className="w-14 h-14 object-cover border border-white/10 bg-black"></video>
                                        ) : (
                                            <img src={artist.photo_src} alt="" className="w-14 h-14 object-cover border border-white/10" />
                                        )
                                    ) : (
                                        <div className="w-14 h-14 bg-zinc-900 border border-white/10 flex items-center justify-center text-zinc-700 text-lg font-black">{artist.name.substring(0, 1)}</div>
                                    )}
                                </td>
                                <td className="px-6 py-4 font-bold text-white">{artist.name}</td>
                                <td className="px-6 py-4 text-zinc-500 text-xs font-mono">{artist.slug}</td>
                                <td className="px-6 py-4 text-right">
                                    <div className="flex justify-end gap-2">
                                        <Link href={route('admin.artists.edit', artist.id)} className="text-xs text-violet-400 hover:text-violet-300 font-bold">Изменить</Link>
                                        <button onClick={() => deleteArtist(artist.id)} className="text-xs text-rose-400 hover:text-rose-300 font-bold">Удалить</button>
                                    </div>
                                </td>
                            </tr>
                        )) : (
                            <tr>
                                <td colSpan="4" className="px-6 py-8 text-center text-zinc-600">Исполнителей пока нет</td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* Pagination */}
            {artists.links && artists.links.length > 3 && (
                <div className="mt-6 flex justify-center gap-1">
                    {artists.links.map((link, i) => (
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
