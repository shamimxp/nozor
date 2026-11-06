@extends('layouts.admin')
@section('title', 'Price Quotation')
@section('content')
<style>
    .pq-page { --pq-navy:#111C38; --pq-red:#CB2127; --pq-border:#E6E8F0; --pq-bg:#F7F8FC; --pq-muted:#6E6B7B; }
    .pq-toolbar {
        background:#fff; border:1px solid var(--pq-border); border-radius:12px; padding:1rem 1.15rem;
        margin-bottom:1rem; box-shadow:0 2px 10px rgba(17,28,56,.04);
    }
    .pq-toolbar label { font-size:.78rem; font-weight:700; color:var(--pq-muted); margin-bottom:.3rem; display:block; }
    .pq-toolbar .form-control, .pq-toolbar .custom-select {
        border-radius:8px; border-color:var(--pq-border); min-height:40px;
    }
    .pq-type-group { display:flex; flex-wrap:wrap; gap:.5rem; }
    .pq-type-btn {
        border:1px solid var(--pq-border); background:#fff; color:var(--pq-navy); border-radius:999px;
        padding:.45rem .9rem; font-weight:600; font-size:.8125rem; cursor:pointer; transition:.15s ease;
    }
    .pq-type-btn.is-active { background:var(--pq-navy); border-color:var(--pq-navy); color:#fff; }
    .pq-layout {
        display:grid; grid-template-columns: minmax(0,1fr) 340px; gap:1rem; align-items:start;
    }
    @media (max-width: 1199.98px) { .pq-layout { grid-template-columns:1fr; } }
    .pq-grid {
        display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:.85rem;
    }
    .pq-card {
        background:#fff; border:1px solid var(--pq-border); border-radius:12px; overflow:hidden;
        transition:border-color .15s ease, box-shadow .15s ease; cursor:pointer; height:100%;
        display:flex; flex-direction:column;
    }
    .pq-card:hover { border-color:#c9cede; box-shadow:0 6px 18px rgba(17,28,56,.07); }
    .pq-card.is-selected { border-color:var(--pq-navy); box-shadow:0 0 0 2px rgba(17,28,56,.12); }
    .pq-card-img {
        height:140px; background:var(--pq-bg); position:relative; overflow:hidden;
    }
    .pq-card-img img { width:100%; height:100%; object-fit:cover; display:block; }
    .pq-card-check {
        position:absolute; top:8px; left:8px; width:22px; height:22px; border-radius:6px;
        background:rgba(255,255,255,.95); border:1px solid #d1d5db; display:flex; align-items:center;
        justify-content:center; z-index:2;
    }
    .pq-card.is-selected .pq-card-check { background:var(--pq-navy); border-color:var(--pq-navy); color:#fff; }
    .pq-badge {
        position:absolute; top:8px; right:8px; background:rgba(17,28,56,.88); color:#fff;
        font-size:.65rem; font-weight:700; padding:.2rem .45rem; border-radius:999px; z-index:2;
    }
    .pq-card-body { padding:.85rem .9rem 1rem; flex:1; display:flex; flex-direction:column; }
    .pq-card-name { font-weight:700; color:var(--pq-navy); font-size:.9rem; line-height:1.3; margin-bottom:.35rem; }
    .pq-card-cat { font-size:.72rem; color:var(--pq-muted); margin-bottom:.55rem; }
    .pq-price-row { margin-top:auto; }
    .pq-price-final { font-size:1.05rem; font-weight:800; color:var(--pq-navy); }
    .pq-price-old { font-size:.78rem; color:#9ca3af; text-decoration:line-through; margin-right:.35rem; }
    .pq-disc {
        display:inline-block; background:#FEE2E2; color:#B91C1C; font-size:.68rem; font-weight:700;
        border-radius:999px; padding:.15rem .45rem; margin-top:.35rem;
    }
    .pq-empty, .pq-loading {
        text-align:center; padding:2.5rem 1rem; color:var(--pq-muted);
        border:1px dashed var(--pq-border); border-radius:12px; background:#fff;
    }
    .pq-side {
        position:sticky; top:80px; background:#fff; border:1px solid var(--pq-border);
        border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(17,28,56,.05);
    }
    .pq-side-head {
        background:var(--pq-navy); color:#fff; padding:.9rem 1rem;
        display:flex; justify-content:space-between; align-items:center; gap:.5rem;
    }
    .pq-side-head h5 { margin:0; font-size:.95rem; font-weight:700; }
    .pq-side-body { padding:1rem; max-height:52vh; overflow:auto; }
    .pq-selected-item {
        display:flex; gap:.65rem; align-items:flex-start; padding:.55rem 0;
        border-bottom:1px solid #f0f1f5;
    }
    .pq-selected-item:last-child { border-bottom:0; }
    .pq-selected-item img {
        width:42px; height:42px; border-radius:8px; object-fit:cover; border:1px solid var(--pq-border);
    }
    .pq-side-foot { padding:1rem; border-top:1px solid var(--pq-border); background:#fafbfe; }
    .pq-side .btn { border-radius:8px; font-weight:600; }
    .pq-chip {
        display:inline-flex; align-items:center; gap:.3rem; background:var(--pq-bg);
        border:1px solid var(--pq-border); border-radius:999px; padding:.3rem .7rem;
        font-size:.75rem; font-weight:700; color:var(--pq-navy);
    }
</style>

<div class="pq-page">
    <div class="d-flex flex-wrap align-items-end justify-content-between mb-1" style="gap:.75rem;">
        <div>
            <h3 class="mb-25 font-weight-bolder" style="color:#111C38;">Price Quotation</h3>
            <p class="mb-0 text-muted font-small-3">
                Filter products, choose a price type, preview the quotation on this page, then download PDF — nothing is saved.
            </p>
        </div>
        <div class="d-flex flex-wrap" style="gap:.5rem;">
            <span class="pq-chip"><i data-feather="building" style="width:13px;height:13px;"></i> {{ $company['name'] }}</span>
            <span class="pq-chip" id="pqVisibleCount">0 products</span>
            <span class="pq-chip" id="pqSelectedCount">0 selected</span>
        </div>
    </div>

    <div class="pq-toolbar">
        <div class="row align-items-end">
            <div class="col-lg-4 col-md-6 mb-1 mb-lg-0">
                <label>Price Type</label>
                <div class="pq-type-group" id="pqPriceTypes">
                    @foreach($priceTypes as $key => $label)
                        <button type="button" class="pq-type-btn {{ $loop->first ? 'is-active' : '' }}" data-type="{{ $key }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-1 mb-lg-0">
                <label for="pqCategory">Category</label>
                <select id="pqCategory" class="custom-select">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-6 mb-1 mb-lg-0">
                <label for="pqSearch">Search</label>
                <input type="search" id="pqSearch" class="form-control" placeholder="Product name or model…">
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="d-flex align-items-center" style="gap:.5rem; margin-top:1.4rem;">
                    <input type="checkbox" id="pqOnlyMfg" class="mr-50">
                    <span class="font-weight-bold text-body" style="font-size:.85rem;">Manufacture products only</span>
                </label>
            </div>
        </div>
    </div>

    <div class="pq-layout">
        <div>
            <div id="pqLoading" class="pq-loading d-none">Loading products…</div>
            <div id="pqEmpty" class="pq-empty d-none">No products match your filters.</div>
            <div class="pq-grid" id="pqGrid"></div>
            <div class="text-center mt-1 mb-1" id="pqLoadMoreWrap">
                <button type="button" class="btn btn-outline-primary d-none" id="pqLoadMore">
                    <span class="pq-load-more-label">Load more</span>
                </button>
            </div>
        </div>

        <aside class="pq-side">
            <div class="pq-side-head">
                <h5>Quotation Preview</h5>
                <button type="button" class="btn btn-sm btn-light" id="pqClearSelected" title="Clear">Clear</button>
            </div>
            <div class="pq-side-body" id="pqSelectedList">
                <div class="text-muted text-center py-2 font-small-3">Select products to build a quotation.</div>
            </div>
            <div class="pq-side-foot">
                <div class="form-group mb-1">
                    <label for="pqTitle" class="font-weight-bold font-small-3">PDF Title</label>
                    <input type="text" id="pqTitle" class="form-control form-control-sm" placeholder="Optional custom title">
                </div>
                <div class="form-group mb-1">
                    <label for="pqNote" class="font-weight-bold font-small-3">Note</label>
                    <textarea id="pqNote" class="form-control form-control-sm" rows="2" placeholder="Optional note for the PDF"></textarea>
                </div>
                <div class="d-flex" style="gap:.5rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" id="pqSelectAllVisible">Select visible</button>
                    <button type="button" class="btn btn-primary btn-sm flex-fill d-inline-flex align-items-center justify-content-center" id="pqDownloadPdf">
                        <i data-feather="download" class="mr-50" style="width:14px;height:14px;"></i>
                        Download PDF
                    </button>
                </div>
            </div>
        </aside>
    </div>
</div>

<form id="pqPdfForm" action="{{ route('admin.price-quotation.pdf') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="price_type" id="pqFormPriceType" value="dealer">
    <input type="hidden" name="title" id="pqFormTitle" value="">
    <input type="hidden" name="note" id="pqFormNote" value="">
    <div id="pqFormIds"></div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    var productsUrl = @json(route('admin.price-quotation.products'));
    var pdfUrl = @json(route('admin.price-quotation.pdf'));
    var csrfToken = @json(csrf_token());
    var currency = @json($company['currency'] ?? '৳');
    var state = {
        priceType: 'dealer',
        products: [],
        selected: {},
        page: 1,
        perPage: 24,
        total: 0,
        hasMore: false,
        loading: false
    };
    var searchTimer = null;

    function qs(sel) { return document.querySelector(sel); }
    function money(v) {
        if (v === null || v === undefined || isNaN(v)) return '—';
        return currency + ' ' + Number(v).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function (m) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]);
        });
    }

    function currentFilters(page) {
        return {
            price_type: state.priceType,
            category_id: qs('#pqCategory').value || '',
            search: qs('#pqSearch').value.trim(),
            only_manufacturer: qs('#pqOnlyMfg').checked ? 1 : 0,
            page: page || state.page,
            per_page: state.perPage
        };
    }

    function setLoadMoreVisible(show) {
        var btn = qs('#pqLoadMore');
        if (!btn) return;
        btn.classList.toggle('d-none', !show);
        btn.disabled = !!state.loading;
        btn.querySelector('.pq-load-more-label').textContent = state.loading ? 'Loading…' : 'Load more';
    }

    function loadProducts(reset) {
        if (state.loading) return;
        if (reset) {
            state.page = 1;
            state.products = [];
            qs('#pqGrid').innerHTML = '';
        }

        state.loading = true;
        setLoadMoreVisible(state.hasMore || reset);
        qs('#pqLoading').classList.toggle('d-none', !reset);
        qs('#pqEmpty').classList.add('d-none');

        $.ajax({
            url: productsUrl,
            data: currentFilters(state.page),
            success: function (res) {
                var batch = res.data || [];
                state.total = res.total || 0;
                state.hasMore = !!res.has_more;
                state.page = (res.page || state.page);

                if (reset) {
                    state.products = batch;
                } else {
                    var existing = {};
                    state.products.forEach(function (p) { existing[p.id] = true; });
                    batch.forEach(function (p) {
                        if (!existing[p.id]) state.products.push(p);
                    });
                }

                refreshSelectedPricing();
                renderGrid(reset);
                renderSelected();
            },
            error: function () {
                if (reset) {
                    state.products = [];
                    state.total = 0;
                    state.hasMore = false;
                    renderGrid(true);
                }
                toastr.error('Failed to load products');
            },
            complete: function () {
                state.loading = false;
                qs('#pqLoading').classList.add('d-none');
                setLoadMoreVisible(state.hasMore);
                if (window.feather) feather.replace({ width: 14, height: 14 });
            }
        });
    }

    function cardHtml(p) {
        var selected = !!state.selected[p.id];
        return '' +
            '<div class="pq-card' + (selected ? ' is-selected' : '') + '" data-id="' + p.id + '">' +
                '<div class="pq-card-img">' +
                    '<div class="pq-card-check">' + (selected ? '✓' : '') + '</div>' +
                    (p.is_manufacturer ? '<span class="pq-badge">MFG</span>' : '') +
                    '<img src="' + escapeHtml(p.image_url) + '" alt="" loading="lazy" onerror="this.src=\'{{ asset('images/no-image.png') }}\'">' +
                '</div>' +
                '<div class="pq-card-body">' +
                    '<div class="pq-card-name">' + escapeHtml(p.name) + '</div>' +
                    '<div class="pq-card-cat">' + escapeHtml(p.category_name || 'Uncategorized') + (p.model_no ? ' · ' + escapeHtml(p.model_no) : '') + '</div>' +
                    '<div class="pq-price-row">' +
                        (p.has_discount && p.base_price !== null ? '<span class="pq-price-old">' + money(p.base_price) + '</span>' : '') +
                        '<div class="pq-price-final">' + money(p.final_price) + '</div>' +
                        (p.has_discount && p.discount_label ? '<span class="pq-disc">' + escapeHtml(p.discount_label) + '</span>' : '') +
                    '</div>' +
                '</div>' +
            '</div>';
    }

    function renderGrid(replace) {
        var grid = qs('#pqGrid');
        var empty = qs('#pqEmpty');
        qs('#pqVisibleCount').textContent = state.products.length + ' / ' + state.total + ' products';

        if (!state.products.length) {
            grid.innerHTML = '';
            empty.classList.remove('d-none');
            setLoadMoreVisible(false);
            return;
        }
        empty.classList.add('d-none');

        if (replace) {
            grid.innerHTML = state.products.map(cardHtml).join('');
        } else {
            // Re-render all so selection state stays correct after append
            grid.innerHTML = state.products.map(cardHtml).join('');
        }
    }

    function renderSelected() {
        var ids = Object.keys(state.selected);
        qs('#pqSelectedCount').textContent = ids.length + ' selected';
        var list = qs('#pqSelectedList');

        if (!ids.length) {
            list.innerHTML = '<div class="text-muted text-center py-2 font-small-3">Select products to build a quotation.</div>';
            return;
        }

        list.innerHTML = ids.map(function (id) {
            var p = state.selected[id];
            return '' +
                '<div class="pq-selected-item">' +
                    '<img src="' + escapeHtml(p.image_url) + '" alt="" onerror="this.src=\'{{ asset('images/no-image.png') }}\'">' +
                    '<div class="flex-grow-1">' +
                        '<div class="font-weight-bold" style="font-size:.8rem;color:#111C38;">' + escapeHtml(p.name) + '</div>' +
                        '<div class="font-small-3 text-muted">' + money(p.final_price) +
                            (p.has_discount && p.discount_label ? ' · ' + escapeHtml(p.discount_label) : '') +
                        '</div>' +
                    '</div>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger pq-remove" data-id="' + p.id + '" title="Remove">&times;</button>' +
                '</div>';
        }).join('');
    }

    function toggleProduct(id) {
        id = String(id);
        var product = state.products.find(function (p) { return String(p.id) === id; });
        if (!product) return;
        if (state.selected[id]) {
            delete state.selected[id];
        } else {
            state.selected[id] = product;
        }
        renderGrid(true);
        renderSelected();
    }

    function refreshSelectedPricing() {
        Object.keys(state.selected).forEach(function (id) {
            var fresh = state.products.find(function (p) { return String(p.id) === id; });
            if (fresh) state.selected[id] = fresh;
        });
    }

    function resetAndLoad() {
        loadProducts(true);
    }

    document.getElementById('pqPriceTypes').addEventListener('click', function (e) {
        var btn = e.target.closest('.pq-type-btn');
        if (!btn) return;
        document.querySelectorAll('.pq-type-btn').forEach(function (el) { el.classList.remove('is-active'); });
        btn.classList.add('is-active');
        state.priceType = btn.getAttribute('data-type');
        qs('#pqFormPriceType').value = state.priceType;
        resetAndLoad();
    });

    qs('#pqCategory').addEventListener('change', resetAndLoad);
    qs('#pqOnlyMfg').addEventListener('change', resetAndLoad);
    qs('#pqSearch').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(resetAndLoad, 280);
    });

    qs('#pqLoadMore').addEventListener('click', function () {
        if (!state.hasMore || state.loading) return;
        state.page += 1;
        loadProducts(false);
    });

    qs('#pqGrid').addEventListener('click', function (e) {
        var card = e.target.closest('.pq-card');
        if (!card) return;
        toggleProduct(card.getAttribute('data-id'));
    });

    qs('#pqSelectedList').addEventListener('click', function (e) {
        var btn = e.target.closest('.pq-remove');
        if (!btn) return;
        delete state.selected[btn.getAttribute('data-id')];
        renderGrid(true);
        renderSelected();
    });

    qs('#pqClearSelected').addEventListener('click', function () {
        state.selected = {};
        renderGrid(true);
        renderSelected();
    });

    qs('#pqSelectAllVisible').addEventListener('click', function () {
        state.products.forEach(function (p) { state.selected[p.id] = p; });
        renderGrid(true);
        renderSelected();
    });

    qs('#pqDownloadPdf').addEventListener('click', function () {
        var ids = Object.keys(state.selected);
        if (!ids.length) {
            toastr.warning('Select at least one product');
            return;
        }

        var btn = qs('#pqDownloadPdf');
        btn.disabled = true;
        var original = btn.innerHTML;
        btn.innerHTML = 'Downloading…';

        var formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('price_type', state.priceType);
        formData.append('title', qs('#pqTitle').value.trim());
        formData.append('note', qs('#pqNote').value.trim());
        ids.forEach(function (id) { formData.append('product_ids[]', id); });

        fetch(pdfUrl, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/pdf' }
        }).then(function (res) {
            if (!res.ok) throw new Error('PDF failed');
            var disposition = res.headers.get('Content-Disposition') || '';
            var match = disposition.match(/filename=\"?([^\";]+)\"?/i);
            var filename = match ? match[1] : ('price-quotation-' + state.priceType + '.pdf');
            return res.blob().then(function (blob) {
                return { blob: blob, filename: filename };
            });
        }).then(function (payload) {
            var url = URL.createObjectURL(payload.blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = payload.filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
            toastr.success('PDF downloaded');
        }).catch(function () {
            // Fallback to classic form submit
            qs('#pqFormPriceType').value = state.priceType;
            qs('#pqFormTitle').value = qs('#pqTitle').value.trim();
            qs('#pqFormNote').value = qs('#pqNote').value.trim();
            qs('#pqFormIds').innerHTML = ids.map(function (id) {
                return '<input type="hidden" name="product_ids[]" value="' + id + '">';
            }).join('');
            qs('#pqPdfForm').submit();
        }).finally(function () {
            btn.disabled = false;
            btn.innerHTML = original;
            if (window.feather) feather.replace({ width: 14, height: 14 });
        });
    });

    loadProducts(true);
})();
</script>
@endpush
