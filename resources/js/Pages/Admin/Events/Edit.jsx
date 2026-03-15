import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Link } from '@inertiajs/react';
import EventForm from './Partials/EventForm';

export default function Edit({ event, venues, artists }) {
    return (
        <AppLayout title={`Редактирование: ${event.title}`}>
            <div className="flex items-center justify-between mb-8">
                <div>
                    <p className="text-sm uppercase text-slate-500 tracking-[0.3em]">Админ / Конструктор</p>
                    <h1 className="text-3xl font-semibold mt-1">Редактирование: {event.title}</h1>
                </div>
                <Link href={route('admin.events.index')} className="text-sm text-slate-400 hover:text-white transition">Назад к списку</Link>
            </div>

            <EventForm 
                event={event} 
                venues={venues} 
                artists={artists} 
                action={route('admin.events.update', event.id)} 
                method="PUT"
            />
        </AppLayout>
    );
}
