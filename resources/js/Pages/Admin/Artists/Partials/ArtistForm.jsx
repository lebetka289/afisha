import React from 'react';
import { useForm } from '@inertiajs/react';

export default function ArtistForm({ artist, method = 'POST', route: actionRoute }) {
    const { data, setData, post, processing, errors } = useForm({
        name: artist.name || '',
        slug: artist.slug || '',
        description: artist.description || '',
        photo: null,
        links_json: artist.links ? JSON.stringify(artist.links, null, 2) : '',
        _method: method
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        // Since we are uploading files, we must use POST and _method for PUT/PATCH
        if (method === 'POST') {
            post(actionRoute);
        } else {
            post(actionRoute);
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
                    Имя исполнителя
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
            </div>

            <label className="text-xs uppercase text-slate-500 tracking-wide block">
                Описание
                <textarea
                    value={data.description}
                    onChange={e => setData('description', e.target.value)}
                    rows="6"
                    className="mt-1 w-full rounded-2xl bg-slate-900 border border-slate-800 px-3 py-2 text-white"
                ></textarea>
            </label>

            <div className="space-y-3">
                <label className="text-xs uppercase text-slate-500 tracking-wide block">
                    Фото исполнителя
                    <input
                        type="file"
                        onChange={e => setData('photo', e.target.files[0])}
                        accept="image/png,image/jpeg,image/gif,image/webp,video/mp4,video/webm,video/quicktime"
                        className="mt-1 block w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white"
                    />
                </label>
                {artist.photo_src && (
                    <div className="mt-2">
                        {artist.photo_is_video ? (
                            <video src={artist.photo_src} controls className="w-40 h-32 rounded-2xl object-cover border border-slate-800 bg-black"></video>
                        ) : (
                            <img src={artist.photo_src} alt="" className="w-32 h-32 rounded-2xl object-cover border border-slate-800" />
                        )}
                    </div>
                )}
            </div>

            <label className="text-xs uppercase text-slate-500 tracking-wide block">
                Ссылки на площадки / соцсети (JSON)
                <textarea
                    value={data.links_json}
                    onChange={e => setData('links_json', e.target.value)}
                    rows="5"
                    className="mt-1 w-full rounded-2xl bg-slate-900 border border-slate-800 px-3 py-2 text-white"
                    placeholder='[{"title":"VK","url":"https://vk.com/artist"},{"title":"YouTube","url":"https://youtube.com/..."}]'
                ></textarea>
            </label>

            <div className="flex justify-end">
                <button
                    type="submit"
                    disabled={processing}
                    className="px-6 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 transition text-white font-semibold disabled:opacity-50"
                >
                    Сохранить исполнителя
                </button>
            </div>
        </form>
    );
}
