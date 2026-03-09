@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-sm uppercase text-slate-500 tracking-[0.3em]">Админ / Площадки</p>
            <h1 class="text-3xl font-semibold mt-1">Редактирование: {{ $venue->name }}</h1>
        </div>
        <a href="{{ route('admin.venues.index') }}" class="text-sm text-slate-400 hover:text-white transition">Назад</a>
    </div>

    @include('admin.venues.partials.form', ['route' => route('admin.venues.update', $venue), 'method' => 'PUT'])
@endsection

