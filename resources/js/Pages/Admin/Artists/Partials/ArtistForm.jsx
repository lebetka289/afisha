import React from 'react';
import { useForm } from '@inertiajs/react';

const albumTypes = [{ value: 'album', label: 'Альбом' }, { value: 'single', label: 'Сингл' }, { value: 'ep', label: 'EP' }];

export default function ArtistForm({ artist, method = 'POST', route: actionRoute }) {
    const initialAlbums = (artist.albums || []).map(a => ({
        id: a.id,
        title: a.title || '',
        year: a.year || '',
        type: a.type || 'album',
        link: a.link || '',
        cover_url: a.cover_url || ''
    }));

    const { data, setData, post, processing, errors } = useForm({
        name: artist.name || '',
        slug: artist.slug || '',
        description: artist.description || '',
        photo: null,
        links_json: artist.links ? JSON.stringify(artist.links, null, 2) : '',
        albums: initialAlbums,
        _method: method
    });

    const addAlbum = () => {
        setData('albums', [...(data.albums || []), { id: null, title: '', year: '', type: 'album', link: '', cover_url: '' }]);
    };
    const removeAlbum = (index) => {
        setData('albums', data.albums.filter((_, i) => i !== index));
    };
    const updateAlbum = (index, field, value) => {
        const next = [...(data.albums || [])];
        next[index] = { ...next[index], [field]: value };
        setData('albums', next);
    };

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

            <div className="space-y-3">
                <p className="text-xs uppercase text-slate-500 tracking-wide">Соцсети и стриминг</p>
                {['vk', 'telegram', 'instagram', 'youtube', 'spotify', 'apple_music', 'yandex_music', 'soundcloud', 'tiktok', 'website'].map(key => {
                    const labels = { vk: 'VK', telegram: 'Telegram', instagram: 'Instagram', youtube: 'YouTube', spotify: 'Spotify', apple_music: 'Apple Music', yandex_music: 'Яндекс Музыка', soundcloud: 'SoundCloud', tiktok: 'TikTok', website: 'Сайт' };
                    let parsed = {};
                    try { parsed = JSON.parse(data.links_json || '{}'); } catch {}
                    return (
                        <label key={key} className="flex items-center gap-3">
                            <span className="w-28 shrink-0 text-xs text-slate-400 font-bold">{labels[key] || key}</span>
                            <input
                                type="url"
                                value={parsed[key] || ''}
                                onChange={e => {
                                    const next = { ...parsed, [key]: e.target.value };
                                    Object.keys(next).forEach(k => { if (!next[k]) delete next[k]; });
                                    setData('links_json', JSON.stringify(next));
                                }}
                                placeholder={`https://...`}
                                className="flex-1 rounded-lg bg-slate-900 border border-slate-800 px-3 py-1.5 text-white text-sm"
                            />
                        </label>
                    );
                })}
            </div>

            {(
                <div className="border-t border-slate-800 pt-6">
                    <h3 className="text-sm font-bold text-slate-300 mb-3">Дискография</h3>
                    {(data.albums || []).map((album, index) => (
                        <div key={index} className="flex flex-wrap gap-3 items-end mb-3 p-3 rounded-xl bg-slate-900 border border-slate-800">
                            <input type="hidden" name={`albums[${index}][id]`} value={album.id || ''} />
                            <label className="flex-1 min-w-[120px]">
                                <span className="text-xs text-slate-500">Название</span>
                                <input type="text" value={album.title} onChange={e => updateAlbum(index, 'title', e.target.value)} className="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-2 py-1.5 text-white text-sm" />
                            </label>
                            <label className="w-20">
                                <span className="text-xs text-slate-500">Год</span>
                                <input type="number" value={album.year} onChange={e => updateAlbum(index, 'year', e.target.value)} min="1900" max="2100" className="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-2 py-1.5 text-white text-sm" />
                            </label>
                            <label className="w-28">
                                <span className="text-xs text-slate-500">Тип</span>
                                <select value={album.type} onChange={e => updateAlbum(index, 'type', e.target.value)} className="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-2 py-1.5 text-white text-sm">
                                    {albumTypes.map(t => <option key={t.value} value={t.value}>{t.label}</option>)}
                                </select>
                            </label>
                            <label className="flex-1 min-w-[140px]">
                                <span className="text-xs text-slate-500">Ссылка</span>
                                <input type="url" value={album.link} onChange={e => updateAlbum(index, 'link', e.target.value)} className="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-2 py-1.5 text-white text-sm" placeholder="https://..." />
                            </label>
                            <label className="flex-1 min-w-[140px]">
                                <span className="text-xs text-slate-500">Обложка (URL)</span>
                                <input type="text" value={album.cover_url} onChange={e => updateAlbum(index, 'cover_url', e.target.value)} className="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-2 py-1.5 text-white text-sm" />
                            </label>
                            <button type="button" onClick={() => removeAlbum(index)} className="px-3 py-1.5 rounded-lg bg-rose-500/20 text-rose-400 hover:bg-rose-500/30 text-sm">Удалить</button>
                        </div>
                    ))}
                    <button type="button" onClick={addAlbum} className="px-4 py-2 rounded-xl bg-slate-800 border border-slate-700 text-slate-300 hover:bg-slate-700 text-sm font-bold">
                        + Добавить альбом / сингл
                    </button>
                </div>
            )}

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
