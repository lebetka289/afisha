import React, { useState, useEffect, useRef } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function Show({ event, isFavorited: initialIsFavorited, mapLat, mapLng }) {
    const { auth } = usePage().props;
    const [isFavorited, setIsFavorited] = useState(initialIsFavorited);
    const [hallScale, setHallScale] = useState(1);
    const [selections, setSelections] = useState(new Map());
    const [standingQty, setStandingQty] = useState({}); // sectionId -> qty
    const [bookingStep, setBookingStep] = useState(1);
    const [addonsQty, setAddonsQty] = useState({}); // addonId -> qty

    const { data, setData, post, processing, errors } = useForm({
        tickets_payload: '[]',
        addons_payload: '[]',
        customer_name: auth.user?.name || '',
        customer_email: auth.user?.email || '',
        customer_phone: '',
        test_mode: 0,
    });

    const handleFavoriteToggle = async () => {
        try {
            const response = await fetch(route('events.favorite.toggle', event.id), {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 
                    'Accept': 'application/json', 
                    'Content-Type': 'application/json' 
                },
            });
            const result = await response.json();
            setIsFavorited(!!result.favorited);
        } catch (e) {
            console.error(e);
        }
    };

    const toggleSeat = (sectionId, seat) => {
        if (seat.status !== 'available') return;
        const newSelections = new Map(selections);
        if (newSelections.has(seat.id)) {
            newSelections.delete(seat.id);
        } else {
            newSelections.set(seat.id, {
                section_id: sectionId,
                seat_id: seat.id,
                label: seat.label,
                price: Number(seat.price || event.sections.find(s => s.id === sectionId).price),
            });
        }
        setSelections(newSelections);
    };

    const updateStandingQty = (sectionId, delta) => {
        const current = standingQty[sectionId] || 0;
        const newVal = Math.max(0, current + delta);
        setStandingQty({ ...standingQty, [sectionId]: newVal });
    };

    const updateAddonQty = (addonId, delta) => {
        const current = addonsQty[addonId] || 0;
        const newVal = Math.max(0, current + delta);
        setAddonsQty({ ...addonsQty, [addonId]: newVal });
    };

    const getTicketsTotals = () => {
        let count = 0, sum = 0;
        selections.forEach(item => { count += 1; sum += item.price; });
        Object.entries(standingQty).forEach(([sectionId, qty]) => {
            const section = event.sections.find(s => s.id === Number(sectionId));
            count += qty;
            sum += (section?.price || 0) * qty;
        });
        return { count, sum };
    };

    const getAddonsSum = () => {
        let sum = 0;
        Object.entries(addonsQty).forEach(([addonId, qty]) => {
            const addon = event.addons.find(a => a.id === Number(addonId));
            sum += (addon?.price || 0) * qty;
        });
        return sum;
    };

    useEffect(() => {
        const tickets = [];
        selections.forEach(item => tickets.push({ section_id: item.section_id, seat_id: item.seat_id }));
        Object.entries(standingQty).forEach(([sectionId, qty]) => {
            if (qty > 0) tickets.push({ section_id: Number(sectionId), quantity: qty });
        });
        
        const addons = [];
        Object.entries(addonsQty).forEach(([addonId, qty]) => {
            if (qty > 0) addons.push({ addon_id: Number(addonId), quantity: qty });
        });

        setData({
            ...data,
            tickets_payload: JSON.stringify(tickets),
            addons_payload: JSON.stringify(addons),
        });
    }, [selections, standingQty, addonsQty]);

    const submitBooking = (e, testMode = 0) => {
        if (e) e.preventDefault();
        
        // Use a temporary object to update data and then call post with it
        const finalData = { ...data, test_mode: testMode };
        
        // Since setData is asynchronous, we pass the data directly to post if needed or just use the updated state in a separate effect.
        // But Inertia's post uses the current 'data' state. So we should set it and then post.
        // Or better, use the manual post option.
        
        router.post(route('events.book', event.id), finalData);
    };

    const { count: ticketsCount, sum: ticketsSum } = getTicketsTotals();
    const addonsSum = getAddonsSum();
    const totalSum = ticketsSum + addonsSum;

    return (
        <AppLayout>
            <Head title={event.title} />

            <div className="relative -mx-4 lg:-mx-8 overflow-hidden border-b-2 border-white/10">
                {event.poster_src ? (
                    <div className="h-[200px] sm:h-[320px] lg:h-[400px] bg-black group">
                        {event.poster_is_video ? (
                            <video src={event.poster_src} autoPlay loop muted playsInline preload="metadata" className="w-full h-full object-cover grayscale-[30%] hover:grayscale-0 transition duration-500" />
                        ) : (
                            <img src={event.poster_src} alt={event.title} className="w-full h-full object-cover grayscale-[30%]" />
                        )}
                    </div>
                ) : (
                    <div className="h-[200px] sm:h-[320px] lg:h-[400px] bg-gradient-to-br from-violet-900/40 to-black"></div>
                )}
                <div className="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent pointer-events-none"></div>

                <div className="checkerboard-sm absolute top-4 right-4 w-16 h-16 opacity-40"></div>

                <div className="absolute bottom-0 left-0 right-0">
                    <div className="px-4 lg:px-8 py-5 flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <p className="text-xs uppercase tracking-[0.15em] text-zinc-400 font-bold">
                                {event.start_at ? new Date(event.start_at).toLocaleString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', ' ·') : ''} · {event.venue?.name}
                            </p>
                            <h1 className="hero-giant text-white text-3xl sm:text-5xl mt-1">{event.title}</h1>
                            {event.subtitle && <p className="text-zinc-400 mt-1 text-sm">{event.subtitle}</p>}
                        </div>
                        <div className="flex items-center gap-2 flex-shrink-0">
                            <button onClick={() => document.getElementById('booking-steps')?.scrollIntoView({ behavior: 'smooth' })}
                               className="inline-flex items-center gap-2 px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white font-black text-sm uppercase tracking-wider transition-colors">
                                Купить билеты
                            </button>
                            {auth.user && (
                                <div className="relative group">
                                    <button 
                                        type="button" 
                                        onClick={handleFavoriteToggle}
                                        className={`w-11 h-11 border-2 flex items-center justify-center text-lg transition ${isFavorited ? 'bg-rose-500/20 border-rose-500 text-rose-400' : 'border-white text-white hover:bg-rose-500/20 hover:border-rose-500 hover:text-rose-400'}`}
                                    >
                                        ♥
                                    </button>
                                </div>
                            )}
                            <span className="px-3 py-2 text-xs font-black uppercase tracking-wider bg-emerald-600 text-white">Продажа</span>
                        </div>
                    </div>
                </div>
            </div>

            <div className="h-px bg-white/10 -mx-4 lg:-mx-8 my-6"></div>

            <div className="grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
                <div className="space-y-6">
                    <div className="border-2 border-white/10 bg-[#0a0a0a] p-6 rounded-none">
                        <div className="text-zinc-400 leading-relaxed text-sm whitespace-pre-line">
                            {event.description}
                        </div>
                    </div>

                    <div className="border-2 border-white/10 bg-[#0a0a0a] p-6 space-y-4" id="booking-steps">
                        <h2 className="text-xl font-black text-white uppercase tracking-wider">
                            Выберите места <span className="sparkle text-sm">✦</span>
                        </h2>
                        <div className="flex flex-wrap gap-4 mb-2">
                            {[...new Set(event.sections.map(s => s.price))].map(price => {
                                const section = event.sections.find(s => s.price === price);
                                return (
                                    <div key={price} className="flex items-center gap-2 text-xs text-zinc-500 font-bold uppercase">
                                        <span className="w-3 h-3 rounded-full shrink-0" style={{ background: section.color || '#7C3AED' }}></span>
                                        <span>{price.toLocaleString('ru-RU')} ₽</span>
                                    </div>
                                );
                            })}
                        </div>
                        <div className="relative border-2 border-white/10 bg-black aspect-[16/9] min-h-[360px] overflow-hidden">
                            <div className="absolute right-4 top-4 z-20 flex flex-col gap-2">
                                <button type="button" onClick={() => setHallScale(Math.min(2, hallScale + 0.2))} className="w-10 h-10 bg-violet-600 text-white font-bold text-lg hover:bg-violet-700 transition-colors">+</button>
                                <button type="button" onClick={() => setHallScale(Math.max(0.8, hallScale - 0.2))} className="w-10 h-10 bg-violet-600 text-white font-bold text-lg hover:bg-violet-700 transition-colors">−</button>
                            </div>
                            <div className="absolute inset-0">
                                <div className="absolute inset-0 origin-center transition-transform duration-200" style={{ transform: `scale(${hallScale})` }}>
                                    <div className="absolute inset-4 border border-dashed border-white/10"></div>
                                    <div className="absolute top-4 left-1/2 -translate-x-1/2 px-6 py-2 text-xs tracking-[0.2em] font-black bg-white/10 text-zinc-400 border border-white/10 uppercase">Сцена</div>
                                    {event.sections.map((section) => {
                                        const pos = section.position || { x: 15, y: 20, width: 20, height: 15 };
                                        return (
                                            <div 
                                                key={section.id}
                                                className="absolute border border-white/20 backdrop-blur cursor-pointer transition hover:scale-[1.01]"
                                                style={{
                                                    left: `${pos.x}%`, 
                                                    top: `${pos.y}%`, 
                                                    width: `${pos.width}%`, 
                                                    height: `${pos.height}%`, 
                                                    background: `${section.color || '#7C3AED'}22`, 
                                                    borderColor: section.color || '#7C3AED'
                                                }}
                                                onClick={() => document.getElementById(`section-${section.id}`)?.scrollIntoView({ behavior: 'smooth' })}
                                            >
                                                <div className="absolute inset-0 flex flex-col items-center justify-center text-center px-3 text-xs font-bold text-white">
                                                    <span>{section.name}</span>
                                                    <span className="text-[11px] text-zinc-400 mt-1">{section.seating_mode === 'seated' ? 'Сидячие' : 'Зона'}</span>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="space-y-6">
                        {event.sections.map((section) => (
                            <div key={section.id} className="border-2 border-white/10 bg-[#0a0a0a] p-5 space-y-4" id={`section-${section.id}`}>
                                <div className="flex flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <p className="text-xs uppercase tracking-[0.15em] text-zinc-600 font-bold">{section.type}</p>
                                        <h3 className="text-xl font-black text-white">{section.name}</h3>
                                    </div>
                                    <div className="text-right">
                                        <div className="text-xs text-zinc-600 uppercase font-bold tracking-wider">Стоимость от</div>
                                        <div className="text-xl font-black text-white">{section.price.toLocaleString('ru-RU')} ₽</div>
                                    </div>
                                </div>

                                {section.seating_mode === 'seated' ? (
                                    <div className="border border-white/10 bg-black/50 p-4 overflow-x-auto">
                                        <div className="space-y-2">
                                            {Object.entries(
                                                section.seats.reduce((acc, seat) => {
                                                    acc[seat.row_number] = acc[seat.row_number] || [];
                                                    acc[seat.row_number].push(seat);
                                                    return acc;
                                                }, {})
                                            ).map(([row, seats]) => (
                                                <div key={row} className="flex items-center gap-2">
                                                    <span className="w-10 text-xs text-zinc-600 text-right font-bold">{row}</span>
                                                    <div className="grid gap-2" style={{ gridTemplateColumns: `repeat(${section.cols || 1}, minmax(32px, 1fr))` }}>
                                                        {seats.sort((a,b) => a.col_number - b.col_number).map((seat) => (
                                                            <button 
                                                                key={seat.id}
                                                                type="button"
                                                                disabled={seat.status !== 'available'}
                                                                onClick={() => toggleSeat(section.id, seat)}
                                                                className={`seat-btn text-xs font-bold border py-1 ${
                                                                    seat.status !== 'available' 
                                                                        ? 'border-red-500/50 text-red-500 cursor-not-allowed opacity-50' 
                                                                        : selections.has(seat.id)
                                                                            ? 'bg-violet-500 border-violet-500 text-white'
                                                                            : 'border-white/20 text-white hover:border-violet-500 hover:bg-violet-500/20'
                                                                }`}
                                                            >
                                                                {seat.label}
                                                            </button>
                                                        ))}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                ) : (
                                    <div className="flex flex-wrap items-center gap-4 border border-white/10 bg-black/50 p-4">
                                        <div>
                                            <div className="text-xs uppercase tracking-wider text-zinc-600 font-bold mb-1">Количество</div>
                                            <div className="flex items-center gap-3">
                                                <button type="button" onClick={() => updateStandingQty(section.id, -1)} className="qty-btn w-8 h-8 border border-white/20 text-zinc-400 hover:border-violet-500 text-lg transition-colors font-bold">−</button>
                                                <input type="number" readOnly value={standingQty[section.id] || 0} className="w-16 text-center bg-black border border-white/10 py-1 text-white font-bold" />
                                                <button type="button" onClick={() => updateStandingQty(section.id, 1)} className="qty-btn w-8 h-8 border border-white/20 text-zinc-400 hover:border-violet-500 text-lg transition-colors font-bold">+</button>
                                            </div>
                                        </div>
                                        <div className="text-sm text-zinc-500">
                                            Доступно: {section.capacity} мест · {section.price.toLocaleString('ru-RU')} ₽
                                        </div>
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>

                    <div className="border-2 border-white/10 bg-[#0a0a0a] p-5">
                        <h2 className="text-lg font-black text-white mb-3 uppercase tracking-wider">Где проходит</h2>
                        <p className="text-xs text-zinc-500 mb-3 font-bold uppercase tracking-wider">{event.venue?.name} · {event.venue?.address}</p>
                        <div className="overflow-hidden border border-white/10 aspect-[21/9] bg-black">
                            <iframe src={`https://yandex.ru/map-widget/v1/?ll=${mapLng},${mapLat}&pt=${mapLng},${mapLat}&z=16&l=map`} width="100%" height="100%" frameBorder="0" allowFullScreen style={{ border: 0 }}></iframe>
                        </div>
                    </div>

                    {ticketsCount > 0 && (
                        <div className="border-2 border-violet-500 bg-violet-600/10 p-6">
                            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <h2 className="text-lg font-black text-white uppercase tracking-wider">Покупка билета</h2>
                                    <p className="text-sm text-zinc-400 mt-1">Выберите места и нажмите для оформления.</p>
                                </div>
                                <button onClick={() => { setBookingStep(3); document.getElementById('booking-form')?.scrollIntoView({ behavior: 'smooth' }); }}
                                        className="inline-flex items-center justify-center gap-2 px-8 py-3 bg-violet-600 hover:bg-violet-700 text-white font-black uppercase tracking-wider transition-colors">
                                    Оформить
                                </button>
                            </div>
                        </div>
                    )}
                </div>

                <div className="border-2 border-white/10 bg-[#0a0a0a] p-6 lg:self-start" id="booking-form">
                    <form onSubmit={submitBooking}>
                        {bookingStep === 1 && (
                            <div>
                                <h2 className="text-xl font-black text-white mb-4 uppercase tracking-wider">Выбранные места</h2>
                                <div className="border border-white/10 bg-black/50 p-4 space-y-2">
                                    {ticketsCount === 0 ? (
                                        <div className="text-sm text-zinc-500">Выберите места на схеме слева</div>
                                    ) : (
                                        <>
                                            {[...selections.values()].map(item => (
                                                <div key={item.seat_id} className="flex justify-between text-sm">
                                                    <span>{item.label}</span>
                                                    <span>{item.price.toLocaleString('ru-RU')} ₽</span>
                                                </div>
                                            ))}
                                            {Object.entries(standingQty).map(([sectionId, qty]) => {
                                                if (qty <= 0) return null;
                                                const section = event.sections.find(s => s.id === Number(sectionId));
                                                return (
                                                    <div key={sectionId} className="flex justify-between text-sm">
                                                        <span>{section.name} × {qty}</span>
                                                        <span>{((section.price || 0) * qty).toLocaleString('ru-RU')} ₽</span>
                                                    </div>
                                                );
                                            })}
                                        </>
                                    )}
                                </div>
                                <p className="mt-4 text-xs text-zinc-600 font-bold uppercase">Нажмите «Далее» после выбора.</p>
                            </div>
                        )}

                        {bookingStep === 2 && (
                            <div>
                                <button type="button" onClick={() => setBookingStep(1)} className="text-zinc-500 hover:text-white mb-4 flex items-center gap-1 text-sm font-bold">← Назад</button>
                                <h2 className="text-xl font-black text-white mb-4 uppercase tracking-wider">Добавьте к заказу</h2>
                                <div className="space-y-4">
                                    {event.addons.length > 0 ? event.addons.map((addon) => (
                                        <div key={addon.id} className="flex flex-wrap items-center justify-between gap-3 border border-white/10 bg-black/50 p-4">
                                            <div className="flex-1 min-w-0">
                                                <p className="font-bold text-white">{addon.name}</p>
                                                {addon.description && <p className="text-xs text-zinc-600 mt-0.5">{addon.description}</p>}
                                                <p className="text-sm text-violet-400 mt-1 font-bold">{addon.price.toLocaleString('ru-RU')} ₽</p>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <button type="button" onClick={() => updateAddonQty(addon.id, -1)} className="w-8 h-8 border border-white/20 text-zinc-500 hover:text-white flex items-center justify-center font-bold">−</button>
                                                <input type="number" readOnly value={addonsQty[addon.id] || 0} className="w-12 text-center bg-black border border-white/10 py-1 text-white text-sm font-bold" />
                                                <button type="button" onClick={() => updateAddonQty(addon.id, 1)} className="w-8 h-8 border border-white/20 text-zinc-500 hover:text-white flex items-center justify-center font-bold">+</button>
                                            </div>
                                        </div>
                                    )) : (
                                        <p className="text-zinc-600 text-sm">Дополнений нет.</p>
                                    )}
                                </div>
                                <div className="mt-4 border border-white/10 bg-black/50 p-3 text-sm text-zinc-400">
                                    {ticketsCount} билет(ов): {ticketsSum.toLocaleString('ru-RU')} ₽
                                </div>
                                <button type="button" onClick={() => setBookingStep(3)} className="w-full mt-4 py-3 bg-violet-600 hover:bg-violet-700 text-white font-black uppercase tracking-wider transition-colors">
                                    Оформить заказ
                                </button>
                            </div>
                        )}

                        {bookingStep === 3 && (
                            <div>
                                <button type="button" onClick={() => setBookingStep(2)} className="text-zinc-500 hover:text-white mb-4 flex items-center gap-1 text-sm font-bold">← Назад</button>
                                <h2 className="text-xl font-black text-white mb-4 uppercase tracking-wider">Оформление</h2>
                                <div className="space-y-4">
                                    <label className="block text-xs uppercase tracking-[0.15em] text-zinc-600 font-bold">Имя и фамилия
                                        <input type="text" value={data.customer_name} onChange={e => setData('customer_name', e.target.value)} required
                                               className="mt-1 w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                                    </label>
                                    <label className="block text-xs uppercase tracking-[0.15em] text-zinc-600 font-bold">E-mail
                                        <input type="email" value={data.customer_email} onChange={e => setData('customer_email', e.target.value)} required
                                               className="mt-1 w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                                    </label>
                                    <label className="block text-xs uppercase tracking-[0.15em] text-zinc-600 font-bold">Телефон
                                        <input type="text" value={data.customer_phone} onChange={e => setData('customer_phone', e.target.value)}
                                               className="mt-1 w-full bg-black border border-white/10 px-3 py-2.5 text-white placeholder-zinc-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30" />
                                    </label>
                                </div>
                                <div className="mt-4 border border-white/10 bg-black/50 p-3 text-sm text-zinc-400">
                                    Билеты: {ticketsSum.toLocaleString('ru-RU')} ₽<br />
                                    {addonsSum > 0 && <>Дополнения: {addonsSum.toLocaleString('ru-RU')} ₽<br /></>}
                                    Итого: {totalSum.toLocaleString('ru-RU')} ₽
                                </div>
                                <div className="mt-4 flex flex-col gap-3">
                                    <button type="submit" disabled={processing} className="py-3 bg-violet-600 hover:bg-violet-700 text-white font-black text-lg uppercase tracking-wider transition-colors">Оформить заказ</button>
                                    <button type="button" onClick={() => submitBooking(null, 1)} disabled={processing} className="py-3 border-2 border-violet-500 bg-violet-500/10 text-violet-300 font-black text-lg uppercase tracking-wider hover:bg-violet-500/20 transition-colors">Тестовая покупка</button>
                                </div>
                            </div>
                        )}
                    </form>
                </div>
            </div>

            {ticketsCount > 0 && bookingStep === 1 && (
                <div className="fixed bottom-0 left-0 right-0 z-20 border-t-2 border-violet-500 bg-black/95 backdrop-blur">
                    <div className="max-w-[1920px] mx-auto lg:ml-56 px-4 lg:px-8 py-3 flex flex-wrap items-center justify-between gap-4">
                        <div className="text-white font-black">
                            {ticketsCount} {ticketsCount === 1 ? 'билет' : ticketsCount < 5 ? 'билета' : 'билетов'}: {ticketsSum.toLocaleString('ru-RU')} ₽
                        </div>
                        <button type="button" onClick={() => setBookingStep(2)} className="px-8 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-black uppercase tracking-wider">Далее</button>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
