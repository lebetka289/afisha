@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.artists.index') }}" class="text-sm text-slate-400 hover:text-white transition">Назад к списку</a>
        <h1 class="text-3xl font-semibold mt-2">Новый исполнитель</h1>
    </div>

    @include('admin.artists.partials.form', ['route' => route('admin.artists.store'), 'method' => 'POST'])
@endsection
