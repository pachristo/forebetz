{{-- Free Quick Edit — refined native modal (same spirit as 100surepredict AJAX modal) --}}
<div id="fastEditModal" onclick="closeFastEditModal()" class="fe-qm hidden" role="dialog" aria-modal="true" aria-labelledby="feQmFreeTitle">
    <div onclick="event.stopPropagation()" class="fe-qm__shell">
        <div class="fe-qm__header">
            <div>
                <h2 id="feQmFreeTitle" class="fe-qm__title">Edit free predictions</h2>
                <p class="fe-qm__hint">Native form — tip fields are instant (no Livewire).</p>
            </div>
            <button type="button" onclick="closeFastEditModal()" class="fe-qm__close" aria-label="Close">&times;</button>
        </div>

        @php
            $quickPickRows = \App\Support\QuickPickModalRows::forFootballFreeEdit();
        @endphp

        <form id="fastEditForm" onsubmit="saveFastEdit(event)" autocomplete="off"
            data-qp-type-keys='@json(collect($quickPickRows)->pluck('key')->values())'>
            <input type="hidden" name="id" id="match-matchId">

            <div class="fe-qm__body">
                <div class="fe-qm__match">
                    <div class="fe-qm__league">
                        <img src="" id="match-leagueLogo" alt="">
                        <span class="fe-qm__league-name" id="match-leagueName"></span>
                    </div>
                    <div class="fe-qm__teams">
                        <div class="fe-qm__team">
                            <img src="" alt="" id="match-homeLogo">
                            <span class="fe-qm__team-name" id="match-homeName"></span>
                        </div>
                        <div class="fe-qm__vs">VS</div>
                        <div class="fe-qm__team">
                            <img src="" alt="" id="match-awayLogo">
                            <span class="fe-qm__team-name" id="match-awayName"></span>
                        </div>
                    </div>
                </div>

                <div class="fe-qm__section">
                    <div class="fe-qm__section-head">
                        <strong>Predictions &amp; odds</strong>
                    </div>

                    <div class="fe-qm__rows" id="predictions-grid">
                        @forelse ($quickPickRows as $row)
                            @php
                                $pKey = $row['key'];
                                $categoryTipOptions = $row['options'];
                                $manualEntry = $row['manual'];
                                $label = $row['label'];
                            @endphp
                            <div class="fe-qm__row" data-qp-row="{{ $pKey }}">
                                <div class="fe-qm__row-label" title="{{ $label }}">{{ $label }}</div>
                                <div class="fe-qm__row-tip">
                                    @if ($manualEntry)
                                        <input type="text" data-qp-key="{{ $pKey }}" name="{{ $pKey }}"
                                            autocomplete="off" placeholder="{{ $row['placeholder'] }}" class="fe-qm__field">
                                    @else
                                        <select data-qp-key="{{ $pKey }}" name="{{ $pKey }}" class="fe-qm__field">
                                            <option value="">—</option>
                                            @foreach ($categoryTipOptions as $tipValue => $tipLabel)
                                                <option value="{{ $tipValue }}">{{ $tipLabel }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="fe-qm__row-odds">
                                    <input type="text" data-qp-odds-key="{{ $pKey }}"
                                        name="{{ \App\Support\QuickPickFields::oddsPayloadKey($pKey) }}"
                                        autocomplete="off" inputmode="decimal" placeholder="Odds"
                                        class="fe-qm__field fe-qm__field--odds">
                                </div>
                            </div>
                        @empty
                            <p class="fe-qm__empty">
                                No football tip categories available. Under
                                <strong>Content Management → Tip categories</strong>, set Sport to Football,
                                set a prediction type, then reload.
                            </p>
                        @endforelse
                    </div>
                </div>

                <p class="fe-qm__error" id="feQmFreeError" hidden></p>

                <div class="fe-qm__actions">
                    <button type="button" class="fe-qm__btn fe-qm__btn--muted" onclick="closeFastEditModal()">Cancel</button>
                    <button type="submit" class="fe-qm__btn fe-qm__btn--primary" id="feQmFreeSave">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
