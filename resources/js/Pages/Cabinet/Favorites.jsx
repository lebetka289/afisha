import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link } from '@inertiajs/react';

export default function Favorites({ events }) {
    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('ru-RU', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(date).replace(',', '');
    };

    return (
        <AppLayout>
            <Head title="Избранное" />
            
            <h1 className="hero-giant text-white text-3xl sm:text-4xl mb-8">Избранное <span className="sparkle text-xl">✦</span></h1>

            {events.length === 0 ? (
                <div className="border-2 border-white/10 bg-[#0a0a0a] p-10 text-center">
                    <p className="text-zinc-600">Вы пока не добавили ни одного мероприятия в избранное.</p>
                    <Link href={route('events.index')} className="inline-block mt-3 text-violet-400 hover:text-violet-300 font-bold text-sm uppercase">Перейти к афише →</Link>
                </div>
            ) : (
                <ul className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    {events.map(event => (
                        <li key={event.id} className="border-2 border-white/10 bg-[#0a0a0a] overflow-hidden hover:border-violet-500/30 transition-colors group">
                            <Link href={route('events.show', event.id)} className="block">
                                {event.poster_src && (
                                    <div className="aspect-[16/10] bg-black overflow-hidden">
                                        {event.poster_is_video ? (
                                            <video src={event.poster_src} autoPlay loop muted playsInline className="w-full h-full object-cover group-hover:scale-105 transition duration-500 grayscale-[20%] group-hover:grayscale-0"></video>
                                        ) : (
                                            <img src={event.poster_src} alt="" className="w-full h-full object-cover group-hover:scale-105 transition duration-500 grayscale-[20%] group-hover:grayscale-0" />
                                        )}
                                    </div>
                                )}
                                <div className="p-4">
                                    <p className="text-xs text-zinc-600 font-bold uppercase tracking-wider">{formatDate(event.start_at)}</p>
                                    <p className="font-black text-white mt-1 group-hover:text-violet-400 transition-colors">{event.title}</p>
                                    <p className="text-sm text-zinc-600">{event.venue?.name}</p>
                                </div>
                            </Link>
                        </li>
                    ))}
                </ul>
            )}
        </AppLayout>
    );
}
