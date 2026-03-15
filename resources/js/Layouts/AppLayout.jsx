import React, { useState, useEffect, useRef } from 'react';
import { Link, usePage, router } from '@inertiajs/react';

export default function AppLayout({ children, title }) {
    const { auth, flash, headerCities, currentCityName, ziggy, unreadNotificationsCount } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [searchQuery, setSearchQuery] = useState(ziggy.query.q || '');
    const [searchItems, setSearchItems] = useState([]);
    const [searchOpen, setSearchOpen] = useState(false);
    const [searchTitle, setSearchTitle] = useState('Популярно сейчас');
    const [cityOpen, setCityOpen] = useState(false);
    const [citySearch, setCitySearch] = useState('');
    const [authOpen, setAuthOpen] = useState(false);
    const [notifOpen, setNotifOpen] = useState(false);
    const [notifItems, setNotifItems] = useState([]);
    const [notifCount, setNotifCount] = useState(0);

    const searchRef = useRef(null);
    const cityRef = useRef(null);
    const authRef = useRef(null);
    const notifRef = useRef(null);

    const categories = {
        concert: 'Концерты',
        theater: 'Театр',
        show: 'Шоу',
        standup: 'Стендап'
    };

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (searchRef.current && !searchRef.current.contains(event.target)) setSearchOpen(false);
            if (cityRef.current && !cityRef.current.contains(event.target)) setCityOpen(false);
            if (authRef.current && !authRef.current.contains(event.target)) setAuthOpen(false);
            if (notifRef.current && !notifRef.current.contains(event.target)) setNotifOpen(false);
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    useEffect(() => {
        setNotifCount(unreadNotificationsCount || 0);
    }, [unreadNotificationsCount]);

    useEffect(() => {
        if (searchOpen) {
            fetchSuggestions(searchQuery);
        }
    }, [searchQuery, searchOpen]);

    const fetchSuggestions = async (query) => {
        try {
            const response = await fetch(`${route('events.suggest')}?q=${encodeURIComponent(query)}`, {
                headers: { Accept: 'application/json' },
            });
            const data = await response.json();
            setSearchItems(data.items || []);
            setSearchTitle(data.title || 'Найденные мероприятия');
        } catch (e) {
            setSearchItems([]);
        }
    };

    const fetchNotifications = async () => {
        try {
            const res = await fetch('/notifications', { headers: { Accept: 'application/json' } });
            const data = await res.json();
            setNotifItems(data.notifications || []);
            setNotifCount(data.unread_count || 0);
        } catch {}
    };

    const markAllRead = async () => {
        try {
            await fetch('/notifications/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', Accept: 'application/json' } });
            setNotifCount(0);
            setNotifItems(items => items.map(n => ({ ...n, read: true })));
        } catch {}
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        router.get(route('events.index'), { ...ziggy.query, q: searchQuery });
    };

    return (
        <div className="min-h-screen antialiased bg-black text-white font-sans">
            <div className="flex min-h-screen max-w-[1920px] mx-auto relative overflow-x-hidden">

                {/* SIDEBAR OVERLAY */}
                {sidebarOpen && (
                    <div
                        onClick={() => setSidebarOpen(false)}
                        className="sidebar-overlay lg:hidden fixed inset-0 bg-black/60 z-40 transition-opacity"
                    />
                )}

                {/* SIDEBAR */}
                <aside
                    className={`fixed left-0 top-0 bottom-0 w-56 flex flex-col bg-black border-r border-white/10 z-50 transition-transform duration-300 lg:transition-none ${
                        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
                    }`}
                >
                    <div className="p-5 pb-2">
                        <Link href={route('events.index')} className="flex items-center gap-3 group" onClick={() => setSidebarOpen(false)}>
                            <video src="/images/logo.mp4" autoPlay loop muted playsInline className="h-10 w-10 object-contain rounded-lg group-hover:opacity-80 transition-opacity" />
                            <div>
                                <div className="text-[10px] text-violet-400 uppercase tracking-[0.2em] font-bold">Платформа</div>
                                <div className="font-extrabold text-white text-lg leading-tight">OnTheRise</div>
                            </div>
                        </Link>
                    </div>

                    <div className="checkerboard-sm h-3 mx-5 mb-4 opacity-60"></div>

                    <nav className="flex-1 flex flex-col gap-0.5 py-2">
                        <Link
                            href={route('events.index')}
                            onClick={() => setSidebarOpen(false)}
                            className={`sidebar-link ${!ziggy.query.category && route().current('events.index') ? 'active' : ''}`}
                        >
                            Главная
                        </Link>
                        <Link
                            href={route('artists.index')}
                            onClick={() => setSidebarOpen(false)}
                            className={`sidebar-link ${route().current('artists.*') ? 'active' : ''}`}
                        >
                            Артисты
                        </Link>
                        {auth.user && (
                            <Link
                                href={route('recommendations.index')}
                                onClick={() => setSidebarOpen(false)}
                                className={`sidebar-link ${route().current('recommendations.*') ? 'active' : ''}`}
                            >
                                Рекомендации
                            </Link>
                        )}
                        {Object.entries(categories).map(([key, label]) => (
                            <Link
                                key={key}
                                href={route('events.index', { ...ziggy.query, category: key })}
                                onClick={() => setSidebarOpen(false)}
                                className={`sidebar-link ${ziggy.query.category === key ? 'active' : ''}`}
                            >
                                {label}
                            </Link>
                        ))}
                    </nav>

                    <div className="p-5 pt-2 border-t border-white/10">
                        <div className="checkerboard-sm h-2 mb-3 opacity-40"></div>
                        <p className="text-[10px] text-zinc-600 uppercase tracking-wider">{new Date().getFullYear()} · OnTheRise</p>
                    </div>
                </aside>

                {/* MAIN CONTENT */}
                <div className="flex-1 lg:ml-56 flex flex-col min-h-screen overflow-x-hidden">
                    <div className="marquee-track bg-violet-600 text-white font-extrabold text-xs py-1.5 uppercase tracking-[0.15em] select-none">
                        <div className="marquee-content">
                            <span>✦ OnTheRise </span><span>✦ КОНЦЕРТЫ </span><span>✦ БИЛЕТЫ </span><span>✦ АРТИСТЫ </span><span>✦ МЕРОПРИЯТИЯ </span>
                            <span>✦ OnTheRise </span><span>✦ КОНЦЕРТЫ </span><span>✦ БИЛЕТЫ </span><span>✦ АРТИСТЫ </span><span>✦ МЕРОПРИЯТИЯ </span>
                        </div>
                    </div>

                    <header className="sticky top-0 z-30 border-b border-white/10 bg-black/90 backdrop-blur-xl">
                        <div className="px-4 lg:px-8 py-3">
                            <div className="flex items-center gap-4 flex-wrap">
                                <button onClick={() => setSidebarOpen(!sidebarOpen)} className="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg border border-white/10 text-white hover:bg-white/5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M3 12h18M3 6h18M3 18h18" /></svg>
                                </button>

                                <form onSubmit={handleSearchSubmit} className="flex-1 min-w-0 max-w-lg relative" ref={searchRef}>
                                    <label className="relative block">
                                        <span className="sr-only">Поиск</span>
                                        <span className="absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-600 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>
                                        </span>
                                        <input
                                            type="search"
                                            value={searchQuery}
                                            onChange={(e) => setSearchQuery(e.target.value)}
                                            onFocus={() => setSearchOpen(true)}
                                            placeholder="События, артисты и места"
                                            className="w-full pl-10 pr-4 py-2.5 rounded-lg bg-white/[0.06] border border-white/10 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 text-white placeholder-zinc-600 transition-colors text-sm"
                                        />
                                    </label>
                                    {searchOpen && (
                                        <div className="absolute left-0 right-0 top-full mt-2 rounded-xl border border-white/10 bg-[#111] shadow-2xl shadow-black/50 overflow-hidden z-50">
                                            <p className="text-xs text-zinc-500 px-4 py-2 uppercase tracking-wider border-b border-white/10 bg-black/30">{searchTitle}</p>
                                            <ul className="max-h-[min(70vh,320px)] overflow-y-auto scrollbar-thin py-1">
                                                {searchItems.length === 0 ? (
                                                    <li className="px-4 py-3 text-sm text-zinc-500">Введите запрос для поиска</li>
                                                ) : (
                                                    searchItems.map((item, idx) => (
                                                        <li key={item.url + String(idx)}>
                                                            <Link href={item.url} className="flex items-center gap-3 px-4 py-2.5 rounded-none hover:bg-violet-500/10 transition-colors border-b border-white/5 last:border-0">
                                                                <span className={`shrink-0 w-6 h-6 rounded flex items-center justify-center text-xs font-bold ${
                                                                    item.type === 'artist' ? 'bg-pink-500/20 text-pink-400' :
                                                                    item.type === 'city' ? 'bg-emerald-500/20 text-emerald-400' :
                                                                    'bg-violet-500/20 text-violet-400'
                                                                }`}>{item.type === 'artist' ? '♪' : item.type === 'city' ? '📍' : '🎫'}</span>
                                                                <div className="min-w-0 flex-1">
                                                                    <div className="text-sm text-white font-medium truncate">{item.title}</div>
                                                                    <div className="text-xs text-zinc-500 truncate">{[item.artist, item.date, item.venue, item.subtitle === 'Артист' ? 'Артист' : null, item.subtitle === 'Город' ? 'Город' : null].filter(Boolean).join(' · ')}</div>
                                                                </div>
                                                            </Link>
                                                        </li>
                                                    ))
                                                )}
                                            </ul>
                                        </div>
                                    )}
                                </form>

                                <div className="flex items-center gap-2 shrink-0">
                                    {auth.user && (
                                        <div className="relative" ref={notifRef}>
                                            <button type="button" onClick={() => { setNotifOpen(!notifOpen); if (!notifOpen) fetchNotifications(); }}
                                                className="relative w-9 h-9 rounded-lg bg-white/[0.06] border border-white/10 hover:border-violet-500/30 flex items-center justify-center text-zinc-400 hover:text-white transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 0 1-3.46 0" />
                                                </svg>
                                                {notifCount > 0 && (
                                                    <span className="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center">{notifCount > 9 ? '9+' : notifCount}</span>
                                                )}
                                            </button>
                                            {notifOpen && (
                                                <div className="absolute right-0 top-full mt-1.5 w-80 max-h-96 overflow-y-auto rounded-xl border border-white/10 bg-[#111] shadow-2xl shadow-black/50 z-50 scrollbar-thin">
                                                    <div className="flex items-center justify-between px-4 py-3 border-b border-white/10">
                                                        <p className="text-xs font-bold text-zinc-400 uppercase tracking-wider">Уведомления</p>
                                                        {notifCount > 0 && (
                                                            <button type="button" onClick={markAllRead} className="text-[10px] text-violet-400 hover:text-violet-300 font-bold uppercase">Прочитать все</button>
                                                        )}
                                                    </div>
                                                    {notifItems.length === 0 ? (
                                                        <div className="px-4 py-6 text-center text-sm text-zinc-600">Нет уведомлений</div>
                                                    ) : (
                                                        <div className="py-1">
                                                            {notifItems.map(n => (
                                                                <a key={n.id} href={n.url || '#'} className={`block px-4 py-3 hover:bg-violet-500/10 transition-colors border-l-2 ${n.read ? 'border-transparent' : 'border-violet-500'}`}>
                                                                    <p className="text-sm text-white font-medium">{n.title}</p>
                                                                    {n.body && <p className="text-xs text-zinc-500 mt-0.5">{n.body}</p>}
                                                                    <p className="text-[10px] text-zinc-600 mt-1">{n.time}</p>
                                                                </a>
                                                            ))}
                                                        </div>
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    )}
                                    <div className="relative" ref={cityRef}>
                                        <button
                                            type="button"
                                            onClick={() => setCityOpen(!cityOpen)}
                                            className="flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white/[0.06] border border-white/10 hover:border-violet-500/30 text-sm text-zinc-400 hover:text-white transition-colors"
                                        >
                                            <span className="text-xs">📍</span>
                                            <span className="max-w-[90px] truncate">{currentCityName}</span>
                                            <span className="text-zinc-600 text-xs">▼</span>
                                        </button>
                                        {cityOpen && (
                                            <div className="absolute right-0 top-full mt-1.5 w-56 max-h-72 overflow-y-auto rounded-xl border border-white/10 bg-[#111] shadow-2xl shadow-black/50 py-1 z-50 scrollbar-thin">
                                                <div className="px-3 pb-2 pt-2">
                                                    <input
                                                        type="search"
                                                        value={citySearch}
                                                        onChange={(e) => setCitySearch(e.target.value)}
                                                        placeholder="Поиск города"
                                                        className="w-full rounded-lg bg-white/[0.06] border border-white/10 px-3 py-2 text-sm text-white placeholder-zinc-600 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20"
                                                    />
                                                </div>
                                                {headerCities
                                                    .filter(c => c.name.toLowerCase().includes(citySearch.toLowerCase()))
                                                    .map(c => (
                                                        <Link
                                                            key={c.slug}
                                                            href={route('events.index', { ...ziggy.query, city: c.slug })}
                                                            className={`block px-4 py-2.5 hover:bg-violet-500/10 text-sm text-zinc-400 hover:text-white ${ziggy.query.city === c.slug ? 'bg-violet-500/10 text-white' : ''}`}
                                                        >
                                                            {c.name}
                                                        </Link>
                                                    ))
                                                }
                                            </div>
                                        )}
                                    </div>

                                    {auth.user ? (
                                        <div className="relative" ref={authRef}>
                                            <button
                                                type="button"
                                                onClick={() => setAuthOpen(!authOpen)}
                                                className="w-9 h-9 rounded-lg overflow-hidden bg-violet-600/30 border border-violet-500/30 font-bold text-sm flex items-center justify-center text-white shrink-0"
                                            >
                                                {auth.user.avatar_src ? (
                                                    auth.user.avatar_is_video ? (
                                                        <video src={auth.user.avatar_src} autoPlay loop muted playsInline className="w-full h-full object-cover" />
                                                    ) : (
                                                        <img src={auth.user.avatar_src} alt="" className="w-full h-full object-cover" />
                                                    )
                                                ) : (
                                                    auth.user.name.substring(0, 1)
                                                )}
                                            </button>
                                            {authOpen && (
                                                <div className="absolute right-0 top-full mt-1.5 w-64 rounded-xl border border-white/10 bg-[#111] shadow-2xl shadow-black/50 py-2 z-50">
                                                    <div className="px-4 py-2 border-b border-white/10">
                                                        <p className="font-bold text-white truncate">{auth.user.name}</p>
                                                        <p className="text-xs text-zinc-500 truncate">{auth.user.email}</p>
                                                    </div>
                                                    <nav className="py-1">
                                                        {(auth.user.is_admin || auth.user.role === 'organizer') && (
                                                            <Link href={route('admin.events.index')} className="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                                <span className="w-5 text-center text-xs">⚙</span> {auth.user.is_admin ? 'Админ-панель' : 'Мои мероприятия'}
                                                            </Link>
                                                        )}
                                                        {auth.user.role === 'artist' && auth.user.artist_id && (
                                                            <Link href={route('cabinet.artist')} className="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                                <span className="w-5 text-center text-xs">♪</span> Профиль артиста
                                                            </Link>
                                                        )}
                                                        <Link href={route('cabinet.favorites')} className="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                            <span className="w-5 text-center text-xs">♥</span> Избранное
                                                        </Link>
                                                        <Link href={route('cabinet.index')} className="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                            <span className="w-5 text-center text-xs">🎫</span> Мои билеты
                                                        </Link>
                                                        <Link href={route('cabinet.index') + '#city'} className="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                            <span className="w-5 text-center text-xs">📍</span> Город
                                                        </Link>
                                                        <Link href={route('cabinet.index') + '#tickets'} className="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                            <span className="w-5 text-center text-xs">↩</span> Вернуть билет
                                                        </Link>
                                                        <Link href={route('cabinet.account')} className="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors">
                                                            <span className="w-5 text-center text-xs">👤</span> Аккаунт
                                                        </Link>
                                                    </nav>
                                                    <div className="border-t border-white/10 mt-1 pt-1">
                                                        <Link href={route('logout')} method="post" as="button" className="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-zinc-400 hover:bg-violet-500/10 hover:text-white transition-colors text-left">
                                                            <span className="w-5 text-center text-xs">→</span> Выйти
                                                        </Link>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    ) : (
                                        <>
                                            <Link href={route('login')} className="px-3 py-2 rounded-lg text-sm text-zinc-400 hover:text-white transition-colors">Войти</Link>
                                            <Link href={route('register')} className="btn-primary text-xs">Регистрация</Link>
                                        </>
                                    )}
                                </div>
                            </div>
                        </div>
                    </header>

                    <div className="px-4 lg:px-8">
                        {flash.status && (
                            <div className="mt-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 text-emerald-200 px-4 py-3 text-sm">
                                {flash.status}
                            </div>
                        )}
                        {Object.keys(flash.errors).length > 0 && (
                            <div className="mt-4 rounded-lg border border-red-500/30 bg-red-500/10 text-red-200 px-4 py-3 text-sm">
                                {Object.values(flash.errors)[0]}
                            </div>
                        )}
                    </div>

                    <main className="flex-1 px-4 lg:px-8 py-8">
                        {children}
                    </main>

                    <footer className="border-t border-white/10 py-6 mt-auto">
                        <div className="px-4 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-600">
                            <div className="flex items-center gap-3">
                                <div className="checkerboard-sm w-6 h-6 opacity-40"></div>
                                <span className="font-bold text-zinc-400 uppercase tracking-wider">OnTheRise</span>
                            </div>
                            <span>{new Date().getFullYear()} · Платформа для артистов и мероприятий</span>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    );
}
