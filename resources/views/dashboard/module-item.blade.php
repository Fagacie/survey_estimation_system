<section class="adm-module-card data-group catalog-module" data-module="{{ strtolower($title ?? '') }}">
    <!-- MODULE HEADER -->
    <header class="adm-module-header">
        <div class="adm-module-title-wrap">
            <div class="adm-module-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <h2>{{ strtoupper($title ?? 'SURVEY DISCIPLINE') }}</h2>
            <span class="adm-module-count">
                {{ count($items ?? []) }} {{ count($items ?? []) === 1 ? 'item' : 'items' }}
            </span>
        </div>
        <div class="d-none d-md-block text-xs font-semibold text-slate-400">
            Reference Module #{{ $categoryId ?? '-' }}
        </div>
    </header>

    <!-- DATA TABLE -->
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Item Description</th>
                    <th style="width: 180px;">Category</th>
                    <th style="width: 180px;">Service</th>
                    <th style="width: 170px;">Internal Rate</th>
                    <th style="width: 200px;">Remark</th>
                    <th class="text-end" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody class="data-body">
                @forelse ($items as $index => $item)
                    @php
                        $unitModel = isset($units) ? $units->firstWhere('unit_id', $item['unit_id'] ?? null) : null;
                        $unitName = $unitModel->unit_name ?? '';
                    @endphp
                    <tr>
                        <td class="adm-row-num">{{ $index + 1 }}</td>
                        <td>
                            <div class="adm-item-name">
                                {{ $item['name'] ?? $item->name ?? 'Unnamed Item' }}
                            </div>
                        </td>
                        <td>
                            <span class="adm-tag adm-tag--category">
                                {{ $item['category_name'] ?? $item->category_name ?? 'General' }}
                            </span>
                        </td>
                        <td>
                            <span class="adm-tag adm-tag--service">
                                {{ $item['service'] ?? $item->service ?? 'Standard' }}
                            </span>
                        </td>
                        <td>
                            @if(!empty($item['rate'] ?? $item->rate ?? null))
                                <div class="adm-rate-badge">
                                    <span>RM {{ $item['rate'] ?? $item->rate }}</span>
                                    @if($unitName)
                                        <span class="adm-rate-unit">/ {{ $unitName }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-400 text-xs font-medium">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="adm-remark" title="{{ $item['description'] ?? $item->description ?? '' }}">
                                {{ $item['description'] ?? $item->description ?: '—' }}
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="adm-action-group">
                                <a href="{{ route('items.edit', $item['id'] ?? $item->id) }}"
                                   class="adm-action-btn"
                                   title="Edit Item"
                                   aria-label="Edit item {{ $item['name'] ?? '' }}">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <form action="{{ route('items.destroy', $item['id'] ?? $item->id) }}"
                                      method="POST"
                                      class="inline-block form-delete-item m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="adm-action-btn adm-action-btn--delete btn-delete-item"
                                            title="Delete Item"
                                            aria-label="Delete item {{ $item['name'] ?? '' }}"
                                            data-name="{{ $item['name'] ?? 'this item' }}">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0">
                            <div class="adm-empty-state">
                                <div class="adm-empty-icon"><i class="fa-regular fa-folder-open"></i></div>
                                <h3 class="adm-empty-title">No items in this module</h3>
                                <p class="adm-empty-text">No survey items have been catalogued under {{ $title ?? 'this module' }} yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>