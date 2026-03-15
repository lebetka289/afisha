import React, { useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function Index({ artists }) {
    const { auth } = usePage().props;
    const favoriteIds = auth?.favoriteArtistIds ?? [];
    const [likedIds, setLikedIds] = useState(new Set(favoriteIds));

    const isLiked = (id) => likedIds.has(id);

    const toggleLike = async (e, artist) => {
        e.preventDefault();
        e.stopPropagation();
        if (!auth?.user) return;
        try {
            const res = await fetch(route('artists.favorite.toggle', artist), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });
            const data = await res.json();
            if (data.ok) {
                setLikedIds((prev) => {
                    const next = new Set(prev);
                    if (data.favorited) next.add(artist.id); else next.delete(artist.id);
                    return next;
                });
            }
        } catch (err) {}
    };

    return (
        <AppLayout>
            <Head title="Артисты" />

            <section className="mb-8">
                <div className="border-2 border-white px-3 py-1.5 font-black text-xs uppercase tracking-wider inline-block mb-4">
                    Артисты
                </div>
                <h1 className="hero-giant text-white text-3xl sm:text-5xl lg:text-6xl">
                    <span className="block">Исполнители</span>
                    <span className="block text-violet-500">OnTheRise</span>
                </h1>
                <p className="text-zinc-400 mt-4 text-sm max-w-xl">
                    Познакомьтесь с артистами хип-хоп индустрии. Биографии, дискографии и расписание концертов.
                </p>
            </section>

            <div className="h-px bg-white/10 my-6"></div>

            <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4">
                {artists.data.map((artist) => (
                    <Link
                        key={artist.id}
                        href={route('artists.show', artist)}
                        className="group block border border-white/10 bg-[#0a0a0a] overflow-hidden hover:border-violet-500/50 transition-colors relative"
                    >
                        <div className="aspect-square relative overflow-hidden bg-zinc-900">
                            {artist.photo_src ? (
                                artist.photo_is_video ? (
                                    <video src={artist.photo_src} loop muted playsInline preload="metadata" className="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                                ) : (
                                    <img src={artist.photo_src} alt={artist.name} className="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                                )
                            ) : (
                                <div className="w-full h-full flex items-center justify-center text-4xl text-zinc-600 group-hover:text-violet-500/70 transition-colors">
                                    ♪
                                </div>
                            )}
                            <div className="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                            {auth?.user && (
                                <button
                                    type="button"
                                    onClick={(e) => toggleLike(e, artist)}
                                    className={`absolute top-2 right-2 w-9 h-9 rounded-full border-2 flex items-center justify-center text-lg transition z-10 ${
                                        isLiked(artist.id) ? 'bg-rose-500 border-rose-500 text-white' : 'border-white/40 bg-black/40 text-white hover:bg-rose-500/20 hover:border-rose-500'
                                    }`}
                                    aria-label={isLiked(artist.id) ? 'Убрать из избранного' : 'В избранное'}
                                >
                                    ♥
                                </button>
                            )}
                            {artist.events_count > 0 && (
                                <span className="absolute bottom-2 left-2 px-2 py-0.5 bg-violet-600 text-white text-[10px] font-bold uppercase">
                                    {artist.events_count} {artist.events_count === 1 ? 'концерт' : 'концерта'}
                                </span>
                            )}
                        </div>
                        <div className="p-3">
                            <h2 className="font-bold text-white group-hover:text-violet-400 transition-colors truncate">
                                {artist.name}
                            </h2>
                            {artist.description && (
                                <p className="text-xs text-zinc-500 mt-0.5 line-clamp-2">{artist.description}</p>
                            )}
                        </div>
                    </Link>
                ))}
            </div>

            {artists.next_page_url && (
                <div className="mt-8 text-center">
                    <Link
                        href={artists.next_page_url}
                        className="inline-block px-8 py-3 bg-violet-600 hover:bg-violet-700 text-white font-black text-xs uppercase tracking-wider transition-colors"
                    >
                        Показать ещё
                    </Link>
                </div>
            )}
        </AppLayout>
    );
}
