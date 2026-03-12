import React, { useState, useEffect, useRef } from 'react';
import { useForm } from '@inertiajs/react';

export default function EventForm({ event, venues, artists, action, method = 'POST' }) {
    // Transform initial data if needed
    const initialSections = event.sections?.map(section => {
        const seatMap = {};
        section.seats?.forEach(seat => {
            if (!seatMap[seat.row_number]) seatMap[seat.row_number] = {};
            seatMap[seat.row_number][seat.col_number] = {
                status: seat.status,
                label: seat.label,
                price: parseFloat(seat.price),
            };
        });

        return {
            id: section.id,
            name: section.name,
            type: section.type,
            color: section.color,
            seating_mode: section.seating_mode,
            capacity: section.capacity,
            price: parseFloat(section.price),
            rows: section.rows,
            cols: section.cols,
            seat_map: seatMap,
            position: section.position || { x: 10, y: 10, width: 20, height: 15 },
            meta: section.meta,
            sort_order: section.sort_order,
        };
    }) || [];

    const initialAddons = event.addons?.map(a => ({
        id: a.id,
        name: a.name,
        price: parseFloat(a.price),
        description: a.description || ''
    })) || [];

    const { data, setData, post, processing, errors } = useForm({
        _method: method,
        title: event.title || '',
        subtitle: event.subtitle || '',
        category: event.category || 'concert',
        slug: event.slug || '',
        venue_id: event.venue_id || '',
        artist_id: event.artist_id || '',
        status: event.status || 'draft',
        start_at: event.start_at ? new Date(event.start_at).toISOString().slice(0, 16) : '',
        end_at: event.end_at ? new Date(event.end_at).toISOString().slice(0, 16) : '',
        sales_start_at: event.sales_start_at ? new Date(event.sales_start_at).toISOString().slice(0, 16) : '',
        sales_end_at: event.sales_end_at ? new Date(event.sales_end_at).toISOString().slice(0, 16) : '',
        poster_url: event.poster_url || '',
        max_tickets: event.max_tickets || '',
        description: event.description || '',
        layout_type: 'custom',
        sections_payload: initialSections,
        addons_payload: initialAddons,
        poster_upload: null,
    });

    const [editingSectionIndex, setEditingSectionIndex] = useState(null);
    const [sectionForm, setSectionForm] = useState({
        id: null,
        name: '',
        type: 'standard',
        color: '#3b82f6',
        seating_mode: 'standing',
        capacity: '',
        price: '',
        rows: '',
        cols: '',
        position: { x: 10, y: 10, width: 20, height: 15 }
    });

    const canvasRef = useRef(null);
    const [drawPreview, setDrawPreview] = useState(null);
    const [dragState, setDragState] = useState(null);
    const [resizeState, setResizeState] = useState(null);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(action);
    };

    // Sections Builder Logic
    const getPercentFromEvent = (e) => {
        const r = canvasRef.current.getBoundingClientRect();
        const x = ((e.clientX - r.left) / r.width) * 100;
        const y = ((e.clientY - r.top) / r.height) * 100;
        return { x: Math.max(0, Math.min(100, x)), y: Math.max(0, Math.min(100, y)) };
    };

    const handleCanvasMouseDown = (e) => {
        if (e.target !== e.currentTarget && !e.target.dataset.drawArea) return;
        const start = getPercentFromEvent(e);
        setDrawPreview({ start, current: start });

        const onMove = (e2) => {
            const current = getPercentFromEvent(e2);
            setDrawPreview(prev => ({ ...prev, current }));
        };

        const onUp = (eUp) => {
            const end = getPercentFromEvent(eUp);
            const x = Math.min(start.x, end.x);
            const y = Math.min(start.y, end.y);
            const w = Math.abs(end.x - start.x);
            const h = Math.abs(end.y - start.y);

            if (w >= 3 && h >= 3) {
                setSectionForm(prev => ({
                    ...prev,
                    position: { x: Math.round(x * 10) / 10, y: Math.round(y * 10) / 10, width: Math.round(w * 10) / 10, height: Math.round(h * 10) / 10 }
                }));
            }
            setDrawPreview(null);
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onUp);
        };

        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    };

    const handleBlockMouseDown = (e, index) => {
        if (e.target.dataset.resizeHandle) return;
        e.preventDefault();
        const startMouse = getPercentFromEvent(e);
        setDragState({ index, startMouse, moved: false });

        const onMove = (e2) => {
            const cur = getPercentFromEvent(e2);
            setDragState(prev => {
                if (!prev) return null;
                const dx = cur.x - prev.startMouse.x;
                const dy = cur.y - prev.startMouse.y;
                
                const newSections = [...data.sections_payload];
                const section = { ...newSections[prev.index] };
                const pos = { ...section.position };
                
                pos.x = Math.max(0, Math.min(100 - pos.width, pos.x + dx));
                pos.y = Math.max(0, Math.min(100 - pos.height, pos.y + dy));
                
                section.position = pos;
                newSections[prev.index] = section;
                setData('sections_payload', newSections);
                
                if (editingSectionIndex === prev.index) {
                    setSectionForm(prevForm => ({ ...prevForm, position: pos }));
                }

                return { ...prev, startMouse: cur, moved: true };
            });
        };

        const onUp = () => {
            setDragState(prev => {
                if (prev && !prev.moved) {
                    startEdit(prev.index);
                }
                return null;
            });
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onUp);
        };

        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    };

    const handleResizeMouseDown = (e, index, handle) => {
        e.preventDefault();
        e.stopPropagation();
        const startMouse = getPercentFromEvent(e);
        const startPos = { ...data.sections_payload[index].position };
        setResizeState({ index, handle, startPos, startMouse });

        const onMove = (e2) => {
            const cur = getPercentFromEvent(e2);
            setResizeState(prev => {
                if (!prev) return null;
                const h = prev.handle;
                let { x, y, width, height } = { ...prev.startPos };
                const dx = cur.x - prev.startMouse.x;
                const dy = cur.y - prev.startMouse.y;

                if (h.includes('e')) width = Math.max(5, width + dx);
                if (h.includes('w')) { 
                    const oldX = x;
                    x = Math.min(x + dx, x + width - 5);
                    width = width + (oldX - x);
                }
                if (h.includes('s')) height = Math.max(5, height + dy);
                if (h.includes('n')) {
                    const oldY = y;
                    y = Math.min(y + dy, y + height - 5);
                    height = height + (oldY - y);
                }

                x = Math.max(0, Math.min(100 - width, x));
                y = Math.max(0, Math.min(100 - height, y));

                const newSections = [...data.sections_payload];
                newSections[prev.index].position = { x, y, width, height };
                setData('sections_payload', newSections);

                if (editingSectionIndex === prev.index) {
                    setSectionForm(prevForm => ({ ...prevForm, position: { x, y, width, height } }));
                }

                return { ...prev, startPos: { x, y, width, height }, startMouse: cur };
            });
        };

        const onUp = () => {
            setResizeState(null);
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onUp);
        };

        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    };

    const startEdit = (index) => {
        setEditingSectionIndex(index);
        setSectionForm({ ...data.sections_payload[index] });
    };

    const resetSectionForm = () => {
        setEditingSectionIndex(null);
        setSectionForm({
            id: null,
            name: '',
            type: 'standard',
            color: '#3b82f6',
            seating_mode: 'standing',
            capacity: '',
            price: '',
            rows: '',
            cols: '',
            position: { x: 10, y: 10, width: 20, height: 15 }
        });
    };

    const saveSection = () => {
        const payload = {
            ...sectionForm,
            capacity: Number(sectionForm.capacity) || 0,
            price: Number(sectionForm.price) || 0,
            rows: Number(sectionForm.rows) || 0,
            cols: Number(sectionForm.cols) || 0,
        };

        let newSections = [...data.sections_payload];
        if (editingSectionIndex !== null) {
            newSections[editingSectionIndex] = payload;
            // Sync prices by type
            newSections = newSections.map(s => s.type === payload.type ? { ...s, price: payload.price } : s);
        } else {
            // Check if type already exists to sync price
            const existing = newSections.find(s => s.type === payload.type);
            if (existing) payload.price = existing.price;
            newSections.push({ ...payload, sort_order: newSections.length });
        }
        setData('sections_payload', newSections);
        setEditingSectionIndex(editingSectionIndex !== null ? editingSectionIndex : newSections.length - 1);
    };

    const deleteSection = () => {
        if (editingSectionIndex === null) return;
        const newSections = data.sections_payload.filter((_, i) => i !== editingSectionIndex);
        setData('sections_payload', newSections);
        resetSectionForm();
    };

    const addAddon = () => {
        setData('addons_payload', [...data.addons_payload, { id: null, name: '', price: 0, description: '' }]);
    };

    const updateAddon = (index, field, value) => {
        const newAddons = [...data.addons_payload];
        newAddons[index][field] = value;
        setData('addons_payload', newAddons);
    };

    const removeAddon = (index) => {
        setData('addons_payload', data.addons_payload.filter((_, i) => i !== index));
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-10">
            {Object.keys(errors).length > 0 && (
                <div className="mb-6 rounded-2xl border border-rose-500/40 bg-rose-500/10 text-sm text-rose-100 px-5 py-4 space-y-1">
                    {Object.values(errors).map((error, i) => (
                        <div key={i}>{error}</div>
                    ))}
                </div>
            )}

            <section className="grid gap-6 lg:grid-cols-2">
                <div className="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                    <h2 className="text-lg font-semibold">Основная информация</h2>
                    <label className="block text-sm text-slate-400 uppercase tracking-wide">Название
                        <input type="text" value={data.title} onChange={e => setData('title', e.target.value)} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" required />
                    </label>
                    <label className="block text-sm text-slate-400 uppercase tracking-wide">Подзаголовок
                        <input type="text" value={data.subtitle} onChange={e => setData('subtitle', e.target.value)} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" />
                    </label>
                    <label className="block text-sm text-slate-400 uppercase tracking-wide">Категория
                        <select value={data.category} onChange={e => setData('category', e.target.value)} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
                            <option value="concert">Концерты</option>
                            <option value="theater">Театр</option>
                            <option value="show">Шоу</option>
                            <option value="standup">Стендап</option>
                        </select>
                    </label>
                    <label className="block text-sm text-slate-400 uppercase tracking-wide">Slug / ссылка
                        <input type="text" value={data.slug} onChange={e => setData('slug', e.target.value)} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" />
                    </label>
                    <label className="block text-sm text-slate-400 uppercase tracking-wide">Площадка
                        <select value={data.venue_id} onChange={e => setData('venue_id', e.target.value)} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
                            <option value="">— Не указано —</option>
                            {venues.map(venue => (
                                <option key={venue.id} value={venue.id}>{venue.name}</option>
                            ))}
                        </select>
                    </label>
                    <label className="block text-sm text-slate-400 uppercase tracking-wide">Исполнитель
                        <select value={data.artist_id} onChange={e => setData('artist_id', e.target.value)} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
                            <option value="">— Не указано —</option>
                            {artists.map(artist => (
                                <option key={artist.id} value={artist.id}>{artist.name}</option>
                            ))}
                        </select>
                    </label>
                    <label className="block text-sm text-slate-400 uppercase tracking-wide">Статус
                        <select value={data.status} onChange={e => setData('status', e.target.value)} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" required>
                            <option value="draft">Черновик</option>
                            <option value="published">Опубликовано</option>
                            <option value="archived">Архив</option>
                        </select>
                    </label>
                    <div className="grid grid-cols-2 gap-4">
                        <label className="block text-sm text-slate-400 uppercase tracking-wide">Дата начала
                            <input type="datetime-local" value={data.start_at} onChange={e => setData('start_at', e.target.value)} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" />
                        </label>
                        <label className="block text-sm text-slate-400 uppercase tracking-wide">Дата окончания
                            <input type="datetime-local" value={data.end_at} onChange={e => setData('end_at', e.target.value)} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" />
                        </label>
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                        <label className="block text-sm text-slate-400 uppercase tracking-wide">Старт продаж
                            <input type="datetime-local" value={data.sales_start_at} onChange={e => setData('sales_start_at', e.target.value)} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" />
                        </label>
                        <label className="block text-sm text-slate-400 uppercase tracking-wide">Закрытие продаж
                            <input type="datetime-local" value={data.sales_end_at} onChange={e => setData('sales_end_at', e.target.value)} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" />
                        </label>
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                        <label className="block text-sm text-slate-400 uppercase tracking-wide">Постер / баннер (URL)
                            <input type="text" value={data.poster_url} onChange={e => setData('poster_url', e.target.value)} placeholder="https://example.com/poster.jpg" className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" />
                        </label>
                        <label className="block text-sm text-slate-400 uppercase tracking-wide">Макс. билетов
                            <input type="number" value={data.max_tickets} onChange={e => setData('max_tickets', e.target.value)} min="0" className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" />
                        </label>
                    </div>
                    <label className="block text-sm text-slate-400 uppercase tracking-wide">Загрузить фото / GIF / видео
                        <input type="file" onChange={e => setData('poster_upload', e.target.files[0])} accept="image/png,image/jpeg,image/gif,image/webp,video/mp4,video/webm,video/quicktime" className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" />
                    </label>
                    {event.poster_src && (
                        <div className="rounded-2xl overflow-hidden border border-slate-800 bg-slate-950">
                            {event.poster_is_video ? (
                                <video src={event.poster_src} controls className="w-full max-h-72 object-cover bg-black"></video>
                            ) : (
                                <img src={event.poster_src} alt="" className="w-full max-h-72 object-cover" />
                            )}
                        </div>
                    )}
                </div>
                <div className="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                    <h2 className="text-lg font-semibold">Описание</h2>
                    <textarea value={data.description} onChange={e => setData('description', e.target.value)} rows="12" className="w-full rounded-2xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0"></textarea>
                </div>
            </section>

            <section className="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p className="text-sm uppercase text-slate-500 tracking-[0.3em]">Конструктор</p>
                        <h2 className="text-2xl font-semibold">Зонирование зала</h2>
                    </div>
                    <button type="button" onClick={resetSectionForm} className="text-sm text-slate-400 hover:text-white transition">Сбросить выделение</button>
                </div>

                <div className="grid gap-8 lg:grid-cols-[2fr,1fr]">
                    <div>
                        <p className="text-xs text-slate-500 mb-2">Нарисуйте зону мышью или перетащите существующую. Выделите зону и тяните за углы для изменения размера.</p>
                        <div 
                            ref={canvasRef}
                            className="relative bg-slate-950 rounded-2xl border border-slate-800 aspect-[4/3] overflow-hidden select-none"
                            onMouseDown={handleCanvasMouseDown}
                        >
                            <div className="absolute inset-0 pointer-events-none border border-dashed border-slate-800 rounded-2xl m-2"></div>
                            <div className="absolute top-2 left-1/2 -translate-x-1/2 px-6 py-2 rounded-full text-xs tracking-[0.3em] bg-slate-800 border border-slate-700 text-slate-200 pointer-events-none">СЦЕНА</div>
                            <div className="absolute inset-0 z-0" data-draw-area="true"></div>

                            {data.sections_payload.map((section, index) => (
                                <div
                                    key={index}
                                    className={`absolute rounded-xl border-2 cursor-move transition z-10 ${editingSectionIndex === index ? 'ring-2 ring-amber-400 ring-offset-2 ring-offset-slate-950' : 'border-white/20 hover:border-white/40'}`}
                                    style={{
                                        left: `${section.position.x}%`,
                                        top: `${section.position.y}%`,
                                        width: `${section.position.width}%`,
                                        height: `${section.position.height}%`,
                                        background: `${section.color || '#3b82f6'}33`,
                                        borderColor: section.color || '#3b82f6'
                                    }}
                                    onMouseDown={(e) => handleBlockMouseDown(e, index)}
                                >
                                    <div className="absolute inset-0 flex items-center justify-center text-xs font-semibold text-white text-center px-2 pointer-events-none">
                                        {section.name || 'Зона'}
                                    </div>
                                    {editingSectionIndex === index && ['nw','n','ne','e','se','s','sw','w'].map(h => (
                                        <div
                                            key={h}
                                            data-resize-handle="true"
                                            className={`absolute w-2.5 h-2.5 bg-amber-400 rounded border border-amber-600 z-20 cursor-${h.includes('n') || h.includes('s') ? 'ns' : 'ew'}-resize`}
                                            style={{
                                                top: h.includes('n') ? '0' : h.includes('s') ? 'auto' : '50%',
                                                bottom: h.includes('s') ? '0' : 'auto',
                                                left: h.includes('w') ? '0' : h.includes('e') ? 'auto' : '50%',
                                                right: h.includes('e') ? '0' : 'auto',
                                                transform: (h === 'n' || h === 's') ? 'translateX(-50%)' : (h === 'e' || h === 'w') ? 'translateY(-50%)' : 'none'
                                            }}
                                            onMouseDown={(e) => handleResizeMouseDown(e, index, h)}
                                        />
                                    ))}
                                </div>
                            ))}

                            {drawPreview && (
                                <div 
                                    className="absolute border-2 border-dashed border-amber-400 bg-amber-400/20 pointer-events-none z-30"
                                    style={{
                                        left: `${Math.min(drawPreview.start.x, drawPreview.current.x)}%`,
                                        top: `${Math.min(drawPreview.start.y, drawPreview.current.y)}%`,
                                        width: `${Math.abs(drawPreview.current.x - drawPreview.start.x)}%`,
                                        height: `${Math.abs(drawPreview.current.y - drawPreview.start.y)}%`,
                                    }}
                                />
                            )}
                        </div>
                    </div>

                    <div className="bg-slate-950 border border-slate-800 rounded-2xl p-4 space-y-3">
                        <div className="grid gap-3">
                            <label className="text-xs uppercase text-slate-500 tracking-wide">Название зоны
                                <input type="text" value={sectionForm.name} onChange={e => setSectionForm({...sectionForm, name: e.target.value})} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" placeholder="Например: VIP ложа" />
                            </label>
                            <label className="text-xs uppercase text-slate-500 tracking-wide">Тип
                                <select value={sectionForm.type} onChange={e => setSectionForm({...sectionForm, type: e.target.value})} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
                                    <option value="vip">VIP</option>
                                    <option value="dancefloor">Танцпол</option>
                                    <option value="balcony">Балкон</option>
                                    <option value="standard">Стандарт</option>
                                </select>
                            </label>
                            <label className="text-xs uppercase text-slate-500 tracking-wide">Цвет
                                <input type="color" value={sectionForm.color} onChange={e => setSectionForm({...sectionForm, color: e.target.value})} className="mt-1 h-10 w-full rounded-xl bg-slate-900 border border-slate-800" />
                            </label>
                            <label className="text-xs uppercase text-slate-500 tracking-wide">Режим
                                <select value={sectionForm.seating_mode} onChange={e => setSectionForm({...sectionForm, seating_mode: e.target.value})} className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
                                    <option value="standing">Зона без мест (танцпол)</option>
                                    <option value="seated">Сидячие места</option>
                                </select>
                            </label>
                            <div className="grid grid-cols-2 gap-3">
                                <label className="text-xs uppercase text-slate-500 tracking-wide">Вместимость
                                    <input type="number" value={sectionForm.capacity} onChange={e => setSectionForm({...sectionForm, capacity: e.target.value})} min="0" className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" placeholder="Например 200" />
                                </label>
                                <label className="text-xs uppercase text-slate-500 tracking-wide">Цена, ₽
                                    <input type="number" value={sectionForm.price} onChange={e => setSectionForm({...sectionForm, price: e.target.value})} min="0" step="100" className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" />
                                </label>
                            </div>
                            {sectionForm.seating_mode === 'seated' && (
                                <div className="grid grid-cols-2 gap-3">
                                    <label className="text-xs uppercase text-slate-500 tracking-wide">Рядов
                                        <input type="number" value={sectionForm.rows} onChange={e => setSectionForm({...sectionForm, rows: e.target.value})} min="0" className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" />
                                    </label>
                                    <label className="text-xs uppercase text-slate-500 tracking-wide">Мест в ряду
                                        <input type="number" value={sectionForm.cols} onChange={e => setSectionForm({...sectionForm, cols: e.target.value})} min="0" className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" />
                                    </label>
                                </div>
                            )}
                            <div className="grid grid-cols-2 gap-3">
                                <label className="text-xs uppercase text-slate-500 tracking-wide">Позиция X
                                    <input type="number" value={sectionForm.position.x} onChange={e => setSectionForm({...sectionForm, position: {...sectionForm.position, x: Number(e.target.value)}})} min="0" max="100" className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" />
                                </label>
                                <label className="text-xs uppercase text-slate-500 tracking-wide">Позиция Y
                                    <input type="number" value={sectionForm.position.y} onChange={e => setSectionForm({...sectionForm, position: {...sectionForm.position, y: Number(e.target.value)}})} min="0" max="100" className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" />
                                </label>
                                <label className="text-xs uppercase text-slate-500 tracking-wide">Ширина %
                                    <input type="number" value={sectionForm.position.width} onChange={e => setSectionForm({...sectionForm, position: {...sectionForm.position, width: Number(e.target.value)}})} min="5" max="100" className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" />
                                </label>
                                <label className="text-xs uppercase text-slate-500 tracking-wide">Высота %
                                    <input type="number" value={sectionForm.position.height} onChange={e => setSectionForm({...sectionForm, position: {...sectionForm.position, height: Number(e.target.value)}})} min="5" max="100" className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" />
                                </label>
                            </div>

                            <div className="flex flex-col gap-2">
                                <button type="button" onClick={saveSection} className="w-full bg-indigo-500 hover:bg-indigo-400 text-white font-semibold rounded-xl py-2 transition">
                                    {editingSectionIndex !== null ? 'Сохранить изменения' : 'Добавить зону'}
                                </button>
                                {editingSectionIndex !== null && (
                                    <>
                                        <button type="button" onClick={resetSectionForm} className="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl py-2 transition">+ Создать новую зону</button>
                                        <button type="button" onClick={deleteSection} className="w-full py-2 rounded-xl border border-rose-500/50 text-rose-200 text-sm">Удалить зону</button>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    {data.sections_payload.length === 0 ? (
                        <div className="col-span-full text-center text-slate-500 text-sm border border-dashed border-slate-800 rounded-2xl py-6">Добавьте первую зону</div>
                    ) : data.sections_payload.map((section, index) => (
                        <div 
                            key={index} 
                            onClick={() => startEdit(index)}
                            className={`rounded-2xl border bg-slate-950/80 p-4 space-y-2 cursor-pointer transition ${editingSectionIndex === index ? 'border-indigo-500' : 'border-slate-800 hover:border-indigo-500/50'}`}
                        >
                            <div className="flex items-center justify-between">
                                <div className="font-semibold text-white">{section.name}</div>
                                <span className="text-xs uppercase tracking-wide text-slate-500">{section.type}</span>
                            </div>
                            <div className="text-sm text-slate-400">
                                Режим: {section.seating_mode === 'seated' ? 'Сидячие' : 'Зона'} · Цена: {section.price || 0} ₽
                            </div>
                        </div>
                    ))}
                </div>
            </section>

            <section className="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                <h2 className="text-lg font-semibold">Дополнения к заказу</h2>
                <p className="text-sm text-slate-400">Дополнительные опции, которые пользователь может добавить при покупке билета (Meet & Greet, мерч и т.д.)</p>
                <div className="space-y-3">
                    {data.addons_payload.map((addon, index) => (
                        <div key={index} className="grid grid-cols-1 md:grid-cols-12 gap-3 items-start p-3 rounded-xl bg-slate-950/80 border border-slate-800">
                            <div className="md:col-span-4">
                                <input type="text" value={addon.name} onChange={e => updateAddon(index, 'name', e.target.value)} className="w-full rounded-lg bg-slate-900 border border-slate-700 px-3 py-2 text-white text-sm" placeholder="Название" />
                            </div>
                            <div className="md:col-span-2">
                                <input type="number" value={addon.price} onChange={e => updateAddon(index, 'price', e.target.value)} min="0" step="100" className="w-full rounded-lg bg-slate-900 border border-slate-700 px-3 py-2 text-white text-sm" placeholder="Цена ₽" />
                            </div>
                            <div className="md:col-span-4">
                                <input type="text" value={addon.description} onChange={e => updateAddon(index, 'description', e.target.value)} className="w-full rounded-lg bg-slate-900 border border-slate-700 px-3 py-2 text-white text-sm" placeholder="Описание (необяз.)" />
                            </div>
                            <div className="md:col-span-2">
                                <button type="button" onClick={() => removeAddon(index)} className="text-rose-400 hover:text-rose-300 text-sm">Удалить</button>
                            </div>
                        </div>
                    ))}
                </div>
                <button type="button" onClick={addAddon} className="px-4 py-2 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 text-sm">+ Добавить дополнение</button>
            </section>

            <div className="flex justify-end">
                <button type="submit" disabled={processing} className="px-6 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 transition text-white font-semibold disabled:opacity-50">
                    {processing ? 'Сохранение...' : 'Сохранить событие'}
                </button>
            </div>
        </form>
    );
}
