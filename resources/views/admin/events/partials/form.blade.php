@php
    $addonsArray = $event->addons->map(fn ($a) => ['id' => $a->id, 'name' => $a->name, 'price' => (float) $a->price, 'description' => $a->description])->values()->all();
    $addonsJson = old('addons_payload', json_encode($addonsArray, JSON_UNESCAPED_UNICODE));
    $sectionArray = $event->sections->map(function ($section) {
        $seatMap = [];
        foreach ($section->seats as $seat) {
            $seatMap[$seat->row_number][$seat->col_number] = [
                'status' => $seat->status,
                'label' => $seat->label,
                'price' => (float) $seat->price,
            ];
        }

        return [
            'id' => $section->id,
            'name' => $section->name,
            'type' => $section->type,
            'color' => $section->color,
            'seating_mode' => $section->seating_mode,
            'capacity' => $section->capacity,
            'price' => (float) $section->price,
            'rows' => $section->rows,
            'cols' => $section->cols,
            'seat_map' => $seatMap,
            'position' => $section->position ?? ['x' => 10, 'y' => 10, 'width' => 20, 'height' => 15],
            'meta' => $section->meta,
            'sort_order' => $section->sort_order,
        ];
    });
    $sectionsJson = old('sections_payload', $sectionArray->toJson(JSON_UNESCAPED_UNICODE));
@endphp

@if($errors->any())
    <div class="mb-6 rounded-2xl border border-rose-500/40 bg-rose-500/10 text-sm text-rose-100 px-5 py-4 space-y-1">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ $route }}" enctype="multipart/form-data" class="space-y-10">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-lg font-semibold">Основная информация</h2>
            <label class="block text-sm text-slate-400 uppercase tracking-wide">Название
                <input type="text" name="title" value="{{ old('title', $event->title) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" required>
            </label>
            <label class="block text-sm text-slate-400 uppercase tracking-wide">Подзаголовок
                <input type="text" name="subtitle" value="{{ old('subtitle', $event->subtitle) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
            </label>
            <label class="block text-sm text-slate-400 uppercase tracking-wide">Категория
                <select name="category" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
                    @foreach(['concert' => 'Концерты', 'theater' => 'Театр', 'show' => 'Шоу', 'standup' => 'Стендап'] as $key => $label)
                        <option value="{{ $key }}" @selected(old('category', $event->category ?? 'concert') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm text-slate-400 uppercase tracking-wide">Slug / ссылка
                <input type="text" name="slug" value="{{ old('slug', $event->slug) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
            </label>
            <label class="block text-sm text-slate-400 uppercase tracking-wide">Площадка
                <select name="venue_id" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
                    <option value="">— Не указано —</option>
                    @foreach($venues as $venue)
                        <option value="{{ $venue->id }}" @selected(old('venue_id', $event->venue_id) == $venue->id)>{{ $venue->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm text-slate-400 uppercase tracking-wide">Исполнитель
                <select name="artist_id" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
                    <option value="">— Не указано —</option>
                    @foreach(($artists ?? collect()) as $artist)
                        <option value="{{ $artist->id }}" @selected(old('artist_id', $event->artist_id) == $artist->id)>{{ $artist->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm text-slate-400 uppercase tracking-wide">Статус
                <select name="status" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0" required>
                    @foreach(['draft' => 'Черновик', 'published' => 'Опубликовано', 'archived' => 'Архив'] as $key => $label)
                        <option value="{{ $key }}" @selected(old('status', $event->status ?? 'draft') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <div class="grid grid-cols-2 gap-4">
                <label class="block text-sm text-slate-400 uppercase tracking-wide">Дата начала
                    <input type="datetime-local" name="start_at" value="{{ old('start_at', optional($event->start_at)->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
                </label>
                <label class="block text-sm text-slate-400 uppercase tracking-wide">Дата окончания
                    <input type="datetime-local" name="end_at" value="{{ old('end_at', optional($event->end_at)->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
                </label>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <label class="block text-sm text-slate-400 uppercase tracking-wide">Старт продаж
                    <input type="datetime-local" name="sales_start_at" value="{{ old('sales_start_at', optional($event->sales_start_at)->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
                </label>
                <label class="block text-sm text-slate-400 uppercase tracking-wide">Закрытие продаж
                    <input type="datetime-local" name="sales_end_at" value="{{ old('sales_end_at', optional($event->sales_end_at)->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
                </label>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <label class="block text-sm text-slate-400 uppercase tracking-wide">Постер / баннер (URL)
                    <input type="text" name="poster_url" value="{{ old('poster_url', filter_var($event->poster_url, FILTER_VALIDATE_URL) ? $event->poster_url : '') }}" placeholder="https://example.com/poster.jpg" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
                </label>
                <label class="block text-sm text-slate-400 uppercase tracking-wide">Макс. билетов
                    <input type="number" name="max_tickets" min="0" value="{{ old('max_tickets', $event->max_tickets) }}" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
                </label>
            </div>
            <label class="block text-sm text-slate-400 uppercase tracking-wide">Загрузить фото / GIF / видео
                <input type="file" name="poster_upload" accept="image/png,image/jpeg,image/gif,image/webp,video/mp4,video/webm,video/quicktime" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">
            </label>
            @if($event->poster_src)
                <div class="rounded-2xl overflow-hidden border border-slate-800 bg-slate-950">
                    @if($event->poster_is_video)
                        <video src="{{ $event->poster_src }}" controls class="w-full max-h-72 object-cover bg-black"></video>
                    @else
                        <img src="{{ $event->poster_src }}" alt="" class="w-full max-h-72 object-cover">
                    @endif
                </div>
            @endif
        </div>
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-lg font-semibold">Описание</h2>
            <textarea name="description" rows="12" class="w-full rounded-2xl bg-slate-900 border border-slate-800 px-3 py-2 text-white focus:border-indigo-500 focus:ring-0">{{ old('description', $event->description) }}</textarea>
            <input type="hidden" name="layout_type" value="custom">
        </div>
    </section>

    <section class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-6" data-sections-builder data-initial="{{ e($sectionsJson) }}">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase text-slate-500 tracking-[0.3em]">Конструктор</p>
                <h2 class="text-2xl font-semibold">Зонирование зала</h2>
            </div>
            <button type="button" data-reset-selection class="text-sm text-slate-400 hover:text-white transition">Сбросить выделение</button>
        </div>

        <div class="grid gap-8 lg:grid-cols-[2fr,1fr]">
            <div>
                <p class="text-xs text-slate-500 mb-2">Нарисуйте зону мышью или перетащите существующую. Выделите зону и тяните за углы для изменения размера.</p>
                <div class="relative bg-slate-950 rounded-2xl border border-slate-800 aspect-[4/3] overflow-hidden select-none" data-hall-canvas>
                    <div class="absolute inset-0 pointer-events-none border border-dashed border-slate-800 rounded-2xl m-2"></div>
                    <div class="absolute top-2 left-1/2 -translate-x-1/2 px-6 py-2 rounded-full text-xs tracking-[0.3em] bg-slate-800 border border-slate-700 text-slate-200 pointer-events-none">СЦЕНА</div>
                    <div class="absolute inset-0 z-0" data-hall-draw-area></div>
                </div>
            </div>

            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 space-y-3" data-section-form>
                <input type="hidden" name="section_id" value="">
                <div class="grid gap-3">
                    <label class="text-xs uppercase text-slate-500 tracking-wide">Название зоны
                        <input type="text" name="section_name" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" placeholder="Например: VIP ложа">
                    </label>
                    <label class="text-xs uppercase text-slate-500 tracking-wide">Тип
                        <select name="section_type" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
                            <option value="vip">VIP</option>
                            <option value="dancefloor">Танцпол</option>
                            <option value="balcony">Балкон</option>
                            <option value="standard">Стандарт</option>
                        </select>
                    </label>
                    <label class="text-xs uppercase text-slate-500 tracking-wide">Цвет
                        <input type="color" name="section_color" value="#3b82f6" class="mt-1 h-10 w-full rounded-xl bg-slate-900 border border-slate-800">
                    </label>
                    <label class="text-xs uppercase text-slate-500 tracking-wide">Режим
                        <select name="section_mode" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
                            <option value="standing">Зона без мест (танцпол)</option>
                            <option value="seated">Сидячие места</option>
                        </select>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="text-xs uppercase text-slate-500 tracking-wide">Вместимость
                            <input type="number" name="section_capacity" min="0" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" placeholder="Например 200">
                        </label>
                        <label class="text-xs uppercase text-slate-500 tracking-wide">Цена, ₽
                            <input type="number" name="section_price" min="0" step="100" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-3 seated-only hidden">
                        <label class="text-xs uppercase text-slate-500 tracking-wide">Рядов
                            <input type="number" name="section_rows" min="0" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
                        </label>
                        <label class="text-xs uppercase text-slate-500 tracking-wide">Мест в ряду
                            <input type="number" name="section_cols" min="0" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="text-xs uppercase text-slate-500 tracking-wide">Позиция X
                            <input type="number" name="section_pos_x" min="0" max="100" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white" placeholder="0-100%">
                        </label>
                        <label class="text-xs uppercase text-slate-500 tracking-wide">Позиция Y
                            <input type="number" name="section_pos_y" min="0" max="100" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
                        </label>
                        <label class="text-xs uppercase text-slate-500 tracking-wide">Ширина %
                            <input type="number" name="section_width" min="5" max="100" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
                        </label>
                        <label class="text-xs uppercase text-slate-500 tracking-wide">Высота %
                            <input type="number" name="section_height" min="5" max="100" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-800 px-3 py-2 text-white">
                        </label>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="button" data-save-section class="w-full bg-indigo-500 hover:bg-indigo-400 text-white font-semibold rounded-xl py-2 transition">Добавить зону</button>
                        <button type="button" data-new-section class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl py-2 transition hidden">+ Создать новую зону</button>
                        <button type="button" data-delete-section class="w-full py-2 rounded-xl border border-rose-500/50 text-rose-200 text-sm hidden">Удалить зону</button>
                    </div>
                    <p class="text-xs text-amber-400/70 hidden" data-price-sync-hint>Цена синхронизируется между зонами с одинаковым типом</p>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2" data-sections-list></div>

        <input type="hidden" id="sectionsPayloadInput" name="sections_payload" value="{{ e($sectionsJson) }}">
    </section>

    <section class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4" data-addons-builder data-initial="{{ e($addonsJson) }}">
        <h2 class="text-lg font-semibold">Дополнения к заказу</h2>
        <p class="text-sm text-slate-400">Дополнительные опции, которые пользователь может добавить при покупке билета (Meet & Greet, мерч и т.д.)</p>
        <div class="space-y-3" data-addons-list></div>
        <button type="button" data-add-addon class="px-4 py-2 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 text-sm">+ Добавить дополнение</button>
        <input type="hidden" id="addonsPayloadInput" name="addons_payload" value="{{ e($addonsJson) }}">
    </section>

    <div class="flex justify-end">
        <button type="submit" class="px-6 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 transition text-white font-semibold">Сохранить событие</button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-sections-builder]').forEach(builderEl => {
        const canvas = builderEl.querySelector('[data-hall-canvas]');
        const listContainer = builderEl.querySelector('[data-sections-list]');
        const form = builderEl.querySelector('[data-section-form]');
        const saveBtn = form.querySelector('[data-save-section]');
        const newBtn = form.querySelector('[data-new-section]');
        const deleteBtn = form.querySelector('[data-delete-section]');
        const resetBtn = builderEl.querySelector('[data-reset-selection]');
        const hiddenInput = document.getElementById('sectionsPayloadInput');
        const modeSelect = form.querySelector('[name="section_mode"]');
        const seatedOnlyFields = form.querySelector('.seated-only');
        const priceSyncHint = form.querySelector('[data-price-sync-hint]');

        let sections = [];
        let editingIndex = null;

        try {
            sections = JSON.parse(hiddenInput.value || builderEl.dataset.initial || '[]') || [];
        } catch (e) {
            sections = [];
        }

        function toggleSeatFields(mode) {
            if (mode === 'seated') {
                seatedOnlyFields.classList.remove('hidden');
            } else {
                seatedOnlyFields.classList.add('hidden');
            }
        }

        function syncHiddenInput() {
            hiddenInput.value = JSON.stringify(sections);
        }

        function getCanvasRect() {
            return canvas.getBoundingClientRect();
        }

        function percentFromEvent(e) {
            const r = getCanvasRect();
            const x = ((e.clientX - r.left) / r.width) * 100;
            const y = ((e.clientY - r.top) / r.height) * 100;
            return { x: Math.max(0, Math.min(100, x)), y: Math.max(0, Math.min(100, y)) };
        }

        let drawPreview = null;
        let dragState = null;
        let resizeState = null;
        const drawArea = canvas.querySelector('[data-hall-draw-area]');

        function renderCanvas() {
            canvas.querySelectorAll('[data-section-block]').forEach(el => el.remove());
            sections.forEach((section, index) => {
                const block = document.createElement('div');
                block.dataset.sectionBlock = 'true';
                block.dataset.sectionIndex = String(index);
                const pos = section.position || { x: 10, y: 10, width: 20, height: 15 };
                block.className = 'absolute rounded-xl border-2 cursor-move transition z-10 ' + (editingIndex === index ? 'ring-2 ring-amber-400 ring-offset-2 ring-offset-slate-950' : 'border-white/20 hover:border-white/40');
                block.style.left = `${pos.x}%`;
                block.style.top = `${pos.y}%`;
                block.style.width = `${pos.width}%`;
                block.style.height = `${pos.height}%`;
                block.style.background = `${section.color || '#3b82f6'}33`;
                block.style.borderColor = section.color || '#3b82f6';

                const label = document.createElement('div');
                label.className = 'absolute inset-0 flex items-center justify-center text-xs font-semibold text-white text-center px-2 pointer-events-none';
                label.textContent = section.name || 'Зона';

                block.appendChild(label);

                if (editingIndex === index) {
                    const handles = ['nw','n','ne','e','se','s','sw','w'];
                    handles.forEach(h => {
                        const handle = document.createElement('div');
                        handle.dataset.resizeHandle = h;
                        handle.className = 'absolute w-2.5 h-2.5 bg-amber-400 rounded border border-amber-600 cursor-' + (h.includes('n') || h.includes('s') ? 'ns' : 'ew') + '-resize z-20';
                        const isN = h.includes('n'), isS = h.includes('s'), isW = h.includes('w'), isE = h.includes('e');
                        if (isN) handle.style.top = '0'; if (isS) handle.style.bottom = '0';
                        if (isW) handle.style.left = '0'; if (isE) handle.style.right = '0';
                        if (h === 'n' || h === 's') { handle.style.left = '50%'; handle.style.transform = 'translateX(-50%)'; }
                        if (h === 'e' || h === 'w') { handle.style.top = '50%'; handle.style.transform = 'translateY(-50%)'; }
                        block.appendChild(handle);
                    });
                }

                block.addEventListener('mousedown', (e) => {
                    if (e.target.closest('[data-resize-handle]')) return;
                    e.preventDefault();
                    dragState = { index, startPos: { ...(section.position || { x:10,y:10,width:20,height:15 }) }, startMouse: percentFromEvent(e), moved: false };
                    const onMove = (e2) => {
                        if (!dragState || dragState.index !== index) return;
                        dragState.moved = true;
                        const cur = percentFromEvent(e2);
                        const dx = cur.x - dragState.startMouse.x, dy = cur.y - dragState.startMouse.y;
                        const pos = sections[index].position || { x:10, y:10, width:20, height:15 };
                        pos.x = Math.max(0, Math.min(100 - pos.width, pos.x + dx));
                        pos.y = Math.max(0, Math.min(100 - pos.height, pos.y + dy));
                        sections[index].position = pos;
                        updateBlockPosition(index);
                        fillForm(sections[index]);
                        syncHiddenInput();
                        dragState.startMouse = cur;
                    };
                    const onUp = () => {
                        if (dragState && !dragState.moved) startEdit(index);
                        dragState = null;
                        document.removeEventListener('mousemove', onMove);
                        document.removeEventListener('mouseup', onUp);
                    };
                    document.addEventListener('mousemove', onMove);
                    document.addEventListener('mouseup', onUp);
                });
                block.querySelectorAll('[data-resize-handle]').forEach(handleEl => {
                    handleEl.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        const pos = sections[index].position || { x:10, y:10, width:20, height:15 };
                        resizeState = { index, handle: handleEl.dataset.resizeHandle, start: { ...pos }, startMouse: percentFromEvent(e) };
                        const onMove = (e2) => {
                            if (!resizeState || resizeState.index !== index) return;
                            const cur = percentFromEvent(e2);
                            const h = resizeState.handle;
                            let { x, y, width, height } = { ...resizeState.start };
                            if (h.includes('e')) width = Math.max(5, cur.x - x);
                            if (h.includes('w')) { const x2 = x + width; x = Math.min(cur.x, x2 - 5); width = x2 - x; }
                            if (h.includes('s')) height = Math.max(5, cur.y - y);
                            if (h.includes('n')) { const y2 = y + height; y = Math.min(cur.y, y2 - 5); height = y2 - y; }
                            x = Math.max(0, Math.min(100 - width, x));
                            y = Math.max(0, Math.min(100 - height, y));
                            sections[index].position = { x, y, width, height };
                            updateBlockPosition(index);
                            fillForm(sections[index]);
                            syncHiddenInput();
                        };
                        const onUp = () => {
                            resizeState = null;
                            document.removeEventListener('mousemove', onMove);
                            document.removeEventListener('mouseup', onUp);
                        };
                        document.addEventListener('mousemove', onMove);
                        document.addEventListener('mouseup', onUp);
                    });
                });
                canvas.appendChild(block);
            });
        }

        function updateBlockPosition(index) {
            const block = canvas.querySelector(`[data-section-block][data-section-index="${index}"]`);
            if (!block) return;
            const section = sections[index];
            const pos = section.position || { x: 10, y: 10, width: 20, height: 15 };
            block.style.left = `${pos.x}%`;
            block.style.top = `${pos.y}%`;
            block.style.width = `${pos.width}%`;
            block.style.height = `${pos.height}%`;
        }

        function renderList() {
            listContainer.innerHTML = '';
            if (!sections.length) {
                listContainer.innerHTML = '<div class="col-span-full text-center text-slate-500 text-sm border border-dashed border-slate-800 rounded-2xl py-6">Добавьте первую зону</div>';
                return;
            }

            sections.forEach((section, index) => {
                const card = document.createElement('div');
                card.className = 'rounded-2xl border border-slate-800 bg-slate-950/80 p-4 space-y-2 cursor-pointer hover:border-indigo-500/50 transition';
                card.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="font-semibold text-white">${section.name}</div>
                        <span class="text-xs uppercase tracking-wide text-slate-500">${section.type}</span>
                    </div>
                    <div class="text-sm text-slate-400">
                        Режим: ${section.seating_mode === 'seated' ? 'Сидячие' : 'Зона'} · Цена: ${section.price ?? 0} ₽
                    </div>
                `;
                card.addEventListener('click', () => startEdit(index));
                listContainer.appendChild(card);
            });
        }

        function readFormValues() {
            const pos = {
                x: Number(form.querySelector('[name="section_pos_x"]').value) || 10,
                y: Number(form.querySelector('[name="section_pos_y"]').value) || 10,
                width: Number(form.querySelector('[name="section_width"]').value) || 20,
                height: Number(form.querySelector('[name="section_height"]').value) || 15,
            };

            return {
                id: form.querySelector('[name="section_id"]').value || null,
                name: form.querySelector('[name="section_name"]').value || 'Безымянная зона',
                type: form.querySelector('[name="section_type"]').value || 'standard',
                color: form.querySelector('[name="section_color"]').value || '#3b82f6',
                seating_mode: form.querySelector('[name="section_mode"]').value || 'standing',
                capacity: Number(form.querySelector('[name="section_capacity"]').value) || 0,
                price: Number(form.querySelector('[name="section_price"]').value) || 0,
                rows: Number(form.querySelector('[name="section_rows"]').value) || 0,
                cols: Number(form.querySelector('[name="section_cols"]').value) || 0,
                position: pos,
                sort_order: editingIndex ?? sections.length,
            };
        }

        function fillForm(section) {
            form.querySelector('[name="section_id"]').value = section.id || '';
            form.querySelector('[name="section_name"]').value = section.name || '';
            form.querySelector('[name="section_type"]').value = section.type || 'standard';
            form.querySelector('[name="section_color"]').value = section.color || '#3b82f6';
            form.querySelector('[name="section_mode"]').value = section.seating_mode || 'standing';
            form.querySelector('[name="section_capacity"]').value = section.capacity ?? '';
            form.querySelector('[name="section_price"]').value = section.price ?? '';
            form.querySelector('[name="section_rows"]').value = section.rows ?? '';
            form.querySelector('[name="section_cols"]').value = section.cols ?? '';
            form.querySelector('[name="section_pos_x"]').value = section.position?.x ?? 10;
            form.querySelector('[name="section_pos_y"]').value = section.position?.y ?? 10;
            form.querySelector('[name="section_width"]').value = section.position?.width ?? 20;
            form.querySelector('[name="section_height"]').value = section.position?.height ?? 15;
            toggleSeatFields(section.seating_mode);
        }

        function syncPricesByType(type, price) {
            let synced = false;
            sections.forEach((s, i) => {
                if (s.type === type) {
                    s.price = price;
                    synced = true;
                }
            });
            if (synced) syncHiddenInput();
            return synced;
        }

        function hasOtherSectionsOfType(type, excludeIndex) {
            return sections.some((s, i) => i !== excludeIndex && s.type === type);
        }

        function getPriceForType(type) {
            const existing = sections.find(s => s.type === type);
            return existing ? existing.price : null;
        }

        function updatePriceSyncHint(type) {
            if (priceSyncHint) {
                const count = sections.filter(s => s.type === type).length;
                if (count > 1 || (editingIndex === null && count > 0)) {
                    priceSyncHint.classList.remove('hidden');
                } else {
                    priceSyncHint.classList.add('hidden');
                }
            }
        }

        function resetForm() {
            editingIndex = null;
            form.reset();
            deleteBtn.classList.add('hidden');
            newBtn.classList.add('hidden');
            saveBtn.textContent = 'Добавить зону';
            if (priceSyncHint) priceSyncHint.classList.add('hidden');
            toggleSeatFields(form.querySelector('[name="section_mode"]').value);
            renderCanvas();
        }

        function startEdit(index) {
            editingIndex = index;
            fillForm(sections[index]);
            deleteBtn.classList.remove('hidden');
            newBtn.classList.remove('hidden');
            saveBtn.textContent = 'Сохранить изменения';
            updatePriceSyncHint(sections[index].type);
            renderCanvas();
        }

        saveBtn.addEventListener('click', () => {
            const payload = readFormValues();
            if (editingIndex !== null) {
                sections[editingIndex] = { ...sections[editingIndex], ...payload };
                syncPricesByType(payload.type, payload.price);
                syncHiddenInput();
                renderCanvas();
                renderList();
                startEdit(editingIndex);
            } else {
                const existingPrice = getPriceForType(payload.type);
                if (existingPrice !== null) {
                    payload.price = existingPrice;
                }
                sections.push(payload);
                syncPricesByType(payload.type, payload.price);
                syncHiddenInput();
                renderCanvas();
                renderList();
                startEdit(sections.length - 1);
            }
        });

        newBtn.addEventListener('click', () => {
            resetForm();
            renderCanvas();
            renderList();
        });

        deleteBtn.addEventListener('click', () => {
            if (editingIndex === null) return;
            sections.splice(editingIndex, 1);
            syncHiddenInput();
            renderCanvas();
            renderList();
            resetForm();
        });

        resetBtn?.addEventListener('click', () => {
            resetForm();
            renderCanvas();
            renderList();
        });

        drawArea.addEventListener('mousedown', (e) => {
            if (e.target !== drawArea) return;
            const start = percentFromEvent(e);
            drawPreview = document.createElement('div');
            drawPreview.className = 'absolute border-2 border-dashed border-amber-400 bg-amber-400/20 pointer-events-none z-30';
            drawPreview.style.left = start.x + '%';
            drawPreview.style.top = start.y + '%';
            drawPreview.style.width = '0%';
            drawPreview.style.height = '0%';
            canvas.appendChild(drawPreview);
            const onMove = (e2) => {
                if (!drawPreview) return;
                const cur = percentFromEvent(e2);
                const x = Math.min(start.x, cur.x), y = Math.min(start.y, cur.y);
                const w = Math.abs(cur.x - start.x), h = Math.abs(cur.y - start.y);
                drawPreview.style.left = x + '%';
                drawPreview.style.top = y + '%';
                drawPreview.style.width = w + '%';
                drawPreview.style.height = h + '%';
            };
            const onUp = (eUp) => {
                if (!drawPreview) return;
                const cur = percentFromEvent(eUp);
                const x = Math.min(start.x, cur.x), y = Math.min(start.y, cur.y);
                let w = Math.abs(cur.x - start.x), h = Math.abs(cur.y - start.y);
                drawPreview.remove();
                drawPreview = null;
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onUp);
                if (w >= 3 && h >= 3) {
                    form.querySelector('[name="section_pos_x"]').value = Math.round(x * 10) / 10;
                    form.querySelector('[name="section_pos_y"]').value = Math.round(y * 10) / 10;
                    form.querySelector('[name="section_width"]').value = Math.round(w * 10) / 10;
                    form.querySelector('[name="section_height"]').value = Math.round(h * 10) / 10;
                    form.querySelector('[name="section_name"]').focus();
                }
            };
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
        });

        modeSelect.addEventListener('change', (event) => {
            toggleSeatFields(event.target.value);
        });

        form.querySelector('[name="section_type"]').addEventListener('change', (event) => {
            const type = event.target.value;
            updatePriceSyncHint(type);
            if (editingIndex === null) {
                const existingPrice = getPriceForType(type);
                if (existingPrice !== null) {
                    form.querySelector('[name="section_price"]').value = existingPrice;
                }
            }
        });

        toggleSeatFields(modeSelect.value);
        renderCanvas();
        renderList();
    });

    // Addons builder
    document.querySelectorAll('[data-addons-builder]').forEach(builderEl => {
        const listEl = builderEl.querySelector('[data-addons-list]');
        const addBtn = builderEl.querySelector('[data-add-addon]');
        const hiddenInput = document.getElementById('addonsPayloadInput');
        let addons = [];
        try {
            addons = JSON.parse(hiddenInput.value || builderEl.dataset.initial || '[]') || [];
        } catch (e) {
            addons = [];
        }

        function syncInput() {
            hiddenInput.value = JSON.stringify(addons);
        }

        function render() {
            listEl.innerHTML = '';
            addons.forEach((addon, index) => {
                const row = document.createElement('div');
                row.className = 'grid grid-cols-1 md:grid-cols-12 gap-3 items-start p-3 rounded-xl bg-slate-950/80 border border-slate-800';
                row.innerHTML = `
                    <input type="hidden" data-addon-id value="${addon.id || ''}">
                    <div class="md:col-span-4">
                        <input type="text" data-addon-name class="w-full rounded-lg bg-slate-900 border border-slate-700 px-3 py-2 text-white text-sm" placeholder="Название" value="${(addon.name || '').replace(/"/g, '&quot;')}">
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" data-addon-price min="0" step="100" class="w-full rounded-lg bg-slate-900 border border-slate-700 px-3 py-2 text-white text-sm" placeholder="Цена ₽" value="${addon.price ?? ''}">
                    </div>
                    <div class="md:col-span-4">
                        <input type="text" data-addon-desc class="w-full rounded-lg bg-slate-900 border border-slate-700 px-3 py-2 text-white text-sm" placeholder="Описание (необяз.)" value="${(addon.description || '').replace(/"/g, '&quot;')}">
                    </div>
                    <div class="md:col-span-2">
                        <button type="button" data-remove-addon class="text-rose-400 hover:text-rose-300 text-sm">Удалить</button>
                    </div>
                `;
                row.querySelector('[data-addon-name]').addEventListener('input', () => updateAddon(index));
                row.querySelector('[data-addon-price]').addEventListener('input', () => updateAddon(index));
                row.querySelector('[data-addon-desc]').addEventListener('input', () => updateAddon(index));
                row.querySelector('[data-remove-addon]').addEventListener('click', () => {
                    addons.splice(index, 1);
                    syncInput();
                    render();
                });
                listEl.appendChild(row);
            });
        }

        function updateAddon(index) {
            const row = listEl.children[index];
            if (!row) return;
            addons[index] = {
                id: addons[index]?.id || null,
                name: row.querySelector('[data-addon-name]').value,
                price: parseFloat(row.querySelector('[data-addon-price]').value) || 0,
                description: row.querySelector('[data-addon-desc]').value || '',
            };
            syncInput();
        }

        addBtn.addEventListener('click', () => {
            addons.push({ id: null, name: '', price: 0, description: '' });
            syncInput();
            render();
            listEl.lastElementChild?.querySelector('[data-addon-name]')?.focus();
        });

        listEl.addEventListener('input', (e) => {
            const row = e.target.closest('[data-addon-name], [data-addon-price], [data-addon-desc]')?.closest('div.grid');
            if (!row) return;
            const index = Array.from(listEl.children).indexOf(row);
            if (index >= 0) updateAddon(index);
        });

        render();
    });
});
</script>
@endpush

