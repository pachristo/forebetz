{{-- VIP Quick Edit — one tip + odds row per plan (same modal design as free) --}}
<div id="fastEditModal1" onclick="closeFastEditModal1()" class="fe-qm hidden" role="dialog" aria-modal="true" aria-labelledby="feQmVipTitle">
    <div onclick="event.stopPropagation()" class="fe-qm__shell fe-qm__shell--vip">
        <div class="fe-qm__header">
            <div>
                <h2 id="feQmVipTitle" class="fe-qm__title">Edit VIP predictions</h2>
                <p class="fe-qm__hint">Fill tip (and odds) to enable a plan. Clear tip to remove it.</p>
            </div>
            <button type="button" onclick="closeFastEditModal1()" class="fe-qm__close" aria-label="Close">&times;</button>
        </div>

        <form id="fastEditForm1" onsubmit="saveFastEdit1(event)" autocomplete="off">
            <input type="hidden" name="id" id="match-vip-matchId">

            <div class="fe-qm__body">
                <div class="fe-qm__match">
                    <div class="fe-qm__league">
                        <img src="" id="match-vip-leagueLogo" alt="">
                        <span class="fe-qm__league-name" id="match-vip-leagueName"></span>
                    </div>
                    <div class="fe-qm__teams">
                        <div class="fe-qm__team">
                            <img src="" alt="" id="match-vip-homeLogo">
                            <span class="fe-qm__team-name" id="match-vip-homeName"></span>
                        </div>
                        <div class="fe-qm__vs">VS</div>
                        <div class="fe-qm__team">
                            <img src="" alt="" id="match-vip-awayLogo">
                            <span class="fe-qm__team-name" id="match-vip-awayName"></span>
                        </div>
                    </div>
                </div>

                <div class="fe-qm__section">
                    <div class="fe-qm__section-head">
                        <strong>VIP plans</strong>
                    </div>

                    <div class="fe-qm__rows" id="predictions-grid1">
                        @php
                            try {
                                $categories = \App\Models\PlanCategory::query()->orderBy('id')->get();
                            } catch (\Throwable $_) {
                                $categories = collect();
                            }
                        @endphp

                        @forelse ($categories as $cat)
                            @if (! empty($cat->id))
                                @php
                                    $id = $cat->id;
                                    $label = trim((string) ($cat->name ?? $cat->title ?? ('Plan '.$id)));
                                @endphp
                                <div class="fe-qm__row" data-vip-plan="{{ $id }}">
                                    <div class="fe-qm__row-label" title="{{ $label }}">{{ $label }}</div>
                                    <div class="fe-qm__row-tip">
                                        <input type="text"
                                            data-qp-key="v_{{ $id }}_tips"
                                            name="v_{{ $id }}_tips"
                                            autocomplete="off"
                                            placeholder="Tip"
                                            class="fe-qm__field">
                                    </div>
                                    <div class="fe-qm__row-odds">
                                        <input type="text"
                                            data-qp-key="v_{{ $id }}_odds"
                                            name="v_{{ $id }}_odds"
                                            autocomplete="off"
                                            inputmode="decimal"
                                            placeholder="Odds"
                                            class="fe-qm__field fe-qm__field--odds">
                                    </div>
                                </div>
                            @endif
                        @empty
                            <p class="fe-qm__empty">
                                No VIP plan categories found. Create plans under VIP / Plan categories, then reload.
                            </p>
                        @endforelse
                    </div>
                </div>

                <p class="fe-qm__error" id="feQmVipError" hidden></p>

                <div class="fe-qm__actions">
                    <button type="button" class="fe-qm__btn fe-qm__btn--muted" onclick="closeFastEditModal1()">Cancel</button>
                    <button type="submit" class="fe-qm__btn fe-qm__btn--primary" id="feQmVipSave">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
