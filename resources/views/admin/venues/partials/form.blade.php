@if($errors->any())
    <div class="mb-6 rounded-2xl border border-rose-500/40 bg-rose-500/10 text-sm text-rose-100 px-5 py-4 space-y-1">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ $route }}" class="space-y-6 bg-slate-900/60 border border-slate-800 rounded-2xl p-6">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <label class="text-xs uppercase text-slate-500 tracking-wide">Название
            <input type="text" name="name" value="{{ old('name', $venue->name) }}" required class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
        </label>
        <label class="text-xs uppercase text-slate-500 tracking-wide">Slug
            <input type="text" name="slug" value="{{ old('slug', $venue->slug) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
        </label>
        <label class="text-xs uppercase text-slate-500 tracking-wide">Город
            <input type="text" name="city" value="{{ old('city', $venue->city) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
        </label>
        <label class="text-xs uppercase text-slate-500 tracking-wide">Адрес
            <input type="text" name="address" value="{{ old('address', $venue->address) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
        </label>
    </div>

    <label class="text-xs uppercase text-slate-500 tracking-wide block">Описание
        <textarea name="description" rows="5" class="mt-1 w-full rounded-2xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">{{ old('description', $venue->description) }}</textarea>
    </label>

    <div class="grid grid-cols-2 gap-4">
        <label class="text-xs uppercase text-slate-500 tracking-wide">Макс. вместимость
            <input type="number" name="max_capacity" min="0" value="{{ old('max_capacity', $venue->max_capacity) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
        </label>
        <label class="text-xs uppercase text-slate-500 tracking-wide">Тип зала
            <input type="text" name="layout_type" value="{{ old('layout_type', $venue->layout_type ?? 'arena') }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
        </label>
    </div>

    <label class="text-xs uppercase text-slate-500 tracking-wide block">Настройки зала (JSON)
        <textarea name="layout_config" rows="4" class="mt-1 w-full rounded-2xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" placeholder='{"width":900,"height":600}'>{{ old('layout_config', $venue->layout_config ? json_encode($venue->layout_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
    </label>

    <div class="flex justify-end">
        <button type="submit" class="px-6 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 transition text-white font-semibold">Сохранить площадку</button>
    </div>
</form>

