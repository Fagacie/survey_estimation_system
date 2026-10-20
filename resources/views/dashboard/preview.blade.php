{{-- resources/views/dashboard/preview.blade.php --}}
<div id="quotationPreviewDocument" class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200/80 shadow-sm font-sans text-slate-800">
    <!-- HEADER BLOCK -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 mb-6 border-b border-slate-200 gap-4">
        <div>
            <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-sky-700 bg-sky-50 px-2.5 py-1 rounded-md border border-sky-100 mb-2">
                <i class="fa-solid fa-file-invoice"></i> Commercial Estimate
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight font-heading m-0" style="font-family: 'Space Grotesk', sans-serif;">
                Official Quotation
            </h2>
            <p class="text-xs text-slate-500 mt-1 mb-0">Date Issued: <span id="preview-date-issued" class="font-semibold text-slate-700">-</span></p>
        </div>
        <div class="text-left sm:text-right bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-lg w-full sm:w-auto border sm:border-0 border-slate-200/60">
            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Total Estimate</span>
            <div class="text-2xl sm:text-3xl font-bold text-sky-800 tracking-tight preview-grand-total" style="font-family: 'Space Grotesk', sans-serif;">
                MYR 0.00
            </div>
        </div>
    </div>

    <!-- PROJECT DETAILS GRID -->
    <div class="bg-slate-50/80 rounded-xl border border-slate-200/70 p-4 sm:p-5 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Project Name</span>
                <strong class="text-sm font-bold text-slate-900 block" id="preview-project">-</strong>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Quotation No.</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-sky-800 border border-sky-200/60" id="preview-number">
                    -
                </span>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Client Entity</span>
                <strong class="text-sm font-semibold text-slate-800 block" id="preview-client">-</strong>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Survey Period</span>
                <span class="text-xs font-medium text-slate-700 block" id="preview-period">-</span>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Person in Charge</span>
                <span class="text-xs font-medium text-slate-700 block" id="preview-pic">-</span>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Contact No.</span>
                <span class="text-xs font-medium text-slate-700 block" id="preview-pic_no">-</span>
            </div>
        </div>
    </div>

    <!-- ITEMS TABLE -->
    <div class="overflow-x-auto rounded-lg border border-slate-200 mb-6">
        <table class="table w-full text-left text-xs mb-0">
            <thead class="bg-slate-50 text-slate-500 border-b border-slate-200">
                <tr>
                    <th style="width: 48px;" class="text-center px-3 py-2.5 font-bold uppercase tracking-wider">#</th>
                    <th class="px-4 py-2.5 font-bold uppercase tracking-wider">Item Description</th>
                    <th style="width: 70px;" class="text-center px-3 py-2.5 font-bold uppercase tracking-wider">Qty</th>
                    <th style="width: 70px;" class="text-center px-3 py-2.5 font-bold uppercase tracking-wider">Days</th>
                    <th style="width: 130px;" class="text-end px-4 py-2.5 font-bold uppercase tracking-wider">Rate (MYR)</th>
                    <th style="width: 140px;" class="text-end px-4 py-2.5 font-bold uppercase tracking-wider">Amount (MYR)</th>
                </tr>
            </thead>
            <tbody id="preview-table-body" class="divide-y divide-slate-100">
                <tr>
                    <td colspan="6" class="text-center text-slate-400 py-6 font-medium">No items added to the quotation yet.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- PAYMENT TERMS & ADDITIONAL NOTES -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-slate-50/60 rounded-lg border border-slate-200/60 p-4">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
                <i class="fa-solid fa-handshake mr-1 opacity-70"></i> Payment Terms
            </span>
            <div id="preview-payment-terms" class="text-xs text-slate-700 leading-relaxed font-medium">-</div>
        </div>
        <div class="bg-slate-50/60 rounded-lg border border-slate-200/60 p-4">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
                <i class="fa-solid fa-notes-medical mr-1 opacity-70"></i> Additional Remarks
            </span>
            <div id="preview-additional-notes" class="text-xs text-slate-700 leading-relaxed font-medium whitespace-pre-wrap">-</div>
        </div>
    </div>

    <!-- FOOTER TOTALS -->
    <div class="flex justify-end items-center pt-4 border-t border-slate-200">
        <div class="text-right">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-3">Grand Total:</span>
            <span class="text-xl sm:text-2xl font-bold text-sky-800 preview-grand-total" style="font-family: 'Space Grotesk', sans-serif;">
                MYR 0.00
            </span>
        </div>
    </div>
</div>