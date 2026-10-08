<script>
(function () {
    if (window.__feQmInited) return;
    window.__feQmInited = true;

    const QP_ODDS_SUFFIX = '_niceodds';

    function qpOddsPayloadKey(typeKey) {
        return typeKey + QP_ODDS_SUFFIX;
    }

    function decodeHtmlEntities(str) {
        const textarea = document.createElement('textarea');
        textarea.innerHTML = str || '';
        return textarea.value;
    }

    function parseRecordPayload(recordData) {
        const decoded = decodeHtmlEntities(recordData);
        return JSON.parse(decoded || '{}');
    }

    /** Ensure SELECT can display a saved value that is not in the preset option list. */
    function setQuickPickFieldValue(el, val) {
        const value = val == null ? '' : String(val);
        if (el.tagName === 'SELECT') {
            if (value === '') {
                el.value = '';
                return;
            }
            if (Array.from(el.options).some((o) => o.value === value)) {
                el.value = value;
                return;
            }
            // Match "Over 3.5" to option value "+3.5" via label / loose compare.
            const lower = value.toLowerCase().trim();
            const byLabel = Array.from(el.options).find((o) => {
                const label = (o.textContent || '').toLowerCase().trim();
                const ov = (o.value || '').toLowerCase().trim();
                return label === lower
                    || ov === lower
                    || label.replace(/\s+/g, '') === lower.replace(/\s+/g, '')
                    || (lower.includes('over') && ov.startsWith('+') && label.includes('over') && label.includes(ov.slice(1)))
                    || (lower.includes('under') && ov.startsWith('-') && label.includes('under') && label.includes(ov.slice(1)));
            });
            if (byLabel) {
                el.value = byLabel.value;
                return;
            }
            const opt = document.createElement('option');
            opt.value = value;
            opt.textContent = value;
            el.appendChild(opt);
            el.value = value;
            return;
        }
        if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
            el.value = value;
            return;
        }
        el.textContent = value;
    }

    function clearQuickPickFields(modalSelector) {
        document.querySelectorAll(`${modalSelector} [data-qp-key]`).forEach((el) => {
            setQuickPickFieldValue(el, '');
        });
        document.querySelectorAll(`${modalSelector} [data-qp-odds-key]`).forEach((el) => {
            el.value = '';
        });
    }

    function populateQuickPickFields(modalSelector, preds) {
        const data = preds && typeof preds === 'object' ? preds : {};
        clearQuickPickFields(modalSelector);

        document.querySelectorAll(`${modalSelector} [data-qp-key]`).forEach((el) => {
            const key = el.getAttribute('data-qp-key');
            if (!key) return;
            const val = Object.prototype.hasOwnProperty.call(data, key) ? data[key] : '';
            setQuickPickFieldValue(el, val);
        });

        document.querySelectorAll(`${modalSelector} [data-qp-odds-key]`).forEach((el) => {
            const key = el.getAttribute('data-qp-odds-key');
            if (!key) return;
            const oddsKey = qpOddsPayloadKey(key);
            const val = Object.prototype.hasOwnProperty.call(data, oddsKey) ? data[oddsKey] : '';
            el.value = val == null ? '' : String(val);
        });
    }

    /** Send every tip-category field; server keys match game_cats prediction type. */
    function collectQuickPickPayload(modalSelector) {
        const predictions = {};
        document.querySelectorAll(`${modalSelector} [data-qp-key]`).forEach((el) => {
            const key = el.getAttribute('data-qp-key');
            if (!key) return;
            predictions[key] = el.value ?? '';
            const oddsEl = document.querySelector(`${modalSelector} [data-qp-odds-key="${CSS.escape(key)}"]`);
            predictions[qpOddsPayloadKey(key)] = oddsEl ? (oddsEl.value ?? '') : '';
        });
        return predictions;
    }

    function unwrapPredictions(json) {
        if (!json || typeof json !== 'object') return {};
        if (json.predictions && typeof json.predictions === 'object') return json.predictions;
        return json;
    }

    async function fetchJson(url) {
        const res = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            credentials: 'same-origin',
        });
        if (!res.ok) {
            throw new Error('Could not load (' + res.status + ')');
        }
        return res.json();
    }

    function csrfToken() {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        return tokenMeta ? tokenMeta.getAttribute('content') : '';
    }

    function setModalError(id, message) {
        const el = document.getElementById(id);
        if (!el) return;
        if (!message) {
            el.hidden = true;
            el.textContent = '';
            return;
        }
        el.hidden = false;
        el.textContent = message;
    }

    function fillMatchHeader(prefix, record) {
        const idEl = document.getElementById(prefix === 'vip' ? 'match-vip-matchId' : 'match-matchId');
        if (idEl) idEl.value = record.id || '';

        const map = prefix === 'vip'
            ? {
                leagueLogo: 'match-vip-leagueLogo',
                leagueName: 'match-vip-leagueName',
                homeLogo: 'match-vip-homeLogo',
                homeName: 'match-vip-homeName',
                awayLogo: 'match-vip-awayLogo',
                awayName: 'match-vip-awayName',
            }
            : {
                leagueLogo: 'match-leagueLogo',
                leagueName: 'match-leagueName',
                homeLogo: 'match-homeLogo',
                homeName: 'match-homeName',
                awayLogo: 'match-awayLogo',
                awayName: 'match-awayName',
            };

        const leagueLogo = document.getElementById(map.leagueLogo);
        const leagueName = document.getElementById(map.leagueName);
        const homeLogo = document.getElementById(map.homeLogo);
        const homeName = document.getElementById(map.homeName);
        const awayLogo = document.getElementById(map.awayLogo);
        const awayName = document.getElementById(map.awayName);

        if (leagueLogo) leagueLogo.src = record.leagueLogo || '';
        if (leagueName) leagueName.textContent = record.leagueName || '';
        if (homeLogo) homeLogo.src = record.homeIcon || '';
        if (homeName) homeName.textContent = record.homeName || '';
        if (awayLogo) awayLogo.src = record.awayIcon || '';
        if (awayName) awayName.textContent = record.awayName || '';
    }

    // --- Free ---

    window.openFastEditModalFromElement = function (el) {
        const payload = el.getAttribute('data-record');
        if (!payload) {
            console.warn('No data-record found on element', el);
            return window.openFastEditModal('{}');
        }
        return window.openFastEditModal(payload);
    };

    window.openFastEditModal = async function (recordData) {
        const modal = document.getElementById('fastEditModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setModalError('feQmFreeError', '');

        try {
            const record = parseRecordPayload(recordData);
            fillMatchHeader('free', record);

            let preds = record.predictions || {};
            const matchId = record.id;
            if (matchId) {
                try {
                    const json = await fetchJson(`/admin/api/fixtures/${encodeURIComponent(matchId)}/predictions`);
                    preds = unwrapPredictions(json);
                } catch (fetchErr) {
                    console.warn('Using embedded free predictions; API fetch failed:', fetchErr);
                }
            }
            populateQuickPickFields('#fastEditModal', preds);
        } catch (error) {
            console.error('Failed to open free edit modal:', error, recordData);
            setModalError('feQmFreeError', 'Could not open fixture data.');
        }
    };

    window.closeFastEditModal = function () {
        const modal = document.getElementById('fastEditModal');
        if (!modal) return;
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        clearQuickPickFields('#fastEditModal');
        setModalError('feQmFreeError', '');
        const form = document.getElementById('fastEditForm');
        if (form) form.reset();
    };

    window.saveFastEdit = async function (event) {
        event.preventDefault();
        const matchId = document.getElementById('match-matchId')?.value;
        if (!matchId) {
            setModalError('feQmFreeError', 'Missing match id');
            return;
        }

        const predictions = collectQuickPickPayload('#fastEditModal');
        const saveBtn = document.getElementById('feQmFreeSave');
        if (saveBtn) saveBtn.disabled = true;
        setModalError('feQmFreeError', '');

        try {
            const res = await fetch(`/admin/api/fixtures/${encodeURIComponent(matchId)}/predictions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify(predictions),
                credentials: 'same-origin',
            });

            if (!res.ok) {
                const text = await res.text();
                console.error('Save failed', res.status, text);
                setModalError('feQmFreeError', 'Save failed (' + res.status + ')');
                return;
            }

            window.closeFastEditModal();
            window.location.reload();
        } catch (err) {
            console.error('Request failed', err);
            setModalError('feQmFreeError', 'Request failed: ' + (err.message || 'network error'));
        } finally {
            if (saveBtn) saveBtn.disabled = false;
        }
    };

    // --- VIP ---

    window.openFastEditModalFromElement1 = function (el) {
        const payload = el.getAttribute('data-record-vip') || el.getAttribute('data-record');
        if (!payload) {
            console.warn('No data-record found on element', el);
            return window.openFastEditModal1('{}');
        }
        return window.openFastEditModal1(payload);
    };

    window.openFastEditModal1 = async function (recordData) {
        const modal = document.getElementById('fastEditModal1');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setModalError('feQmVipError', '');

        try {
            const record = parseRecordPayload(recordData);
            fillMatchHeader('vip', record);

            let preds = record.predictions || record.vip_predictions || {};
            const matchId = record.id;
            if (matchId) {
                try {
                    const json = await fetchJson(`/admin/api/fixtures/${encodeURIComponent(matchId)}/vip/predictions`);
                    preds = unwrapPredictions(json);
                } catch (fetchErr) {
                    console.warn('Using embedded VIP predictions; API fetch failed:', fetchErr);
                }
            }
            populateQuickPickFields('#fastEditModal1', preds);
        } catch (error) {
            console.error('Failed to open VIP edit modal:', error, recordData);
            setModalError('feQmVipError', 'Could not open fixture data.');
        }
    };

    window.closeFastEditModal1 = function () {
        const modal = document.getElementById('fastEditModal1');
        if (!modal) return;
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        clearQuickPickFields('#fastEditModal1');
        setModalError('feQmVipError', '');
        const form = document.getElementById('fastEditForm1');
        if (form) form.reset();
    };

    window.saveFastEdit1 = async function (event) {
        event.preventDefault();
        const matchId = document.getElementById('match-vip-matchId')?.value;
        if (!matchId) {
            setModalError('feQmVipError', 'Missing match id');
            return;
        }

        const predictions = {};
        document.querySelectorAll('#fastEditModal1 [data-qp-key]').forEach((el) => {
            const key = el.getAttribute('data-qp-key');
            if (!key) return;
            predictions[key] = el.value ?? '';
        });

        const saveBtn = document.getElementById('feQmVipSave');
        if (saveBtn) saveBtn.disabled = true;
        setModalError('feQmVipError', '');

        try {
            const res = await fetch(`/admin/api/fixtures/${encodeURIComponent(matchId)}/vip/predictions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify(predictions),
                credentials: 'same-origin',
            });

            if (!res.ok) {
                const text = await res.text();
                console.error('VIP save failed', res.status, text);
                setModalError('feQmVipError', 'Save failed (' + res.status + ')');
                return;
            }

            window.closeFastEditModal1();
            window.location.reload();
        } catch (err) {
            console.error('VIP request failed', err);
            setModalError('feQmVipError', 'Request failed: ' + (err.message || 'network error'));
        } finally {
            if (saveBtn) saveBtn.disabled = false;
        }
    };
})();
</script>
