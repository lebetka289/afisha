import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

const albumTypeLabels = { album: 'Альбом', single: 'Сингл', ep: 'EP' };

export default function Show({ artist, upcomingEvents, isFavorited: initialFavorited }) {
    const [isFavorited, setIsFavorited] = useState(!!initialFavorited);
    const hasLinks = artist.links && Object.keys(artist.links).length > 0;

    const toggleFavorite = async () => {
        try {
            const res = await fetch(route('artists.favorite.toggle', artist), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();
            if (data.ok) setIsFavorited(!!data.favorited);
        } catch (err) {}
    };

    return (
        <AppLayout>
            <Head title={artist.name} />

            <div className="relative -mx-4 lg:-mx-8 overflow-hidden border-b-2 border-white/10">
                <div className="h-[200px] sm:h-[320px] lg:h-[400px] bg-gradient-to-br from-violet-900/40 to-black">
                    {artist.photo_src ? (
                        artist.photo_is_video ? (
                            <video src={artist.photo_src} autoPlay loop muted playsInline className="w-full h-full object-cover opacity-80" />
                        ) : (
                            <img src={artist.photo_src} alt={artist.name} className="w-full h-full object-cover opacity-90" />
                        )
                    ) : (
                        <div className="w-full h-full flex items-center justify-center text-8xl text-violet-500/30">♪</div>
                    )}
                </div>
                <div className="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent pointer-events-none" />
                <div className="absolute bottom-0 left-0 right-0 px-4 lg:px-8 py-6 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 className="hero-giant text-white text-3xl sm:text-5xl">{artist.name}</h1>
                        <p className="text-zinc-400 text-sm mt-1">Артист · OnTheRise</p>
                    </div>
                    {initialFavorited !== null && initialFavorited !== undefined && (
                        <button
                            type="button"
                            onClick={toggleFavorite}
                            className={`flex items-center gap-2 px-5 py-2.5 border-2 font-black text-sm uppercase tracking-wider transition ${
                                isFavorited ? 'bg-rose-500 border-rose-500 text-white' : 'border-white/40 text-white hover:bg-rose-500/20 hover:border-rose-500'
                            }`}
                        >
                            ♥ {isFavorited ? 'В избранном' : 'В избранное'}
                        </button>
                    )}
                </div>
            </div>

            <div className="grid gap-8 lg:grid-cols-[1fr,320px] mt-8">
                <div className="space-y-8">
                    {artist.description && (
                        <div className="border-2 border-white/10 bg-[#0a0a0a] p-6">
                            <h2 className="text-lg font-black text-white uppercase tracking-wider mb-3">О артисте</h2>
                            <div className="text-zinc-400 text-sm whitespace-pre-line leading-relaxed">
                                {artist.description}
                            </div>
                        </div>
                    )}

                    {artist.albums && artist.albums.length > 0 && (
                        <div className="border-2 border-white/10 bg-[#0a0a0a] p-6">
                            <h2 className="text-lg font-black text-white uppercase tracking-wider mb-4">Дискография</h2>
                            <div className="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                {artist.albums.map((album) => (
                                    <div key={album.id} className="group">
                                        <div className="aspect-square rounded-lg overflow-hidden bg-zinc-900 border border-white/10">
                                            {album.cover_url ? (
                                                <img src={album.cover_src || album.cover_url} alt={album.title} className="w-full h-full object-cover" />
                                            ) : (
                                                <div className="w-full h-full flex items-center justify-center text-3xl text-zinc-600">♪</div>
                                            )}
                                        </div>
                                        <p className="font-bold text-white text-sm mt-2 truncate">{album.title}</p>
                                        <p className="text-xs text-zinc-500">
                                            {album.year && `${album.year} · `}{albumTypeLabels[album.type] || album.type}
                                        </p>
                                        {album.link && (
                                            <a href={album.link} target="_blank" rel="noopener noreferrer" className="text-xs text-violet-400 hover:underline mt-1 inline-block">
                                                Слушать →
                                            </a>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {upcomingEvents && upcomingEvents.length > 0 && (
                        <div className="border-2 border-white/10 bg-[#0a0a0a] p-6">
                            <h2 className="text-lg font-black text-white uppercase tracking-wider mb-4">Расписание концертов</h2>
                            <ul className="space-y-3">
                                {upcomingEvents.map((ev) => (
                                    <li key={ev.id}>
                                        <Link
                                            href={route('events.show', ev)}
                                            className="flex flex-wrap items-center justify-between gap-2 py-3 px-4 border border-white/10 hover:border-violet-500/40 hover:bg-violet-500/5 transition-colors"
                                        >
                                            <div>
                                                <p className="font-bold text-white">{ev.title}</p>
                                                <p className="text-xs text-zinc-500">
                                                    {ev.start_at ? new Date(ev.start_at).toLocaleString('ru-RU', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : ''}
                                                    {ev.venue?.name && ` · ${ev.venue.name}`}
                                                </p>
                                            </div>
                                            <span className="text-violet-400 text-xs font-bold uppercase">Билеты</span>
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}
                </div>

                <div className="lg:self-start space-y-6">
                    {hasLinks && (
                        <div className="border-2 border-white/10 bg-[#0a0a0a] p-6">
                            <h2 className="text-sm font-black text-white uppercase tracking-wider mb-3">Соцсети и стриминг</h2>
                            <div className="flex flex-wrap gap-2">
                                {Object.entries(artist.links).map(([key, url]) => (
                                    url ? (
                                        <a
                                            key={key}
                                            href={url}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="px-4 py-2 border border-white/20 text-zinc-300 hover:border-violet-500 hover:text-white hover:bg-violet-500/10 transition-colors text-sm font-bold"
                                        >
                                            {key}
                                        </a>
                                    ) : null
                                ))}
                            </div>
                        </div>
                    )}

                    {upcomingEvents && upcomingEvents.length === 0 && (
                        <div className="border-2 border-white/10 bg-[#0a0a0a] p-6 text-center text-zinc-500 text-sm">
                            Пока нет анонсированных концертов.
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
