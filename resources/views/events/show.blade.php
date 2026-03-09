@extends('layouts.app')

@section('content')
    {{-- Обложка и блок с кнопками как на референсе --}}
    <div class="relative mb-8 left-1/2 w-screen -translate-x-1/2 overflow-hidden border-y border-white/[0.08] bg-black">
        @if($event->poster_url)
            <div class="h-[320px] sm:h-[420px] lg:h-[520px] bg-[#18181c]">
                <img src="{{ $event->poster_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
            </div>
        @else
            <div class="h-[320px] sm:h-[420px] lg:h-[520px] bg-gradient-to-br from-violet-900/40 to-cyan-900/30"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/40 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 right-0">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:py-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wider text-zinc-400">{{ optional($event->start_at)->format('d.m.Y · H:i') }} · {{ $event->venue?->name }}</p>
                    <h1 class="text-2xl sm:text-4xl font-bold text-white mt-1">{{ $event->title }}</h1>
                    @if($event->subtitle)
                        <p class="text-zinc-300 mt-1">{{ $event->subtitle }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="#booking-steps" data-scroll-to-booking class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-black font-semibold text-sm transition-colors">
                        Купить билеты
                    </a>
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" data-favorite-btn data-event-id="{{ $event->id }}" data-favorited="{{ $isFavorited ? '1' : '0' }}"
                                    @click="open = !open"
                                    class="w-10 h-10 rounded-xl border flex items-center justify-center transition {{ $isFavorited ? 'bg-rose-500/20 border-rose-500/50 text-rose-400' : 'border-amber-500/50 text-amber-400 hover:bg-rose-500/20 hover:border-rose-500/50 hover:text-rose-400' }}">
                                ♥
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 top-full mt-1.5 w-56 rounded-xl border border-white/[0.08] bg-[#18181c] shadow-xl py-2 z-50">
                                <a href="{{ route('cabinet.favorites') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-300 hover:bg-white/5 hover:text-white transition-colors">
                                    <span class="text-zinc-500">♥</span> Понравившиеся мероприятия
                                </a>
                            </div>
                        </div>
                    @endauth
                    <span class="px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-500/20 border border-emerald-500/30 text-emerald-300">Открыта продажа</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-[1.2fr,0.8fr]">
        <div class="space-y-6">
            <div class="gradient-border p-6">
                <div class="text-zinc-300 leading-relaxed">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>

            <div class="gradient-border p-6 space-y-4" id="booking-steps">
                <h2 class="text-xl font-semibold text-white">Выберите места</h2>
                <div class="flex flex-wrap gap-4 mb-2" data-price-legend>
                    @foreach($event->sections->unique('price') as $section)
                        <div class="flex items-center gap-2 text-sm text-zinc-400">
                            <span class="w-3 h-3 rounded-full shrink-0" style="background:{{ $section->color ?? '#3b82f6' }}"></span>
                            <span>{{ number_format($section->price, 0, '', ' ') }} ₽</span>
                        </div>
                    @endforeach
                </div>
                <div class="relative rounded-2xl border border-white/[0.08] bg-[#18181c] aspect-[16/9] min-h-[360px] overflow-hidden">
                    <div class="absolute right-4 top-4 z-20 flex flex-col gap-2">
                        <button type="button" class="w-11 h-11 rounded-full bg-white text-zinc-900 shadow-lg text-xl" data-hall-zoom-in>+</button>
                        <button type="button" class="w-11 h-11 rounded-full bg-white text-zinc-900 shadow-lg text-xl" data-hall-zoom-out>-</button>
                    </div>
                    <div class="absolute inset-0" data-hall-zoom-stage>
                        <div class="absolute inset-0 origin-center transition-transform duration-200" data-hall-zoom-content>
                            <div class="absolute inset-4 border border-dashed border-white/[0.1] rounded-2xl"></div>
                            <div class="absolute top-4 left-1/2 -translate-x-1/2 px-6 py-2 rounded-full text-xs tracking-widest bg-white/[0.08] text-zinc-400 border border-white/[0.08]">СЦЕНА</div>
                            @foreach($event->sections as $section)
                                @php
                                    $position = $section->position ?? ['x' => 15, 'y' => 20, 'width' => 20, 'height' => 15];
                                @endphp
                                <div class="absolute rounded-2xl border border-white/10 backdrop-blur cursor-pointer transition hover:scale-[1.01]"
                                     style="left: {{ $position['x'] ?? 10 }}%; top: {{ $position['y'] ?? 10 }}%; width: {{ $position['width'] ?? 20 }}%; height: {{ $position['height'] ?? 15 }}%; background: {{ ($section->color ?? '#3b82f6') }}22; border-color: {{ $section->color ?? '#3b82f6' }};"
                                     data-scroll-to-section="{{ $section->id }}">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-3 text-xs font-semibold text-white">
                                        <span>{{ $section->name }}</span>
                                        <span class="text-[11px] text-zinc-300 mt-1">{{ $section->seating_mode === 'seated' ? 'Сидячие' : 'Зона' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                @foreach($event->sections as $section)
                    <div class="gradient-border p-5 space-y-4" 
                        id="section-{{ $section->id }}" 
                        data-section-card 
                        data-section='{!! json_encode([
                            "id" => $section->id,
                            "name" => $section->name,
                            "seating_mode" => $section->seating_mode,
                            "price" => (float) $section->price,
                        ]) !!}'>
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-xs uppercase tracking-wider text-zinc-500">{{ $section->type }}</p>
                                <h3 class="text-xl font-semibold text-white">{{ $section->name }}</h3>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-zinc-500 uppercase">Стоимость от</div>
                                <div class="text-xl font-bold text-white">{{ number_format($section->price, 0, '', ' ') }} ₽</div>
                            </div>
                        </div>

                        @if($section->seating_mode === 'seated')
                            @php
                                $grouped = $section->seats->sortBy(['row_number','col_number'])->groupBy('row_number');
                            @endphp
                            <div class="rounded-xl border border-white/[0.08] bg-white/[0.03] p-4 overflow-x-auto">
                                <div class="space-y-2">
                                    @foreach($grouped as $row => $seats)
                                        <div class="flex items-center gap-2">
                                            <span class="w-10 text-xs text-zinc-500 text-right">{{ $row }}</span>
                                            <div class="grid gap-2" style="grid-template-columns: repeat({{ max(1, $section->cols) }}, minmax(32px, 1fr));">
                                                @foreach($seats as $seat)
                                                    <button type="button"
                                                            class="seat-btn text-xs font-semibold border rounded-lg py-1 {{ $seat->status === 'available' ? 'border-white/20 text-white hover:border-violet-500 hover:bg-violet-500/20' : 'border-red-500/50 text-red-400 cursor-not-allowed opacity-50' }}"
                                                            data-seat
                                                            data-section-id="{{ $section->id }}"
                                                            data-seat-id="{{ $seat->id }}"
                                                            data-label="{{ $seat->label }}"
                                                            data-price="{{ $seat->price ?: $section->price }}"
                                                            data-status="{{ $seat->status }}"
                                                            @disabled($seat->status !== 'available')>
                                                        {{ $seat->label }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="flex flex-wrap items-center gap-4 rounded-xl border border-white/[0.08] bg-white/[0.03] p-4" data-standing-section data-section-id="{{ $section->id }}">
                                <div>
                                    <div class="text-xs uppercase tracking-wider text-zinc-500 mb-1">Количество</div>
                                    <div class="flex items-center gap-3">
                                        <button type="button" class="qty-btn w-8 h-8 rounded-lg border border-white/20 text-zinc-300 hover:border-violet-500/50 text-lg transition-colors" data-action="minus">-</button>
                                        <input type="number" min="0" value="0" class="w-16 text-center rounded-lg bg-white/[0.06] border border-white/[0.08] py-1 text-white" data-qty-input>
                                        <button type="button" class="qty-btn w-8 h-8 rounded-lg border border-white/20 text-zinc-300 hover:border-violet-500/50 text-lg transition-colors" data-action="plus">+</button>
                                    </div>
                                </div>
                                <div class="text-sm text-zinc-400">
                                    Доступно: {{ $section->capacity }} мест · {{ number_format($section->price, 0, '', ' ') }} ₽
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-12 gradient-border p-4">
                <h2 class="text-lg font-semibold text-white mb-3">Где проходит</h2>
                <p class="text-sm text-zinc-500 mb-3">{{ $event->venue?->name }} · {{ $event->venue?->address }}</p>
                <div class="rounded-xl overflow-hidden border border-white/[0.08] aspect-[21/9] bg-[#18181c]">
                    <iframe src="https://yandex.ru/map-widget/v1/?ll={{ $mapLng }},{{ $mapLat }}&pt={{ $mapLng }},{{ $mapLat }}&z=16&l=map" width="100%" height="100%" frameborder="0" allowfullscreen style="border:0;"></iframe>
                </div>
            </div>

            <div class="gradient-border p-6 hidden" data-purchase-cta>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Покупка билета</h2>
                        <p class="text-sm text-zinc-500 mt-1">Выберите места, добавьте опции к заказу и нажмите кнопку ниже для заполнения данных покупателя.</p>
                    </div>
                    <button type="button" data-open-booking-form class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-black font-semibold transition-colors">
                        Перейти к оформлению
                    </button>
                </div>
            </div>
        </div>

        <div class="gradient-border p-6 lg:self-start" data-booking-panel>
            <form method="POST" action="{{ route('events.book', $event) }}" id="booking-form" data-booking-form>
                @csrf
                <input type="hidden" name="tickets_payload" value="[]" data-tickets-input>
                <input type="hidden" name="addons_payload" value="[]" data-addons-input>

                <div data-booking-step="1">
                    <h2 class="text-xl font-semibold text-white mb-4">Выбранные места</h2>
                    <div class="rounded-xl border border-white/[0.08] bg-white/[0.03] p-4 space-y-2">
                        <div data-selection-summary class="text-sm text-zinc-400">Выберите места на схеме слева</div>
                    </div>
                    <p class="mt-4 text-sm text-zinc-500">Нажмите «Далее» внизу страницы после выбора мест.</p>
                </div>

                <div data-booking-step="2" class="hidden">
                    <button type="button" data-step-back class="text-zinc-400 hover:text-white mb-4 flex items-center gap-1 text-sm">← Назад</button>
                    <h2 class="text-xl font-semibold text-white mb-4">Добавьте к заказу</h2>
                    <div class="space-y-4" data-addons-list>
                        @foreach($event->addons as $addon)
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-white/[0.08] bg-white/[0.03] p-4" data-addon-row data-addon-id="{{ $addon->id }}">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-white">{{ $addon->name }}</p>
                                    @if($addon->description)
                                        <p class="text-xs text-zinc-500 mt-0.5">{{ $addon->description }}</p>
                                    @endif
                                    <p class="text-sm text-violet-400 mt-1">{{ number_format($addon->price, 0, '', ' ') }} ₽</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" data-addon-minus class="w-8 h-8 rounded-lg border border-white/20 text-zinc-400 hover:text-white flex items-center justify-center">−</button>
                                    <input type="number" min="0" value="0" data-addon-qty class="w-12 text-center rounded-lg bg-white/[0.06] border border-white/[0.08] py-1 text-white text-sm">
                                    <button type="button" data-addon-plus class="w-8 h-8 rounded-lg border border-white/20 text-zinc-400 hover:text-white flex items-center justify-center">+</button>
                                </div>
                            </div>
                        @endforeach
                        @if($event->addons->isEmpty())
                            <p class="text-zinc-500 text-sm">Дополнений к этому мероприятию нет.</p>
                        @endif
                    </div>
                    <div class="mt-4 rounded-xl border border-white/[0.08] bg-white/[0.03] p-3 text-sm text-zinc-400" data-tickets-summary-step2></div>
                    <button type="button" data-open-booking-form class="w-full mt-4 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-black font-semibold transition-colors">
                        Перейти к оформлению
                    </button>
                </div>

                <div data-booking-step="3" class="hidden">
                    <button type="button" data-step-back class="text-zinc-400 hover:text-white mb-4 flex items-center gap-1 text-sm">← Назад</button>
                    <h2 class="text-xl font-semibold text-white mb-4">Оформление заказа</h2>
                    <div class="space-y-4">
                        <label class="block text-xs uppercase tracking-wider text-zinc-500">Имя и фамилия
                            <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" required class="mt-1 w-full rounded-xl bg-white/[0.06] border border-white/[0.08] px-3 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20">
                        </label>
                        <label class="block text-xs uppercase tracking-wider text-zinc-500">E-mail
                            <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" required class="mt-1 w-full rounded-xl bg-white/[0.06] border border-white/[0.08] px-3 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20">
                        </label>
                        <label class="block text-xs uppercase tracking-wider text-zinc-500">Телефон
                            <input type="text" name="customer_phone" class="mt-1 w-full rounded-xl bg-white/[0.06] border border-white/[0.08] px-3 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20">
                        </label>
                    </div>
                    <div class="mt-4 rounded-xl border border-white/[0.08] bg-white/[0.03] p-3 text-sm text-zinc-400" data-order-total-summary></div>
                    <input type="hidden" name="test_mode" value="0" data-test-mode-input>
                    <div class="mt-4 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 py-3 rounded-xl btn-primary font-semibold text-lg">Оформить заказ</button>
                        <button type="button" data-test-booking class="flex-1 py-3 rounded-xl border border-amber-500/50 bg-amber-500/10 text-amber-200 font-semibold text-lg hover:bg-amber-500/20 transition-colors">Купить в тестовом режиме</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Нижняя панель: количество билетов и кнопка Далее --}}
    <div class="fixed bottom-0 left-0 right-0 z-20 border-t border-white/[0.08] bg-[#1c1c21]/95 backdrop-blur hidden" data-bottom-bar>
        <div class="max-w-7xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-4">
            <div class="text-white font-medium" data-bottom-bar-text>0 билетов: 0 ₽</div>
            <button type="button" data-next-step class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-black font-semibold">Далее</button>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const bookingForm = document.querySelector('[data-booking-form]');
    const bottomBar = document.querySelector('[data-bottom-bar]');
    const bottomBarText = document.querySelector('[data-bottom-bar-text]');
    const nextStepBtn = document.querySelector('[data-next-step]');
    const hallZoomContent = document.querySelector('[data-hall-zoom-content]');
    const purchaseCta = document.querySelector('[data-purchase-cta]');
    let hallScale = 1;

    // Плавная прокрутка к выбору мест
    document.querySelectorAll('[data-scroll-to-booking]').forEach(el => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('booking-steps')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    function applyHallZoom() {
        if (!hallZoomContent) return;
        hallZoomContent.style.transform = `scale(${hallScale})`;
    }

    document.querySelector('[data-hall-zoom-in]')?.addEventListener('click', () => {
        hallScale = Math.min(2, +(hallScale + 0.2).toFixed(2));
        applyHallZoom();
    });

    document.querySelector('[data-hall-zoom-out]')?.addEventListener('click', () => {
        hallScale = Math.max(0.8, +(hallScale - 0.2).toFixed(2));
        applyHallZoom();
    });

    // ——— Избранное ———
    document.querySelectorAll('[data-favorite-btn]').forEach(btn => {
        btn.addEventListener('click', () => {
            const eventId = btn.dataset.eventId;
            if (!eventId) return;
            fetch('{{ route('events.favorite.toggle', $event) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({}),
            })
            .then(r => r.json())
            .then(data => {
                const favorited = !!data.favorited;
                btn.dataset.favorited = favorited ? '1' : '0';
                btn.classList.toggle('bg-rose-500/20', favorited);
                btn.classList.toggle('border-rose-500/50', favorited);
                btn.classList.toggle('text-rose-400', favorited);
                btn.classList.toggle('border-amber-500/50', !favorited);
                btn.classList.toggle('text-amber-400', !favorited);
            })
            .catch(() => {});
        });
    });

    if (!bookingForm) {
        return;
    }

    const ticketsInput = bookingForm.querySelector('[data-tickets-input]');
    const addonsInput = bookingForm.querySelector('[data-addons-input]');
    const summary = bookingForm.querySelector('[data-selection-summary]');
    let ticketsTotalCount = 0;
    let ticketsTotalSum = 0;

    const seats = new Map();
    document.querySelectorAll('[data-seat]').forEach(btn => {
        const key = btn.dataset.seatId;
        seats.set(key, {
            button: btn,
            sectionId: Number(btn.dataset.sectionId),
            price: Number(btn.dataset.price),
            label: btn.dataset.label,
        });
    });

    const standingSections = new Map();
    document.querySelectorAll('[data-standing-section]').forEach(wrapper => {
        const sectionId = Number(wrapper.dataset.sectionId);
        const input = wrapper.querySelector('[data-qty-input]');
        standingSections.set(sectionId, { input, wrapper });

        wrapper.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const action = btn.dataset.action;
                const current = Number(input.value) || 0;
                input.value = action === 'plus' ? current + 1 : Math.max(0, current - 1);
                syncPayload();
            });
        });

        input.addEventListener('input', syncPayload);
    });

    const selections = new Map();

    document.querySelectorAll('[data-seat]').forEach(btn => {
        if (btn.dataset.status !== 'available') {
            return;
        }
        btn.addEventListener('click', () => {
            const seatId = btn.dataset.seatId;
            if (selections.has(seatId)) {
                selections.delete(seatId);
                btn.classList.remove('bg-violet-500/30', 'border-violet-500');
            } else {
                selections.set(seatId, {
                    section_id: Number(btn.dataset.sectionId),
                    seat_id: Number(seatId),
                    label: btn.dataset.label,
                    price: Number(btn.dataset.price),
                });
                btn.classList.add('bg-violet-500/30', 'border-violet-500');
            }
            syncPayload();
        });
    });

    function syncPayload() {
        const payload = [];
        selections.forEach(item => payload.push({
            section_id: item.section_id,
            seat_id: item.seat_id,
        }));

        standingSections.forEach((obj, sectionId) => {
            const qty = Number(obj.input.value) || 0;
            if (qty > 0) {
                payload.push({
                    section_id: sectionId,
                    quantity: qty,
                });
            }
        });

        ticketsInput.value = JSON.stringify(payload);
        renderSummary();
        updateBottomBar();
    }

    function getTicketsTotals() {
        let count = 0;
        let sum = 0;
        selections.forEach(item => { count += 1; sum += item.price; });
        standingSections.forEach((obj, sectionId) => {
            const qty = Number(obj.input.value) || 0;
            const sectionCard = document.querySelector(`#section-${sectionId}`);
            const meta = sectionCard ? JSON.parse(sectionCard.dataset.section || '{}') : {};
            count += qty;
            sum += (meta.price || 0) * qty;
        });
        return { count, sum };
    }

    function renderSummary() {
        const { count, sum } = getTicketsTotals();
        ticketsTotalCount = count;
        ticketsTotalSum = sum;

        if (count === 0) {
            summary.textContent = 'Выберите места на схеме слева';
            return;
        }

        const fragments = [];
        selections.forEach(item => {
            fragments.push(`<div class="flex justify-between"><span>${item.label}</span><span>${item.price.toLocaleString('ru-RU')} ₽</span></div>`);
        });

        standingSections.forEach((obj, sectionId) => {
            const qty = Number(obj.input.value) || 0;
            if (!qty) return;
            const sectionCard = document.querySelector(`#section-${sectionId}`);
            const meta = sectionCard ? JSON.parse(sectionCard.dataset.section || '{}') : {};
            const price = meta.price || 0;
            fragments.push(`<div class="flex justify-between"><span>${meta.name || 'Зона'} × ${qty}</span><span>${(price * qty).toLocaleString('ru-RU')} ₽</span></div>`);
        });

        summary.innerHTML = fragments.join('');
    }

    let currentStep = 1;

    function showStep(step) {
        currentStep = step;
        bookingForm.querySelectorAll('[data-booking-step]').forEach(el => {
            el.classList.toggle('hidden', Number(el.dataset.bookingStep) !== step);
        });
        if (step === 1) {
            updateBottomBar();
        } else {
            if (bottomBar) bottomBar.classList.add('hidden');
        }
        if (step === 2) {
            updateStep2Summary();
        }
        if (step === 3) {
            updateStep2Summary();
            updateStep3Summary();
        }
    }

    function updateBottomBar() {
        if (!bottomBar || !bottomBarText || !nextStepBtn) return;
        const { count, sum } = getTicketsTotals();
        if (purchaseCta) {
            purchaseCta.classList.toggle('hidden', count === 0);
        }
        if (count === 0) {
            bottomBar.classList.add('hidden');
            return;
        }
        bottomBar.classList.remove('hidden');
        const label = count === 1 ? 'билет' : count < 5 ? 'билета' : 'билетов';
        bottomBarText.textContent = `${count} ${label}: ${sum.toLocaleString('ru-RU')} ₽`;
    }

    function updateStep2Summary() {
        const el = bookingForm.querySelector('[data-tickets-summary-step2]');
        if (!el) return;
        el.textContent = `${ticketsTotalCount} билет(ов): ${ticketsTotalSum.toLocaleString('ru-RU')} ₽`;
    }

    function updateStep3Summary() {
        const el = bookingForm.querySelector('[data-order-total-summary]');
        if (!el) return;
        let addonsSum = 0;
        bookingForm.querySelectorAll('[data-addon-row]').forEach(row => {
            const id = row.dataset.addonId;
            const qtyInput = row.querySelector('[data-addon-qty]');
            const qty = Number(qtyInput?.value) || 0;
            const priceEl = row.querySelector('.text-violet-400');
            const price = priceEl ? parseInt(priceEl.textContent.replace(/\s/g, ''), 10) || 0 : 0;
            addonsSum += price * qty;
        });
        const total = ticketsTotalSum + addonsSum;
        const parts = [`Билеты: ${ticketsTotalSum.toLocaleString('ru-RU')} ₽`];
        if (addonsSum > 0) parts.push(`Дополнения: ${addonsSum.toLocaleString('ru-RU')} ₽`);
        parts.push(`Итого: ${total.toLocaleString('ru-RU')} ₽`);
        el.innerHTML = parts.join('<br>');
    }

    nextStepBtn?.addEventListener('click', () => {
        if (currentStep === 1) {
            if (ticketsTotalCount === 0) return;
            showStep(2);
        }
    });

    document.querySelectorAll('[data-open-booking-form]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (ticketsTotalCount === 0) {
                showStep(1);
                document.getElementById('booking-steps')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                return;
            }
            showStep(3);
            bookingForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    bookingForm.querySelectorAll('[data-step-back]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep === 2) showStep(1);
            else if (currentStep === 3) showStep(2);
        });
    });

    bookingForm.querySelectorAll('[data-addon-row]').forEach(row => {
        const qtyInput = row.querySelector('[data-addon-qty]');
        const minus = row.querySelector('[data-addon-minus]');
        const plus = row.querySelector('[data-addon-plus]');
        if (!qtyInput) return;
        function clamp() {
            const v = parseInt(qtyInput.value, 10);
            qtyInput.value = isNaN(v) || v < 0 ? 0 : v;
            if (currentStep === 3) updateStep3Summary();
        }
        minus?.addEventListener('click', () => { qtyInput.value = Math.max(0, (parseInt(qtyInput.value, 10) || 0) - 1); clamp(); });
        plus?.addEventListener('click', () => { qtyInput.value = (parseInt(qtyInput.value, 10) || 0) + 1; clamp(); });
        qtyInput.addEventListener('input', clamp);
    });

    bookingForm.addEventListener('submit', () => {
        const addons = [];
        bookingForm.querySelectorAll('[data-addon-row]').forEach(row => {
            const id = row.dataset.addonId;
            const qty = parseInt(row.querySelector('[data-addon-qty]')?.value, 10) || 0;
            if (id && qty > 0) addons.push({ addon_id: parseInt(id, 10), quantity: qty });
        });
        addonsInput.value = JSON.stringify(addons);
    });

    document.querySelector('[data-test-booking]')?.addEventListener('click', () => {
        const testInput = bookingForm.querySelector('[data-test-mode-input]');
        if (testInput) testInput.value = '1';
        bookingForm.submit();
    });

    document.querySelectorAll('[data-scroll-to-section]').forEach(block => {
        block.addEventListener('click', () => {
            const target = document.querySelector(`#section-${block.dataset.scrollToSection}`);
            target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    syncPayload();
    applyHallZoom();
    showStep(1);
});
</script>
@endpush

