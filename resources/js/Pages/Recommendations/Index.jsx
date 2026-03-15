import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function Index({ events }) {
    const handleVideoHover = (e, play) => {
        const video = e.currentTarget.querySelector('video');
        if (video) (play ? video.play() : video.pause()).catch(() => {});
    };

    return (
        <AppLayout>
            <Head title="Рекомендации" />

            <section className="mb-8">
                <div className="border-2 border-violet-500/50 px-3 py-1.5 font-black text-xs uppercase tracking-wider inline-block mb-4 text-violet-300">
                    Для вас
                </div>
                <h1 className="hero-giant text-white text-3xl sm:text-5xl lg:text-6xl">
                    <span className="block">Рекомендуем</span>
                    <span className="block text-violet-500">по вашим интересам</span>
                </h1>
                <p className="text-zinc-400 mt-4 text-sm max-w-xl">
                    Подборка мероприятий на основе просмотров, избранных артистов и выбранного города.
                </p>
            </section>

            <div className="h-px bg-white/10 my-6" />

            {events.length === 0 ? (
                <div className="border-2 border-white/10 bg-[#0a0a0a] p-12 text-center rounded-xl">
                    <p className="text-zinc-500 mb-4">Пока недостаточно данных для рекомендаций.</p>
                    <p className="text-sm text-zinc-600 mb-6">Смотрите мероприятия, добавляйте артистов в избранное и укажите город — тогда здесь появятся подборки.</p>
                    <Link href={route('events.index')} className="inline-block px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white font-black text-xs uppercase tracking-wider transition-colors">
                        Перейти к мероприятиям
                    </Link>
                </div>
            ) : (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    {events.map((event) => {
                        const minPrice = Math.min(...(event.sections?.map(s => s.price) || [0])) || 0;
                        return (
                            <Link
                                key={event.id}
                                href={route('events.show', event)}
                                className="group block border border-white/10 bg-[#0a0a0a] overflow-hidden hover:border-violet-500/50 transition-colors"
                                onMouseEnter={(e) => handleVideoHover(e, true)}
                                onMouseLeave={(e) => handleVideoHover(e, false)}
                            >
                                <div className="aspect-[3/4] relative overflow-hidden">
                                    {event.poster_is_video ? (
                                        <video src={event.poster_src} loop muted playsInline preload="metadata" className="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                                    ) : event.poster_src ? (
                                        <img src={event.poster_src} alt={event.title} className="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                                    ) : (
                                        <div className="w-full h-full bg-zinc-900 flex items-center justify-center text-zinc-700 text-4xl">🎭</div>
                                    )}
                                    {minPrice > 0 && (
                                        <span className="absolute bottom-2 left-2 px-2 py-0.5 bg-violet-600 text-white text-[10px] font-bold uppercase tracking-wider">
                                            от {minPrice.toLocaleString('ru-RU')} ₽
                                        </span>
                                    )}
                                </div>
                                <div className="p-3">
                                    <p className="text-[10px] text-zinc-600 font-bold uppercase tracking-wider">
                                        {event.start_at ? new Date(event.start_at).toLocaleString('ru-RU', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).replace(',', ' ·') : '—'}
                                    </p>
                                    <h2 className="font-extrabold text-sm text-white mt-0.5 group-hover:text-violet-400 transition-colors line-clamp-2">
                                        {event.title}
                                    </h2>
                                    <p className="text-xs text-zinc-500 mt-0.5 truncate">{event.venue?.name || '—'}</p>
                                    {event.artist && (
                                        <p className="text-[10px] text-violet-400/80 mt-1 truncate">{event.artist.name}</p>
                                    )}
                                </div>
                            </Link>
                        );
                    })}
                </div>
            )}
        </AppLayout>
    );
}
