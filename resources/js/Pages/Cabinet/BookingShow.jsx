import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function BookingShow({ booking, qrUrl }) {
    const [showRefundConfirm, setShowRefundConfirm] = useState(false);
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

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('ru-RU').format(amount);
    };

    return (
        <AppLayout>
            <Head title={`Билет ${booking.reference}`} />
            
            <div className="max-w-4xl mx-auto space-y-6">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <Link href={route('cabinet.index')} className="text-sm text-violet-400 hover:text-violet-300 font-bold uppercase tracking-wider">← Назад</Link>
                        <h1 className="hero-giant text-white text-2xl mt-2">Билет {booking.reference}</h1>
                        <p className="text-zinc-500 mt-1 text-sm">{booking.event.title} · {formatDate(booking.event.start_at)}</p>
                    </div>
                    {booking.status !== 'refunded' && (
                        showRefundConfirm ? (
                            <span className="flex items-center gap-3 text-sm">
                                <span className="text-zinc-500">Вернуть билет?</span>
                                <button type="button" onClick={() => { router.post(route('cabinet.bookings.refund', booking.id)); setShowRefundConfirm(false); }} className="px-4 py-2 bg-rose-500/20 border border-rose-500/50 text-rose-300 font-bold hover:bg-rose-500/30 transition-colors">Да</button>
                                <button type="button" onClick={() => setShowRefundConfirm(false)} className="px-4 py-2 border border-white/20 text-zinc-400 hover:text-white transition-colors">Отмена</button>
                            </span>
                        ) : (
                            <button type="button" onClick={() => setShowRefundConfirm(true)} className="px-5 py-2.5 bg-rose-500/10 border-2 border-rose-500/30 text-rose-300 hover:bg-rose-500/20 font-black text-xs uppercase tracking-wider transition-colors">Вернуть билет</button>
                        )
                    )}
                </div>

                <div className="grid gap-6 lg:grid-cols-[1.4fr,0.8fr]">
                    <div className="space-y-6">
                        <div className="border-2 border-white/10 bg-[#0a0a0a] p-6">
                            <h2 className="text-lg font-black text-white mb-4 uppercase tracking-wider">О событии</h2>
                            <div className="space-y-2 text-sm text-zinc-400">
                                <p><span className="text-zinc-600 font-bold">Событие:</span> {booking.event.title}</p>
                                <p><span className="text-zinc-600 font-bold">Площадка:</span> {booking.event.venue?.name}</p>
                                <p><span className="text-zinc-600 font-bold">Адрес:</span> {booking.event.venue?.address}</p>
                                <p><span className="text-zinc-600 font-bold">Дата:</span> {formatDate(booking.event.start_at)}</p>
                                <p><span className="text-zinc-600 font-bold">Покупатель:</span> {booking.customer_name} · {booking.customer_email}</p>
                                <p><span className="text-zinc-600 font-bold">Статус:</span> {booking.status === 'refunded' ? 'Возвращён' : 'Активен'}</p>
                            </div>
                        </div>

                        <div className="border-2 border-white/10 bg-[#0a0a0a] p-6">
                            <h2 className="text-lg font-black text-white mb-4 uppercase tracking-wider">Места</h2>
                            <div className="space-y-3">
                                {booking.items.map(item => (
                                    <div key={item.id} className="flex items-center justify-between gap-4 border border-white/10 bg-black/50 p-4">
                                        <div>
                                            <p className="text-white font-bold">{item.seat_label}</p>
                                            <p className="text-sm text-zinc-600">{item.section?.name}</p>
                                        </div>
                                        <div className="text-right text-sm">
                                            <p className="text-zinc-600 text-xs font-bold uppercase">Цена</p>
                                            <p className="text-white font-black">{formatCurrency(item.price)} ₽</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {booking.addons && booking.addons.length > 0 && (
                            <div className="border-2 border-white/10 bg-[#0a0a0a] p-6">
                                <h2 className="text-lg font-black text-white mb-4 uppercase tracking-wider">Дополнения</h2>
                                <div className="space-y-3">
                                    {booking.addons.map(addon => (
                                        <div key={addon.id} className="flex items-center justify-between gap-4 border border-white/10 bg-black/50 p-4">
                                            <div>
                                                <p className="text-white font-bold">{addon.event_addon?.name}</p>
                                                <p className="text-sm text-zinc-600">Кол-во: {addon.quantity}</p>
                                            </div>
                                            <div className="text-right text-sm">
                                                <p className="text-zinc-600 text-xs font-bold uppercase">Сумма</p>
                                                <p className="text-white font-black">{formatCurrency(addon.price * addon.quantity)} ₽</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>

                    <div className="space-y-6">
                        <div className="border-2 border-violet-500/30 bg-[#0a0a0a] p-6 text-center">
                            <h2 className="text-lg font-black text-white mb-4 uppercase tracking-wider">QR-код</h2>
                            <div className="bg-white p-4 inline-block">
                                <img src={qrUrl} alt="QR-код на страницу мероприятия" className="w-52 h-52" />
                            </div>
                            <p className="text-xs text-zinc-600 mt-4 font-bold">Ведёт на страницу мероприятия · Покажите на входе</p>
                        </div>

                        <div className="border-2 border-white/10 bg-[#0a0a0a] p-6">
                            <h2 className="text-lg font-black text-white mb-4 uppercase tracking-wider">Итог</h2>
                            <div className="space-y-2 text-sm text-zinc-400">
                                <p><span className="text-zinc-600 font-bold">Билетов:</span> {booking.tickets_count}</p>
                                <p><span className="text-zinc-600 font-bold">Сумма:</span> {formatCurrency(booking.total_amount)} ₽</p>
                                <p><span className="text-zinc-600 font-bold">Код:</span> {booking.reference}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
