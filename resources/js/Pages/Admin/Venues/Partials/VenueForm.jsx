import React from 'react';
import { useForm } from '@inertiajs/react';

export default function VenueForm({ venue, method = 'POST', route: actionRoute }) {
    const { data, setData, post, put, processing, errors } = useForm({
        name: venue.name || '',
        slug: venue.slug || '',
        city: venue.city || '',
        address: venue.address || '',
        description: venue.description || '',
        max_capacity: venue.max_capacity || 0,
        layout_type: venue.layout_type || 'arena',
        layout_config: venue.layout_config ? JSON.stringify(venue.layout_config, null, 2) : '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        if (method === 'POST') {
            post(actionRoute);
        } else {
            put(actionRoute);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6 bg-slate-900/60 border border-slate-800 rounded-2xl p-6">
            {Object.keys(errors).length > 0 && (
                <div className="mb-6 rounded-2xl border border-rose-500/40 bg-rose-500/10 text-sm text-rose-100 px-5 py-4 space-y-1">
                    {Object.values(errors).map((error, i) => (
                        <div key={i}>{error}</div>
                    ))}
                </div>
            )}

            <div className="grid gap-4 md:grid-cols-2">
                <label className="text-xs uppercase text-slate-500 tracking-wide block">
                    Название
                    <input
                        type="text"
                        value={data.name}
                        onChange={e => setData('name', e.target.value)}
                        required
                        className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white"
                    />
                </label>
                <label className="text-xs uppercase text-slate-500 tracking-wide block">
                    Slug
                    <input
                        type="text"
                        value={data.slug}
                        onChange={e => setData('slug', e.target.value)}
                        className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white"
                    />
                </label>
                <label className="text-xs uppercase text-slate-500 tracking-wide block">
                    Город
                    <input
                        type="text"
                        value={data.city}
                        onChange={e => setData('city', e.target.value)}
                        className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white"
                    />
                </label>
                <label className="text-xs uppercase text-slate-500 tracking-wide block">
                    Адрес
                    <input
                        type="text"
                        value={data.address}
                        onChange={e => setData('address', e.target.value)}
                        className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white"
                    />
                </label>
            </div>

            <label className="text-xs uppercase text-slate-500 tracking-wide block">
                Описание
                <textarea
                    value={data.description}
                    onChange={e => setData('description', e.target.value)}
                    rows="5"
                    className="mt-1 w-full rounded-2xl bg-slate-900 border border-slate-800 px-3 py-2 text-white"
                ></textarea>
            </label>

            <div className="grid grid-cols-2 gap-4">
                <label className="text-xs uppercase text-slate-500 tracking-wide block">
                    Макс. вместимость
                    <input
                        type="number"
                        min="0"
                        value={data.max_capacity}
                        onChange={e => setData('max_capacity', parseInt(e.target.value) || 0)}
                        className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white"
                    />
                </label>
                <label className="text-xs uppercase text-slate-500 tracking-wide block">
                    Тип зала
                    <input
                        type="text"
                        value={data.layout_type}
                        onChange={e => setData('layout_type', e.target.value)}
                        className="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white"
                    />
                </label>
            </div>

            <label className="text-xs uppercase text-slate-500 tracking-wide block">
                Настройки зала (JSON)
                <textarea
                    value={data.layout_config}
                    onChange={e => setData('layout_config', e.target.value)}
                    rows="4"
                    className="mt-1 w-full rounded-2xl bg-slate-900 border border-slate-800 px-3 py-2 text-white"
                    placeholder='{"width":900,"height":600}'
                ></textarea>
            </label>

            <div className="flex justify-end">
                <button
                    type="submit"
                    disabled={processing}
                    className="px-6 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 transition text-white font-semibold disabled:opacity-50"
                >
                    Сохранить площадку
                </button>
            </div>
        </form>
    );
}
