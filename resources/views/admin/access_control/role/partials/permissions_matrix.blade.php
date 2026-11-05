@php
    $role = $role ?? null;
    $modules = $permissions->sortKeys();
    $totalPermissions = $modules->flatten()->count();
    $selectedCount = 0;
    if ($role) {
        $selectedCount = $role->permissions->count();
    } elseif (old('permissions')) {
        $selectedCount = count((array) old('permissions'));
    }
    $actionLabels = [
        'list' => 'List',
        'create' => 'Create',
        'edit' => 'Edit',
        'show' => 'View',
        'delete' => 'Delete',
        'force-delete' => 'Force Delete',
        'deleted-list' => 'Deleted List',
        'restore' => 'Restore',
    ];
@endphp

<style>
    .role-perm-page { --rp-navy: #111C38; --rp-red: #CB2127; --rp-border: #E6E8F0; --rp-muted: #6E6B7B; --rp-bg: #F8F8FC; }
    .role-perm-toolbar {
        background: #fff;
        border: 1px solid var(--rp-border);
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        position: sticky;
        top: 70px;
        z-index: 20;
        box-shadow: 0 2px 10px rgba(17, 28, 56, 0.04);
    }
    .role-perm-toolbar .form-control,
    .role-perm-toolbar .custom-select {
        border-radius: 8px;
        border-color: var(--rp-border);
        min-height: 40px;
    }
    .role-perm-toolbar .form-control:focus,
    .role-perm-toolbar .custom-select:focus {
        border-color: var(--rp-navy);
        box-shadow: 0 0 0 0.15rem rgba(17, 28, 56, 0.12);
    }
    .role-perm-stats {
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
    }
    .role-perm-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: var(--rp-bg);
        border: 1px solid var(--rp-border);
        border-radius: 999px;
        padding: .35rem .75rem;
        font-size: .8125rem;
        color: var(--rp-navy);
        font-weight: 600;
        white-space: nowrap;
    }
    .role-perm-chip strong { color: var(--rp-red); }
    .role-perm-actions .btn {
        border-radius: 8px;
        font-weight: 600;
        font-size: .8125rem;
    }
    .role-perm-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
    }
    .role-perm-card {
        background: #fff;
        border: 1px solid var(--rp-border);
        border-radius: 10px;
        overflow: hidden;
        transition: border-color .15s ease, box-shadow .15s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .role-perm-card:hover {
        border-color: #cfd3e3;
        box-shadow: 0 4px 14px rgba(17, 28, 56, 0.06);
    }
    .role-perm-card.is-filtered-out { display: none !important; }
    .role-perm-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .85rem 1rem;
        background: var(--rp-bg);
        border-bottom: 1px solid var(--rp-border);
    }
    .role-perm-card-title {
        margin: 0;
        font-size: .9375rem;
        font-weight: 700;
        color: var(--rp-navy);
        line-height: 1.3;
    }
    .role-perm-card-meta {
        font-size: .75rem;
        color: var(--rp-muted);
        margin-top: .15rem;
    }
    .role-perm-card-body {
        padding: .85rem 1rem 1rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .35rem .75rem;
        flex: 1;
    }
    .role-perm-item {
        margin: 0;
        padding: .2rem 0;
    }
    .role-perm-item .custom-control-label {
        font-size: .8125rem;
        color: #4B4B4B;
        padding-top: 1px;
    }
    .role-perm-item .custom-control-input:checked ~ .custom-control-label::before {
        background-color: var(--rp-navy);
        border-color: var(--rp-navy);
    }
    .role-perm-empty {
        display: none;
        text-align: center;
        padding: 2.5rem 1rem;
        color: var(--rp-muted);
        border: 1px dashed var(--rp-border);
        border-radius: 10px;
        background: #fff;
    }
    .role-perm-empty.is-visible { display: block; }
    .role-perm-footer {
        position: sticky;
        bottom: 0;
        z-index: 15;
        background: rgba(255,255,255,.96);
        backdrop-filter: blur(6px);
        border-top: 1px solid var(--rp-border);
        padding: .85rem 0;
        margin-top: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .role-perm-footer .btn-primary {
        background: var(--rp-navy);
        border-color: var(--rp-navy);
        border-radius: 8px;
        font-weight: 600;
        min-width: 140px;
    }
    .role-perm-footer .btn-primary:hover {
        background: #0c1529;
        border-color: #0c1529;
    }
    .role-perm-footer .btn-outline-secondary {
        border-radius: 8px;
        font-weight: 600;
    }
    @media (max-width: 575.98px) {
        .role-perm-card-body { grid-template-columns: 1fr; }
        .role-perm-toolbar { top: 56px; }
    }
</style>

<div class="role-perm-page">
    <div class="card border-0 shadow-sm mb-1" style="border-radius: 10px;">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-lg-5 col-md-6 mb-1 mb-md-0">
                    <label for="name" class="font-weight-bold">{{ __('Role Name') }}</label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror" id="name"
                           placeholder="e.g. Sales Manager" value="{{ old('name', $role->name ?? '') }}" required autofocus>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-7 col-md-6">
                    <div class="role-perm-stats justify-content-md-end">
                        <span class="role-perm-chip">Modules: <strong id="rpVisibleModules">{{ $modules->count() }}</strong>/{{ $modules->count() }}</span>
                        <span class="role-perm-chip">Selected: <strong id="rpSelectedCount">{{ $selectedCount }}</strong>/{{ $totalPermissions }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="role-perm-toolbar">
        <div class="row align-items-center">
            <div class="col-lg-4 col-md-6 mb-1 mb-lg-0">
                <label for="rpModuleFilter" class="sr-only">Filter by module</label>
                <select id="rpModuleFilter" class="custom-select">
                    <option value="">All modules</option>
                    @foreach($modules as $moduleName => $modulePermissions)
                        <option value="{{ \Illuminate\Support\Str::slug($moduleName) }}">{{ $moduleName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4 col-md-6 mb-1 mb-lg-0">
                <label for="rpSearch" class="sr-only">Search permissions</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white" style="border-radius: 8px 0 0 8px; border-color: #E6E8F0;">
                            <i data-feather="search" style="width:14px;height:14px;"></i>
                        </span>
                    </div>
                    <input type="search" id="rpSearch" class="form-control" placeholder="Search module or permission…" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="role-perm-actions d-flex justify-content-lg-end flex-wrap" style="gap:.5rem;">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="rpSelectVisible">Select visible</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="rpClearVisible">Clear visible</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="rpClearAll">Clear all</button>
                </div>
            </div>
        </div>
    </div>

    <div class="role-perm-grid" id="rpGrid">
        @foreach($modules as $moduleName => $modulePermissions)
            @php
                $moduleSlug = \Illuminate\Support\Str::slug($moduleName);
                $moduleId = 'module_' . $moduleSlug;
                $checkedInModule = 0;
                if ($role) {
                    $checkedInModule = $modulePermissions->filter(fn ($p) => $role->hasPermissionTo($p->id))->count();
                } elseif (old('permissions')) {
                    $oldIds = collect((array) old('permissions'))->map(fn ($v) => (int) $v);
                    $checkedInModule = $modulePermissions->filter(fn ($p) => $oldIds->contains($p->id))->count();
                }
            @endphp
            <div class="role-perm-card"
                 data-module="{{ $moduleSlug }}"
                 data-module-label="{{ strtolower($moduleName) }}"
                 data-search="{{ strtolower($moduleName . ' ' . $modulePermissions->pluck('name')->implode(' ')) }}">
                <div class="role-perm-card-head">
                    <div>
                        <h5 class="role-perm-card-title">{{ $moduleName }}</h5>
                        <div class="role-perm-card-meta">
                            <span class="rp-module-checked">{{ $checkedInModule }}</span>/{{ $modulePermissions->count() }} selected
                        </div>
                    </div>
                    <div class="custom-control custom-checkbox mb-0">
                        <input type="checkbox"
                               class="custom-control-input rp-module-toggle"
                               id="{{ $moduleId }}"
                               data-target=".rp-perm-{{ $moduleSlug }}"
                               {{ $checkedInModule === $modulePermissions->count() && $modulePermissions->count() > 0 ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold" for="{{ $moduleId }}" style="font-size:.8125rem;">All</label>
                    </div>
                </div>
                <div class="role-perm-card-body">
                    @foreach($modulePermissions as $permission)
                        @php
                            $parts = explode('.', $permission->name, 2);
                            $action = $parts[1] ?? $permission->name;
                            $label = $actionLabels[$action] ?? ucwords(str_replace(['-', '_'], ' ', $action));
                            $isChecked = false;
                            if ($role) {
                                $isChecked = $role->hasPermissionTo($permission->id);
                            } elseif (old('permissions')) {
                                $isChecked = in_array($permission->id, (array) old('permissions'), false)
                                    || in_array((string) $permission->id, (array) old('permissions'), true);
                            }
                        @endphp
                        <div class="custom-control custom-checkbox role-perm-item">
                            <input type="checkbox"
                                   name="permissions[]"
                                   class="custom-control-input rp-perm rp-perm-{{ $moduleSlug }}"
                                   id="perm_{{ $permission->id }}"
                                   value="{{ $permission->id }}"
                                   data-module="{{ $moduleSlug }}"
                                   {{ $isChecked ? 'checked' : '' }}>
                            <label class="custom-control-label" for="perm_{{ $permission->id }}" title="{{ $permission->name }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="role-perm-empty" id="rpEmpty">
        <i data-feather="filter" style="width:28px;height:28px;margin-bottom:.5rem;"></i>
        <div class="font-weight-bold mb-25">No modules match your filter</div>
        <div class="font-small-2">Try another module name or clear the search.</div>
    </div>

    <div class="role-perm-footer">
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button class="btn btn-primary" type="submit">
            {{ $submitLabel ?? __('Submit') }}
        </button>
    </div>
</div>

@push('scripts')
<script>
(function () {
    function qs(sel, root) { return (root || document).querySelector(sel); }
    function qsa(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

    function updateCounts() {
        var selected = qsa('.rp-perm:checked').length;
        var total = qsa('.rp-perm').length;
        var selectedEl = qs('#rpSelectedCount');
        if (selectedEl) selectedEl.textContent = String(selected);

        qsa('.role-perm-card').forEach(function (card) {
            var perms = qsa('.rp-perm', card);
            var checked = perms.filter(function (p) { return p.checked; }).length;
            var meta = qs('.rp-module-checked', card);
            if (meta) meta.textContent = String(checked);
            var toggle = qs('.rp-module-toggle', card);
            if (toggle) {
                toggle.checked = perms.length > 0 && checked === perms.length;
                toggle.indeterminate = checked > 0 && checked < perms.length;
            }
        });

        var visible = qsa('.role-perm-card:not(.is-filtered-out)').length;
        var visibleEl = qs('#rpVisibleModules');
        if (visibleEl) visibleEl.textContent = String(visible);
        var empty = qs('#rpEmpty');
        if (empty) empty.classList.toggle('is-visible', visible === 0);
    }

    function applyFilter() {
        var moduleVal = (qs('#rpModuleFilter') || {}).value || '';
        var searchVal = ((qs('#rpSearch') || {}).value || '').trim().toLowerCase();

        qsa('.role-perm-card').forEach(function (card) {
            var moduleMatch = !moduleVal || card.getAttribute('data-module') === moduleVal;
            var searchMatch = !searchVal || (card.getAttribute('data-search') || '').indexOf(searchVal) !== -1;
            card.classList.toggle('is-filtered-out', !(moduleMatch && searchMatch));
        });
        updateCounts();
    }

    document.addEventListener('change', function (e) {
        var t = e.target;
        if (t.classList.contains('rp-module-toggle')) {
            var target = t.getAttribute('data-target');
            qsa(target).forEach(function (cb) {
                var card = cb.closest('.role-perm-card');
                if (card && card.classList.contains('is-filtered-out')) return;
                cb.checked = t.checked;
            });
            updateCounts();
        }
        if (t.classList.contains('rp-perm')) {
            updateCounts();
        }
        if (t.id === 'rpModuleFilter') {
            applyFilter();
        }
    });

    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'rpSearch') applyFilter();
    });

    var selectVisible = qs('#rpSelectVisible');
    if (selectVisible) {
        selectVisible.addEventListener('click', function () {
            qsa('.role-perm-card:not(.is-filtered-out) .rp-perm').forEach(function (cb) { cb.checked = true; });
            updateCounts();
        });
    }
    var clearVisible = qs('#rpClearVisible');
    if (clearVisible) {
        clearVisible.addEventListener('click', function () {
            qsa('.role-perm-card:not(.is-filtered-out) .rp-perm').forEach(function (cb) { cb.checked = false; });
            updateCounts();
        });
    }
    var clearAll = qs('#rpClearAll');
    if (clearAll) {
        clearAll.addEventListener('click', function () {
            qsa('.rp-perm').forEach(function (cb) { cb.checked = false; });
            updateCounts();
        });
    }

    updateCounts();
    if (window.feather) feather.replace();
})();
</script>
@endpush
