import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Link } from '@inertiajs/react';
import ArtistForm from './Partials/ArtistForm';

export default function Edit({ artist }) {
    return (
        <AppLayout title="Редактировать исполнителя">
            <div className="mb-8">
                <Link href={route('admin.artists.index')} className="text-sm text-slate-400 hover:text-white transition">Назад к списку</Link>
                <h1 className="text-3xl font-semibold mt-2">Редактировать исполнителя</h1>
            </div>

            <ArtistForm artist={artist} method="PUT" route={route('admin.artists.update', artist.id)} />
        </AppLayout>
    );
}
