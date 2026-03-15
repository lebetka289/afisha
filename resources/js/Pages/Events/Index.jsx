import React, { useState, useRef } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function Index({ events, cities, favoriteArtists = [], rouletteDates, filters }) {
    const [view, setView] = useState('cards');
    const sliderRef = useRef(null);

    const cityLabel = filters.city ? (cities.find(c => c.slug === filters.city)?.name || '') : '';
    const pageTitle = cityLabel ? `Мероприятия в ${cityLabel}` : 'Мероприятия';
    const featured = events.data.slice(0, 2);

    const toggleDate = (date) => {
        let { date_from, date_to } = filters;
        if (!date_from && !date_to) {
            date_from = date;
            date_to = date;
        } else if (date_from && date_to && date_from === date_to && date !== date_from) {
            date_to = date;
            if (date_to < date_from) {
                [date_from, date_to] = [date_to, date_from];
            }
        } else {
            date_from = date;
            date_to = date;
        }
        router.get(route('events.index'), { ...filters, date_from, date_to }, { preserveState: true });
    };

    const clearDates = () => {
        router.get(route('events.index'), { ...filters, date_from: null, date_to: null }, { preserveState: true });
    };

    const formatDate = (ymd) => {
        if (!ymd) return '';
        const [y, m, d] = ymd.split('-').map(Number);
        const months = ['янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];
        return `${d} ${months[m - 1]}`;
    };

    const scrollSlider = (direction) => {
        if (sliderRef.current) {
            const amount = 280;
            sliderRef.current.scrollBy({ left: direction === 'next' ? amount : -amount, behavior: 'smooth' });
        }
    };

    const handleVideoHover = (e, play) => {
        const video = e.currentTarget.querySelector('video');
        if (video) {
            if (play) video.play().catch(() => {});
            else video.pause();
        }
    };

    const currentMonthLabel = filters.date_from 
        ? new Date(filters.date_from).toLocaleString('ru-RU', { month: 'long' }).toUpperCase()
        : 'Выберите даты';

    return (
        <AppLayout>
            <Head title={pageTitle} />

            <section className="relative overflow-hidden mb-8">
                <div className="diagonal-banner bg-white text-black w-[140%] -ml-[20%] rotate-[-2deg] z-10 py-2 mt-2 mb-6 torn-edge-bottom">
                    <div className="marquee-track">
                        <div className="marquee-content text-[11px] font-black tracking-[0.2em]">
                            OnTheRise ✦ КОНЦЕРТЫ ✦ БИЛЕТЫ ✦ АРТИСТЫ ✦ МЕРОПРИЯТИЯ ✦
                            OnTheRise ✦ КОНЦЕРТЫ ✦ БИЛЕТЫ ✦ АРТИСТЫ ✦ МЕРОПРИЯТИЯ ✦
                        </div>
                    </div>
                </div>

                <div className="relative grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_220px] gap-3 mt-6">
                    <div className="checkerboard absolute -top-4 -right-4 w-16 h-16 opacity-50 hidden md:block z-0"></div>

                    {featured.map((feat) => (
                        <Link 
                            key={feat.id} 
                            href={route('events.show', feat)} 
                            className="img-frame aspect-[4/3] sm:aspect-[3/4] lg:aspect-auto lg:h-[280px] block group"
                            onMouseEnter={(e) => handleVideoHover(e, true)}
                            onMouseLeave={(e) => handleVideoHover(e, false)}
                        >
                            {feat.poster_is_video ? (
                                <video src={feat.poster_src} loop muted playsInline preload="metadata" className="w-full h-full object-cover" />
                            ) : feat.poster_src ? (
                                <img src={feat.poster_src} alt={feat.title} className="w-full h-full object-cover" />
                            ) : (
                                <div className="w-full h-full bg-zinc-900 flex items-center justify-center text-zinc-700 text-4xl">🎭</div>
                            )}
                        </Link>
                    ))}

                    <div className="flex flex-col justify-center items-start gap-3 px-2 sm:col-span-2 lg:col-span-1">
                        <div className="border-2 border-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                            Новые события
                        </div>
                        <p className="text-sm text-zinc-400 leading-relaxed">
                            Лучшие концерты, шоу, спектакли и стендап в вашем городе.
                        </p>
                        <a href="#events-list" className="btn-white text-xs">Смотреть все</a>
                        <div className="flex gap-3 mt-1">
                            <span className="sparkle text-lg">✦</span>
                            <span className="sparkle text-xs mt-1">✦</span>
                        </div>
                    </div>
                </div>

                <div className="mt-6 mb-2">
                    <h1 className="hero-giant text-white text-3xl sm:text-5xl lg:text-7xl">
                        <span className="block">Лучшие</span>
                        <span className="block">События <span className="text-violet-500">города</span></span>
                    </h1>
                    <span className="sparkle text-xl inline-block ml-3 -mt-3">✦</span>
                </div>
                <div className="checkerboard w-12 h-12 opacity-40 -ml-2 mt-1"></div>
            </section>

            <div className="h-px bg-white/10 my-4"></div>

            {favoriteArtists.length > 0 && (
                <div className="mb-8 rounded-xl border-2 border-white/10 bg-[#0a0a0a] p-4 sm:p-6">
                    <h2 className="text-base font-black text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span className="text-rose-400">♥</span> Любимые артисты
                    </h2>
                    <div className="flex gap-4 overflow-x-auto pb-2 scrollbar-thin -mx-1">
                        {favoriteArtists.map((artist) => (
                            <Link
                                key={artist.id}
                                href={route('artists.show', artist)}
                                className="shrink-0 w-28 sm:w-36 group block border border-white/10 bg-black/50 overflow-hidden rounded-xl hover:border-violet-500/50 transition-colors"
                            >
                                <div className="aspect-square relative overflow-hidden bg-zinc-900">
                                    {artist.photo_src ? (
                                        artist.photo_is_video ? (
                                            <video src={artist.photo_src} loop muted playsInline preload="metadata" className="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                                        ) : (
                                            <img src={artist.photo_src} alt={artist.name} className="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                                        )
                                    ) : (
                                        <div className="w-full h-full flex items-center justify-center text-3xl text-zinc-600 group-hover:text-violet-500/70">♪</div>
                                    )}
                                    {artist.events_count > 0 && (
                                        <span className="absolute bottom-1 left-1 px-1.5 py-0.5 bg-violet-600 text-white text-[9px] font-bold">
                                            {artist.events_count}
                                        </span>
                                    )}
                                </div>
                                <p className="p-2 text-xs font-bold text-white truncate text-center group-hover:text-violet-400">{artist.name}</p>
                            </Link>
                        ))}
                    </div>
                </div>
            )}

            <div>
                <div className="mb-8 rounded-xl border border-white/10 bg-[#0a0a0a] p-4">
                    <p className="text-xs font-bold uppercase tracking-[0.15em] text-zinc-600 mb-3">{currentMonthLabel}</p>
                    <div className="flex gap-2 overflow-x-auto pb-2 -mx-1 scrollbar-thin">
                        {rouletteDates.map((d) => {
                            const isFrom = filters.date_from === d.date;
                            const isTo = filters.date_to === d.date;
                            const inRange = filters.date_from && filters.date_to && d.date >= filters.date_from && d.date <= filters.date_to;
                            return (
                                <button 
                                    key={d.date}
                                    type="button"
                                    onClick={() => toggleDate(d.date)}
                                    className={`date-chip shrink-0 flex flex-col items-center justify-center w-14 py-2.5 rounded-lg border-2 transition select-none
                                        ${d.is_weekend ? 'text-rose-400' : 'text-zinc-500'}
                                        ${isFrom || isTo || inRange ? 'border-violet-500 bg-violet-500/20 text-white' : 'border-white/10 hover:border-violet-500/40 hover:bg-violet-500/10 hover:text-zinc-300'}`}
                                >
                                    <span className="text-lg font-bold leading-none">{d.day}</span>
                                    <span className="text-xs mt-0.5 opacity-80">{d.weekday}</span>
                                </button>
                            );
                        })}
                    </div>
                    {(filters.date_from || filters.date_to) && (
                        <p className="text-sm text-zinc-500 mt-3">
                            {filters.date_from && filters.date_to && filters.date_from === filters.date_to ? (
                                <>Выбран: <span className="text-zinc-300 font-medium">{formatDate(filters.date_from)}</span></>
                            ) : (
                                <>Диапазон: <span className="text-zinc-300 font-medium">{formatDate(filters.date_from)}</span> — <span className="text-zinc-300 font-medium">{formatDate(filters.date_to)}</span></>
                            )}
                            <button type="button" onClick={clearDates} className="ml-2 text-violet-400 hover:text-violet-300 hover:underline text-xs uppercase">Сбросить</button>
                        </p>
                    )}
                </div>

                {(filters.date_from || filters.date_to || filters.city || filters.q) && (
                    <div className="mb-6">
                        <Link 
                            href={route('events.index')}
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-white/10 text-xs text-zinc-500 hover:bg-white/5 hover:text-zinc-300 uppercase tracking-wider font-bold transition-colors"
                        >
                            × Сбросить фильтры
                        </Link>
                    </div>
                )}

                <div className="flex items-center justify-between gap-4 mb-4" id="events-list">
                    <h2 className="text-base font-bold text-white uppercase tracking-wider">
                        {pageTitle}
                        <span className="sparkle text-sm ml-1">✦</span>
                    </h2>
                    <div className="flex items-center gap-2">
                        <div className="view-toggle flex rounded-lg bg-white/[0.04] border border-white/10 p-0.5 gap-0.5">
                            <button 
                                type="button" 
                                onClick={() => setView('table')}
                                className={`px-2.5 py-1 rounded text-[11px] font-bold uppercase ${view === 'table' ? 'active' : 'text-zinc-500'}`}
                            >Таблица</button>
                            <button 
                                type="button" 
                                onClick={() => setView('cards')}
                                className={`px-2.5 py-1 rounded text-[11px] font-bold uppercase ${view === 'cards' ? 'active' : 'text-zinc-500'}`}
                            >Карточки</button>
                        </div>
                        <div className="hidden sm:flex items-center gap-1">
                            <button type="button" onClick={() => scrollSlider('prev')} className="slider-nav" aria-label="Назад">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M15 18l-6-6 6-6"/></svg>
                            </button>
                            <button type="button" onClick={() => scrollSlider('next')} className="slider-nav" aria-label="Вперёд">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M9 18l6-6-6-6"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {view === 'table' ? (
                    <div className="rounded-xl border border-white/10 bg-[#0a0a0a] overflow-hidden">
                        <div className="overflow-x-auto scrollbar-thin">
                            <table className="events-table">
                                <thead>
                                    <tr>
                                        <th className="poster-cell">Фото</th>
                                        <th>Событие</th>
                                        <th>Дата и время</th>
                                        <th>Площадка</th>
                                        <th className="text-right">Цена от</th>
                                        <th className="w-24"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {events.data.length > 0 ? events.data.map((event) => {
                                        const minPrice = Math.min(...(event.sections?.map(s => s.price) || [0])) || 0;
                                        return (
                                            <tr key={event.id}>
                                                <td className="poster-cell" onMouseEnter={(e) => handleVideoHover(e, true)} onMouseLeave={(e) => handleVideoHover(e, false)}>
                                                    {event.poster_is_video ? (
                                                        <video src={event.poster_src} loop muted playsInline preload="metadata" className="poster-thumb object-cover" />
                                                    ) : event.poster_src ? (
                                                        <img src={event.poster_src} alt="" className="poster-thumb" loading="lazy" />
                                                    ) : null}
                                                </td>
                                                <td>
                                                    <Link href={route('events.show', event)} className="font-bold text-white hover:text-violet-400 transition-colors text-sm">
                                                        {event.title}
                                                    </Link>
                                                    {event.subtitle && <p className="text-xs text-zinc-600 mt-0.5">{event.subtitle}</p>}
                                                </td>
                                                <td className="text-zinc-500 text-xs whitespace-nowrap">
                                                    {event.start_at ? new Date(event.start_at).toLocaleString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', ' ·') : '—'}
                                                </td>
                                                <td className="text-zinc-500 text-xs">{event.venue?.name || '—'}</td>
                                                <td className="text-right text-zinc-300 font-bold whitespace-nowrap text-xs">
                                                    {minPrice > 0 ? `от ${minPrice.toLocaleString('ru-RU')} ₽` : '—'}
                                                </td>
                                                <td>
                                                    <Link href={route('events.show', event)} className="btn-outline-purple text-[11px] py-1 px-2">
                                                        Билеты
                                                    </Link>
                                                </td>
                                            </tr>
                                        );
                                    }) : (
                                        <tr>
                                            <td colSpan="6" className="text-center py-10 text-zinc-600 text-sm">
                                                По выбранным фильтрам событий не найдено.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                ) : (
                    <div ref={sliderRef} className="event-slider">
                        {events.data.length > 0 ? events.data.map((event) => {
                            const minPrice = Math.min(...(event.sections?.map(s => s.price) || [0])) || 0;
                            return (
                                <Link 
                                    key={event.id}
                                    href={route('events.show', event)}
                                    className="event-slider-card group block border border-white/10 bg-[#0a0a0a] overflow-hidden hover:border-violet-500/50"
                                    onMouseEnter={(e) => handleVideoHover(e, true)}
                                    onMouseLeave={(e) => handleVideoHover(e, false)}
                                >
                                    <div className="aspect-[3/4] relative overflow-hidden">
                                        {event.poster_is_video ? (
                                            <video src={event.poster_src} loop muted playsInline preload="metadata" className="w-full h-full object-cover group-hover:scale-105 transition duration-500 grayscale-[20%] group-hover:grayscale-0" />
                                        ) : event.poster_src ? (
                                            <img src={event.poster_src} alt={event.title} className="w-full h-full object-cover group-hover:scale-105 transition duration-500 grayscale-[20%] group-hover:grayscale-0" />
                                        ) : (
                                            <div className="w-full h-full bg-zinc-900 flex items-center justify-center text-zinc-800 text-3xl">🎭</div>
                                        )}
                                        <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
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
                                        <h3 className="font-extrabold text-sm text-white mt-0.5 group-hover:text-violet-400 transition-colors leading-tight line-clamp-2">
                                            {event.title}
                                        </h3>
                                        <p className="text-xs text-zinc-500 mt-0.5 truncate">{event.venue?.name || '—'}</p>
                                    </div>
                                </Link>
                            );
                        }) : (
                            <div className="flex-1 text-center py-12 text-zinc-600 border border-white/10 bg-[#0a0a0a] min-w-full">
                                По выбранным фильтрам событий не найдено.
                            </div>
                        )}
                    </div>
                )}

                {events.next_page_url && (
                    <div className="mt-6 text-center">
                        <Link 
                            href={events.next_page_url}
                            className="inline-block px-8 py-3 bg-violet-600 hover:bg-violet-700 text-white font-black text-xs uppercase tracking-wider transition-colors"
                        >
                            Показать ещё
                        </Link>
                    </div>
                )}
            </div>

            <div className="flex justify-end mt-8">
                <div className="checkerboard w-16 h-6 opacity-30"></div>
            </div>
        </AppLayout>
    );
}
