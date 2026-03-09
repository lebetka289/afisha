@if($errors->any())
    <div class="mb-6 rounded-2xl border border-rose-500/40 bg-rose-500/10 text-sm text-rose-100 px-5 py-4 space-y-1">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ $route }}" enctype="multipart/form-data" class="space-y-6 bg-slate-900/60 border border-slate-800 rounded-2xl p-6">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <label class="text-xs uppercase text-slate-500 tracking-wide">Имя исполнителя
            <input type="text" name="name" value="{{ old('name', $artist->name) }}" required class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
        </label>
        <label class="text-xs uppercase text-slate-500 tracking-wide">Slug
            <input type="text" name="slug" value="{{ old('slug', $artist->slug) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
        </label>
    </div>

    <label class="text-xs uppercase text-slate-500 tracking-wide block">Описание
        <textarea name="description" rows="6" class="mt-1 w-full rounded-2xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">{{ old('description', $artist->description) }}</textarea>
    </label>

    <div class="space-y-3">
        <label class="text-xs uppercase text-slate-500 tracking-wide block">Фото исполнителя
            <input type="file" name="photo" accept="image/png,image/jpeg,image/gif,image/webp" class="mt-1 block w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
        </label>
        @if($artist->photo)
            <img src="{{ route('media.show', ['path' => $artist->photo]) }}" alt="" class="w-32 h-32 rounded-2xl object-cover border border-slate-800">
        @endif
    </div>

    <label class="text-xs uppercase text-slate-500 tracking-wide block">Ссылки на площадки / соцсети (JSON)
        <textarea name="links_json" rows="5" class="mt-1 w-full rounded-2xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" placeholder='[{"title":"VK","url":"https://vk.com/artist"},{"title":"YouTube","url":"https://youtube.com/..."}]'>{{ old('links_json', $artist->links ? json_encode($artist->links, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
    </label>

    <div class="flex justify-end">
        <button type="submit" class="px-6 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 transition text-white font-semibold">Сохранить исполнителя</button>
    </div>
</form>
