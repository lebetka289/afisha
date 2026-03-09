@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-sm uppercase text-slate-500 tracking-[0.3em]">Админ / Конструктор</p>
            <h1 class="text-3xl font-semibold mt-1">Редактирование: {{ $event->title }}</h1>
        </div>
        <a href="{{ route('admin.events.index') }}" class="text-sm text-slate-400 hover:text-white transition">Назад к списку</a>
    </div>

    @include('admin.events.partials.form', ['route' => route('admin.events.update', $event), 'method' => 'PUT'])
@endsection

