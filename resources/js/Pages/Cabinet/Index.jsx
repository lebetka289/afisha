import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link, useForm, usePage, router } from '@inertiajs/react';

export default function Index({ user, bookings = [], cities = [] }) {
    const [refundingId, setRefundingId] = useState(null);
    const { flash } = usePage().props;
    const { data, setData, post, processing } = useForm({
        city_id: user?.city_id ?? '',
    });

    const submitCity = (e) => {
        e.preventDefault();
        post(route('cabinet.city.update'));
    };

    const formatDate = (dateString) => {
        if (dateString == null) return '—';
        const date = new Date(dateString);
        if (Number.isNaN(date.getTime())) return '—';
        return new Intl.DateTimeFormat('ru-RU', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            weekday: 'short',
            hour: '2-digit',
            minute: '2-digit',
        }).format(date);
    };

    const formatCurrency = (amount) => {
        if (amount == null) return '0';
        return new Intl.NumberFormat('ru-RU').format(amount);
    };

    return (
        <AppLayout>
            <Head title="Личный кабинет" />
            
            <h1 className="hero-giant text-white text-3xl sm:text-4xl mb-8">Личный кабинет</h1>

            <div className="grid gap-8 lg:grid-cols-[1fr,2fr]">
                {/* Settings */}
                <div className="border-2 border-white/10 bg-[#0a0a0a] p-6" id="city">
                    <h2 className="font-black text-white mb-4 uppercase tracking-wider text-sm">Настройки</h2>
                    <p className="text-xs text-zinc-600 mb-2 font-bold uppercase tracking-wider">Город для каталога мероприятий</p>
                    <form onSubmit={submitCity} className="flex gap-2">
                        <select 
                            name="city_id" 
                            value={data.city_id}
                            onChange={e => setData('city_id', e.target.value)}
                            className="flex-1 bg-black border border-white/10 px-3 py-2.5 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 text-sm"
                        >
                            {(cities || []).map(c => (
                                <option key={c.id} value={c.id}>{c.name}</option>
                            ))}
                        </select>
                        <button 
                            type="submit" 
                            disabled={processing}
                            className="px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-xs font-black uppercase tracking-wider transition-colors disabled:opacity-50"
                        >
                            Сохранить
                        </button>
                    </form>
                </div>

                {/* Tickets */}
                <div id="tickets">
                    <div className="flex flex-wrap items-center justify-between gap-4 mb-4">
                        <h2 className="font-black text-lg text-white uppercase tracking-wider">
                            Мои билеты <span className="sparkle text-sm">✦</span>
                        </h2>
                        <span className="px-4 py-2 border border-white/10 text-xs text-zinc-400 font-bold uppercase tracking-wider">Правила возврата</span>
                    </div>
                    
                    {bookings.length === 0 ? (
                        <div className="border-2 border-white/10 bg-[#0a0a0a] p-10 text-center">
                            <p className="text-zinc-600">У вас пока нет купленных билетов.</p>
                            <Link href={route('events.index')} className="inline-block mt-3 text-violet-400 hover:text-violet-300 font-bold text-sm uppercase">Перейти к мероприятиям →</Link>
                        </div>
                    ) : (
                        <ul className="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                            {(bookings || []).map(booking => {
                                const event = booking.event;
                                if (!event) {
                                    return (
                                        <li key={booking.id} className="border-2 border-white/10 bg-[#0a0a0a] p-4">
                                            <p className="text-zinc-500 text-sm">Билет {booking.reference}</p>
                                            <p className="text-zinc-600 text-xs mt-1">Мероприятие недоступно</p>
                                            <Link href={route('cabinet.bookings.show', booking.id)} className="inline-block mt-2 text-xs text-violet-400 hover:text-violet-300 font-bold">Подробнее</Link>
                                        </li>
                                    );
                                }
                                const isPast = new Date(event.start_at) < new Date();
                                return (
                                    <li key={booking.id} className={`border-2 overflow-hidden transition-colors ${isPast ? 'border-white/[0.05] bg-zinc-900/50' : 'border-white/10 bg-[#0a0a0a] hover:border-violet-500/30'}`}>
                                        <div className="text-xs px-4 pt-4 text-zinc-500 font-bold uppercase tracking-wider">
                                            {formatDate(event.start_at)}
                                        </div>
                                        <div className={`aspect-[16/10] mt-3 overflow-hidden ${isPast ? 'grayscale opacity-60' : ''}`}>
                                            {event.poster_is_video ? (
                                                <video src={event.poster_src} autoPlay loop muted playsInline className="w-full h-full object-cover" />
                                            ) : event.poster_src ? (
                                                <img src={event.poster_src} alt={event.title} className="w-full h-full object-cover" />
                                            ) : (
                                                <div className="w-full h-full bg-zinc-900 flex items-center justify-center text-zinc-600 text-2xl">🎭</div>
                                            )}
                                        </div>
                                        <div className="p-4 space-y-2">
                                            <p className={`text-xl font-black ${isPast ? 'text-zinc-400' : 'text-white'}`}>{event.title}</p>
                                            <p className="text-sm text-zinc-500 font-bold">{booking.tickets_count} билет{booking.tickets_count > 1 ? (booking.tickets_count < 5 ? 'а' : 'ов') : ''}</p>
                                            <p className="text-sm text-zinc-600">{event.venue?.name || '—'}</p>
                                            <p className="text-xs text-zinc-600">Код: <strong className="text-zinc-400">{booking.reference}</strong> · {formatCurrency(booking.total_amount)} ₽</p>
                                        </div>
                                        <div className="px-4 pb-4 flex items-center gap-2">
                                            <Link href={route('cabinet.bookings.show', booking.id)} className="px-3 py-1.5 text-xs font-black uppercase tracking-wider bg-violet-600/20 border border-violet-500/30 text-violet-300 hover:bg-violet-600/30 hover:text-white transition-colors">Подробнее</Link>
                                            {booking.status !== 'refunded' && (
                                                refundingId === booking.id ? (
                                                    <span className="flex items-center gap-2 text-xs">
                                                        <span className="text-zinc-500">Вернуть?</span>
                                                        <button type="button" onClick={() => { router.post(route('cabinet.bookings.refund', booking.id)); setRefundingId(null); }} className="text-rose-400 font-bold hover:text-rose-300">Да</button>
                                                        <button type="button" onClick={() => setRefundingId(null)} className="text-zinc-400 hover:text-white">Отмена</button>
                                                    </span>
                                                ) : (
                                                    <button type="button" onClick={() => setRefundingId(booking.id)} className="px-3 py-1.5 text-xs font-black uppercase tracking-wider bg-rose-500/10 border border-rose-500/30 text-rose-300 hover:bg-rose-500/20 hover:text-white transition-colors">Возврат</button>
                                                )
                                            )}
                                        </div>
                                    </li>
                                );
                            })}
                        </ul>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
