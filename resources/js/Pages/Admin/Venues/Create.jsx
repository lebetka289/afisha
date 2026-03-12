import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Link } from '@inertiajs/react';
import VenueForm from './Partials/VenueForm';

export default function Create({ venue }) {
    return (
        <AppLayout title="Новая площадка">
            <div className="flex items-center justify-between mb-8">
                <div>
                    <p className="text-sm uppercase text-slate-500 tracking-[0.3em]">Админ / Площадки</p>
                    <h1 className="text-3xl font-semibold mt-1">Новая площадка</h1>
                </div>
                <Link href={route('admin.venues.index')} className="text-sm text-slate-400 hover:text-white transition">Назад</Link>
            </div>

            <VenueForm venue={venue} method="POST" route={route('admin.venues.store')} />
        </AppLayout>
    );
}
