<x-app-layout containerClass="w-full px-8 py-8">
    <x-slot name="header">Invoices</x-slot>

    <div class="flex justify-between items-center mb-6 mt-2">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Invoices</h1>
        <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 text-sm font-medium transition-colors shadow-sm no-underline">
            <i class="fa-solid fa-plus text-xs"></i> New Invoice
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-6 flex items-start gap-3 text-sm">
            <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-800 border border-red-200 p-4 mb-6 flex items-start gap-3 text-sm">
            <i class="fa-solid fa-circle-xmark mt-0.5 text-red-600"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('invoices.index') }}" class="bg-white border border-slate-200 p-4 mb-6 flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Status</label>
            <select name="status" class="border border-slate-200 bg-slate-50 text-sm px-3 py-2 min-w-[140px] focus:outline-none focus:border-slate-400">
                <option value="">All Statuses</option>
                @foreach(['Draft', 'Issued', 'Paid', 'Overdue', 'Cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Client</label>
            <select name="client_id" class="border border-slate-200 bg-slate-50 text-sm px-3 py-2 min-w-[180px] focus:outline-none focus:border-slate-400">
                <option value="">All Clients</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice number..." class="border border-slate-200 bg-slate-50 text-sm px-3 py-2 min-w-[200px] focus:outline-none focus:border-slate-400">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 text-sm font-medium border border-slate-200 transition-colors">
                <i class="fa-solid fa-filter text-xs mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['status', 'client_id', 'search']))
                <a href="{{ route('invoices.index') }}" class="bg-white hover:bg-slate-50 text-slate-500 px-4 py-2 text-sm border border-slate-200 transition-colors no-underline">
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- INVOICE TABLE --}}
    @if($invoices->count() > 0)
        <div class="bg-white border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3 text-[10px] uppercase font-bold text-slate-500 tracking-wider">Invoice #</th>
                        <th class="text-left px-5 py-3 text-[10px] uppercase font-bold text-slate-500 tracking-wider">Client</th>
                        <th class="text-left px-5 py-3 text-[10px] uppercase font-bold text-slate-500 tracking-wider">Project</th>
                        <th class="text-left px-5 py-3 text-[10px] uppercase font-bold text-slate-500 tracking-wider">Issue Date</th>
                        <th class="text-right px-5 py-3 text-[10px] uppercase font-bold text-slate-500 tracking-wider">Amount (RM)</th>
                        <th class="text-center px-5 py-3 text-[10px] uppercase font-bold text-slate-500 tracking-wider">Status</th>
                        <th class="text-right px-5 py-3 text-[10px] uppercase font-bold text-slate-500 tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        @php $colors = $invoice->status_color; @endphp
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4 font-medium text-slate-900">
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="hover:text-teal-600 transition-colors no-underline">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $invoice->client->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $invoice->project->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $invoice->issue_date->format('d M Y') }}</td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-900">{{ number_format($invoice->grand_total, 2) }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-block px-2.5 py-1 text-[11px] font-semibold border {{ $colors['bg'] }} {{ $colors['text'] }} {{ $colors['border'] }} {{ $invoice->status === 'Cancelled' ? 'line-through' : '' }}">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('invoices.preview', $invoice->id) }}" class="text-slate-400 hover:text-teal-600 transition-colors no-underline" title="Preview PDF">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="text-slate-400 hover:text-slate-800 transition-colors no-underline" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if($invoice->status === 'Draft')
                                        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this invoice?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors" title="Delete">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $invoices->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white border border-slate-200 p-16 text-center">
            <div class="text-slate-300 mb-4"><i class="fa-solid fa-file-invoice text-5xl"></i></div>
            <div class="text-slate-500 mb-1 font-medium">No invoices found</div>
            <div class="text-slate-400 text-sm mb-6">Create your first invoice to get started.</div>
            <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 text-sm font-medium transition-colors no-underline">
                <i class="fa-solid fa-plus text-xs"></i> New Invoice
            </a>
        </div>
    @endif
</x-app-layout>
