// ==================================================================
// 1. GLOBAL ADD ITEM FUNCTION
// ==================================================================
window.addItemRow = function (module_Id, category_Id, btnElement = null) {
    let container = null;

    // Priority 1: Locate container relative to the clicked button element
    if (btnElement) {
        const sectionBlock = btnElement.closest('.section-card, .section-block');
        if (sectionBlock) {
            container = sectionBlock.querySelector('.section-items-container, .item-list');
        }
    }

    // Priority 2: Fallback to finding container via template ID
    if (!container) {
        container = document.getElementById(`container-${module_Id}-${category_Id}`);
    }

    if (!container) {
        console.error('Target item container not found for Module:', module_Id, 'Category:', category_Id);
        return;
    }

    // Clone item card from <template>, append to DOM container
    const newCard = createItemCard(module_Id, category_Id);
    container.appendChild(newCard);

    calculateItemTotal(newCard);
    calculateAllTotals();
};

// ==================================================================
// PAYMENT TERMS STATE
// ==================================================================
const defaultPaymentTerms = [
    { percentage: '40%', condition: 'Upon acceptance of LOA or issuance of PO' },
    { percentage: '40%', condition: 'Upon submission of First Draft Report' },
    { percentage: '20%', condition: 'Upon submission and acceptance of Final Report' },
];

let paymentTerms = JSON.parse(JSON.stringify(defaultPaymentTerms));

function renderPaymentTerms() {
    const paymentTermsList = document.getElementById('paymentTermsList');
    const paymentTermsHidden = document.getElementById('paymentTermsValue');
    if (!paymentTermsList || !paymentTermsHidden) return;

    paymentTermsList.innerHTML = '';

    paymentTerms.forEach((term, index) => {
        const row = document.createElement('div');
        row.className = 'payment-term-row d-flex align-items-center flex-wrap mb-2';
        row.innerHTML = `
            <span class="fw-bold small me-1">Payment ${index + 1} :</span>
            <input type="text" class="payment-inline-input term-percentage me-1" data-index="${index}" value="${term.percentage}" style="width:70px;">
            <span class="me-1">-</span>
            <input type="text" class="payment-inline-input term-condition flex-grow-1 me-1" data-index="${index}" value="${term.condition}">
            <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-term-btn" data-index="${index}">
                <i class="bi bi-x-lg"></i>
            </button>
        `;
        paymentTermsList.appendChild(row);
    });

    syncPaymentTermsValue();
}

function syncPaymentTermsValue() {
    const paymentTermsHidden = document.getElementById('paymentTermsValue');
    if (paymentTermsHidden) paymentTermsHidden.value = JSON.stringify(paymentTerms);
}

// ==================================================================
// 2. DOM CONTENT LOADED LISTENERS & EVENT DELEGATION
// ==================================================================
document.addEventListener('DOMContentLoaded', function () {

    function updateLastSavedTimestamp() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const lastSavedEl = document.getElementById('lastSaved') || document.getElementById('lastSavedTimestamp');
        if (lastSavedEl) {
            lastSavedEl.textContent = `Last autosaved at ${timeStr}`;
        }
    }

    // Helper function to show modal safely (Supports Bootstrap 5 or CSS display)
    function showModal() {
        const modalEl = document.getElementById('quotationSaveModal');
        if (!modalEl) {
            alert('Quotation successfully saved to database!');
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            const bsModal = window.bootstrap.Modal.getInstance(modalEl) || new window.bootstrap.Modal(modalEl);
            bsModal.show();
        } else {
            modalEl.style.display = 'block';
            modalEl.classList.add('show');
        }
    }

    // Helper function to hide modal safely
    function hideModal() {
        const modalEl = document.getElementById('quotationSaveModal');
        if (!modalEl) return;

        if (window.bootstrap && window.bootstrap.Modal) {
            const bsModal = window.bootstrap.Modal.getInstance(modalEl);
            if (bsModal) bsModal.hide();
        }
        modalEl.style.display = 'none';
        modalEl.classList.remove('show');
    }

    // ==========================================
    // PROJECT NUMBER AUTO-GENERATION
    // ==========================================
    const selectTypeEl   = document.getElementById('selectType');
    const clientEl       = document.getElementById('client');
    const numberPrefixEl = document.getElementById('number_prefix');
    const numberRunEl    = document.getElementById('number_running');
    const numberHiddenEl = document.getElementById('number');

    function getClientInitials(name) {
        return name.trim().split(/\s+/).filter(Boolean)
            .map(w => w.charAt(0).toUpperCase()).join('');
    }

    function getProjectTypeCode(selectEl) {
        const opt = selectEl.options[selectEl.selectedIndex];
        if (!opt || !opt.value) return '';
        return opt.textContent.split('-')[0].trim(); // "CP - Coastal Project" -> "CP"
    }

    function buildProjectNumberPrefix() {
        const typeCode   = getProjectTypeCode(selectTypeEl);
        const clientCode = getClientInitials(clientEl.value);
        if (!typeCode || !clientCode) return '';
        return `EHS/${typeCode}/${clientCode}/`;
    }

    function refreshProjectNumber() {
        const prefix = buildProjectNumberPrefix();
        numberPrefixEl.value = prefix;
        const running = numberRunEl.value.trim();
        numberHiddenEl.value = (prefix && running) ? `${prefix}${running}` : '';
    }

    async function fetchNextRunningNumber() {
        try {
            const res = await fetch('/project/next-number', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Request failed');
            const data = await res.json();
            return String(data.next_number).padStart(3, '0');
        } catch (err) {
            console.warn('Could not fetch next running number:', err);
            return '';
        }
    }

    if (selectTypeEl && clientEl && numberPrefixEl && numberRunEl && numberHiddenEl) {

        // EDIT MODE: if a full number already exists (from server), split it back out
        const existingValue = numberHiddenEl.value;
        const isEditMode = existingValue.includes('/');

        if (isEditMode) {
            const parts = existingValue.split('/');
            numberRunEl.value = parts.pop();
            numberPrefixEl.value = parts.join('/') + '/';
        }

        const autoSuggestIfEmpty = async () => {
            refreshProjectNumber();
            if (!isEditMode && !numberRunEl.value) {
                numberRunEl.value = await fetchNextRunningNumber();
                refreshProjectNumber();
            }
        };

        selectTypeEl.addEventListener('change', autoSuggestIfEmpty);
        clientEl.addEventListener('input', autoSuggestIfEmpty);

        // Running number stays user-editable
        numberRunEl.addEventListener('input', refreshProjectNumber);
        numberRunEl.addEventListener('blur', () => {
            const n = parseInt(numberRunEl.value, 10);
            if (!isNaN(n)) numberRunEl.value = String(n).padStart(3, '0');
            refreshProjectNumber();
        });
    }

    // ==========================================
    // SEPARATE DATE PICKERS & PERIOD CALCULATOR
    // ==========================================
    const startDateInput = document.getElementById('project_start_date');
    const endDateInput = document.getElementById('project_end_date');
    const periodInput = document.getElementById('period');

    if (startDateInput && endDateInput) {
        const handleDateChange = () => {
            if (startDateInput.value && endDateInput.value) {
                const [sYear, sMonth, sDay] = startDateInput.value.split('-').map(Number);
                const [eYear, eMonth, eDay] = endDateInput.value.split('-').map(Number);

                const start = new Date(sYear, sMonth - 1, sDay);
                const end = new Date(eYear, eMonth - 1, eDay);

                calculateAndSetPeriod(start, end);
                updateLastSavedTimestamp();
            }
        };

        startDateInput.addEventListener('change', handleDateChange);
        endDateInput.addEventListener('change', handleDateChange);

        if (startDateInput.value && endDateInput.value) {
            handleDateChange();
        }
    }

    function calculateAndSetPeriod(start, end) {
        if (!periodInput || isNaN(start) || isNaN(end) || end < start) {
            if (periodInput) periodInput.value = '';
            return;
        }

        const diffTime = Math.abs(end - start);
        const totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

        let years = end.getFullYear() - start.getFullYear();
        let months = end.getMonth() - start.getMonth();
        let days = end.getDate() - start.getDate();

        if (days < 0) {
            months--;
            const prevMonthLastDay = new Date(end.getFullYear(), end.getMonth(), 0).getDate();
            days += prevMonthLastDay;
        }

        if (months < 0) {
            years--;
            months += 12;
        }

        const weeks = Math.floor(days / 7);
        const remainingDays = days % 7;

        let parts = [];
        if (years > 0) parts.push(`${years} ${years === 1 ? 'Year' : 'Years'}`);
        if (months > 0) parts.push(`${months} ${months === 1 ? 'Month' : 'Months'}`);
        if (weeks > 0) parts.push(`${weeks} ${weeks === 1 ? 'Week' : 'Weeks'}`);
        if (remainingDays > 0) parts.push(`${remainingDays} ${remainingDays === 1 ? 'Day' : 'Days'}`);

        const breakdownStr = parts.length > 0 ? parts.join(' ') : '0 Days';
        periodInput.value = `${breakdownStr} ; ${totalDays} ${totalDays === 1 ? 'Day' : 'Days'}`;
    }

    // ==========================================
    // PREVIEW & VIEW SWITCHING LOGIC
    // ==========================================
    const editFormContainer = document.getElementById('editFormContainer');
    const previewModeContainer = document.getElementById('previewModeContainer');
    const previewBtn = document.getElementById('previewBtn');
    const backToEditBtn = document.getElementById('backToEditBtn');

    if (previewBtn) {
        previewBtn.addEventListener('click', function () {
            renderQuotationPreview();
            if (editFormContainer && previewModeContainer) {
                editFormContainer.classList.add('d-none');
                previewModeContainer.classList.remove('d-none');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    if (backToEditBtn) {
        backToEditBtn.addEventListener('click', function () {
            if (editFormContainer && previewModeContainer) {
                previewModeContainer.classList.add('d-none');
                editFormContainer.classList.remove('d-none');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    // ==========================================
    // QUOTATION SAVE & PRINT WORKFLOW
    // ==========================================
    const saveQuotationBtn = document.getElementById('saveQuotationBtn');
    const confirmQuotationSaveBtn = document.getElementById('confirmQuotationSaveBtn');
    const cancelQuotationSaveBtn = document.getElementById('cancelQuotationSaveBtn');

    // 1. Trigger Save to Database on Button Click
    if (saveQuotationBtn) {
        saveQuotationBtn.addEventListener('click', async function (e) {
            e.preventDefault();

            // Fetch CSRF Token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || document.querySelector('input[name="_token"]')?.value;

            if (!csrfToken) {
                alert('CSRF token missing! Please make sure <meta name="csrf-token" content="{{ csrf_token() }}"> is in your blade layout <head>.');
                return;
            }

            // Loop through dynamic item cards on page
            const items = [];
            const cardElements = document.querySelectorAll('.quotation-item-card, .item-card, tbody tr.item-row');

            cardElements.forEach((card, index) => {
                const moduleId = card.querySelector('.input-module-id, [name*="module_id"]')?.value || card.dataset.moduleId;
                const itemId = card.querySelector('.select-item, .item-name')?.value;
                const qty      = card.querySelector('.item-qty, .input-unit-qty, [name*="unit_qty"]')?.value;
                const days     = card.querySelector('.item-days, .input-days, [name*="days"]')?.value;
                const rate     = card.querySelector('.item-rate, .input-daily-rate, [name*="daily_rate"]')?.value;
                const markup   = card.querySelector('.item-markup, .input-markup, [name*="mark_up"]')?.value || 0;

                if (moduleId && itemId && qty && days && rate) {
                    items.push({
                        module_id:  parseInt(moduleId, 10),
                        item_id: parseInt(itemId, 10),
                        unit_qty:   parseInt(qty, 10),
                        days:       parseInt(days, 10),
                        daily_rate: parseFloat(rate),
                        mark_up:    parseFloat(markup)
                    });
                } else {
                    console.warn(`Card at index ${index} skipped due to missing inputs:`, { moduleId, itemId, qty, days, rate });
                }
            });

            if (items.length === 0) {
                alert('Please select or add at least one valid line item before saving.');
                return;
            }

            const projectIdInput = document.querySelector('#project_id, [name="project_id"]');
            const projectId = projectIdInput ? projectIdInput.value : null;

            // Show loading state
            const originalBtnHtml = saveQuotationBtn.innerHTML;
            saveQuotationBtn.disabled = true;
            saveQuotationBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> SAVING...';

                // Send AJAX POST Request
                try {
                    // 1. Read input values from the DOM
                    // Locate this block inside saveQuotationBtn in your JavaScript file:
                    const projectVal   = document.getElementById('project')?.value || document.querySelector('[name="project"]')?.value || '';
                    const clientVal    = document.getElementById('client')?.value || document.querySelector('[name="client"]')?.value || '';
                    const clientAddresVal = document.getElementById('client_address')?.value || document.querySelector('[name="client_address"]')?.value || '';
                    const projectNoVal = document.getElementById('number')?.value || document.querySelector('[name="number"]')?.value || '';
                    const picVal       = document.getElementById('pic')?.value || document.querySelector('[name="pic"]')?.value || '';

                        // ADD THESE TWO LINES TO READ THE DOM INPUTS:
                    const periodVal    = document.getElementById('period')?.value || document.querySelector('[name="period"]')?.value || '';
                    const noOfPicVal   = document.getElementById('pic_no')?.value || document.querySelector('[name="pic_no"]')?.value || '';
                    const additionalNotesVal = document.getElementById('additional_notes')?.value || '';

                    const payload = {
                        project_id: projectId || null,
                        project_name: projectVal,
                        client_name: clientVal,
                        client_address: clientAddresVal,
                        number: projectNoVal,
                        pic: picVal,
                        period: periodVal,        // <-- ADDED THIS KEY
                        pic_no: noOfPicVal,    // <-- ADDED THIS KEY
                        payment_terms: paymentTerms,
                        additional_notes: additionalNotesVal,
                        items: items
                    };

                    const response = await fetch('/quotation', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        updateLastSavedTimestamp();

                         // ADD THIS BLOCK: quo no displayed
                        const quotationNoField = document.getElementById('quotation_no');
                        if (quotationNoField && result.quotation_no) {
                                quotationNoField.value = result.quotation_no;
                        }

                        showModal(); 
                    } else {
                        console.error('Server error details:', result);
                        if (result.errors) {
                            const errorMsg = Object.values(result.errors).flat().join('\n');
                            alert('Validation Failed:\n' + errorMsg);
                        } else {
                            alert(result.message || 'Failed to save quotation.');
                        }
                    }
                } catch (err) {
                    console.error('AJAX Fetch failure:', err);
                    alert('Network error occurred: ' + err.message);
                } finally {
                    saveQuotationBtn.disabled = false;
                    saveQuotationBtn.innerHTML = originalBtnHtml;
                }
        });
    }

    // 2. "Cancel" / "No thanks" — Just close the modal
    if (cancelQuotationSaveBtn) {
        cancelQuotationSaveBtn.addEventListener('click', function () {
            hideModal();
        });
    }

    // 3. "Print / Export PDF" — Switch to Preview mode and trigger print
    if (confirmQuotationSaveBtn) {
        confirmQuotationSaveBtn.addEventListener('click', function () {
            hideModal();
            renderQuotationPreview();

            if (editFormContainer && previewModeContainer) {
                editFormContainer.classList.add('d-none');
                previewModeContainer.classList.remove('d-none');
            }

            setTimeout(function () {
                window.print();
            }, 150);
        });
    }

    // Global Event Delegation for Dynamic Buttons
    document.addEventListener('click', function (e) {
        const addBtn = e.target.closest('.add-item-btn');
        if (addBtn) {
            e.preventDefault();
            const sectionCard = addBtn.closest('.section-card') || addBtn.closest('.section-block');
            const module_Id = addBtn.getAttribute('data-module-id') || (sectionCard ? sectionCard.getAttribute('data-module-id') : null);
            const category_Id = addBtn.getAttribute('data-section-id') || (sectionCard ? sectionCard.getAttribute('data-section-id') : null);

            window.addItemRow(module_Id, category_Id, addBtn);
        }

        const removeBtn = e.target.closest('.remove-item-btn');
        if (removeBtn) {
            const itemCard = removeBtn.closest('.quotation-item-card');
            if (itemCard) {
                itemCard.remove();
                calculateAllTotals();
            }
        }
    });

    document.addEventListener('input', function (e) {
        updateLastSavedTimestamp();

        if (e.target.matches('.item-rate, .item-qty, .item-days, .item-markup, .input-daily-rate, .input-unit-qty, .input-days, .input-markup')) {
            const itemCard = e.target.closest('.quotation-item-card');
            if (itemCard) {
                calculateItemTotal(itemCard);
                calculateAllTotals();
            }
        }
    });

    document.addEventListener('change', function (e) {
        updateLastSavedTimestamp();

        if (e.target.matches('.item-category, .select-service')) {
            handleServiceChange(e.target);
        }

        if (e.target.matches('.item-name, .select-item')) {
            handleItemSelectChange(e.target);
        }
    });

    const tabButtons = document.querySelectorAll('button[data-bs-toggle="tab"], .nav-link[data-bs-toggle="tab"]');
    tabButtons.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function (e) {
            const moduleName = e.target.getAttribute('data-module-name') || e.target.innerText.trim();

            document.querySelectorAll('.active-module-title').forEach(titleEl => {
                titleEl.textContent = moduleName.toUpperCase();
            });

            updateActiveTabBreakdown();
            calculateAllTotals();
        });
    });

    // ==========================================
    // PAYMENT TERMS EVENT WIRING
    // ==========================================
    const paymentTermsList = document.getElementById('paymentTermsList');
    const addPaymentTermBtn = document.getElementById('addPaymentTermBtn');

    if (paymentTermsList && addPaymentTermBtn) {
        paymentTermsList.addEventListener('input', (e) => {
            const index = e.target.dataset.index;
            if (index === undefined) return;

            if (e.target.classList.contains('term-percentage')) {
                paymentTerms[index].percentage = e.target.value;
            } else if (e.target.classList.contains('term-condition')) {
                paymentTerms[index].condition = e.target.value;
            }
            syncPaymentTermsValue();
            updateLastSavedTimestamp();
        });

        paymentTermsList.addEventListener('click', (e) => {
            const btn = e.target.closest('.remove-term-btn');
            if (!btn) return;
            const index = parseInt(btn.dataset.index, 10);
            paymentTerms.splice(index, 1);
            renderPaymentTerms();
            updateLastSavedTimestamp();
        });

        addPaymentTermBtn.addEventListener('click', () => {
            paymentTerms.push({ percentage: '', condition: '' });
            renderPaymentTerms();
            updateLastSavedTimestamp();
        });

        renderPaymentTerms();
    }

    document.querySelectorAll('.quotation-item-card').forEach(card => calculateItemTotal(card));
    calculateAllTotals();
    updateLastSavedTimestamp();
});

// ==================================================================
// 3. HELPER & CALCULATIONS FUNCTIONS
// ==================================================================

function createItemCard(module_Id, category_Id) {
    const template = document.getElementById('item-card-template');
    const clone = template.content.cloneNode(true);
    const card = clone.querySelector('[data-item-row]') || clone.querySelector('.quotation-item-card');

    const inputModule = card.querySelector('.input-module-id');
    const inputSection = card.querySelector('.input-section-id');

    if (inputModule) inputModule.value = module_Id || '';
    if (inputSection) inputSection.value = category_Id || '';

    const daysInput = card.querySelector('.item-days, .input-days');
    const estimatedDays = Number(window.projectEstimation?.total_days || 0);
    if (daysInput && estimatedDays > 0) {
        daysInput.value = Math.max(1, Math.ceil(estimatedDays));
    }

    populateServicesDropdown(card, module_Id, category_Id);
    return card;
}

function populateServicesDropdown(cardNode, module_Id, category_Id) {
    const serviceSelect = cardNode.querySelector('.select-service, .item-category');
    if (!serviceSelect || !window.adminModulesTree) return;

    serviceSelect.innerHTML = '<option value="">Select Service...</option>';

    const modulesArray = Array.isArray(window.adminModulesTree)
        ? window.adminModulesTree
        : Object.values(window.adminModulesTree);

    const moduleData = modulesArray.find(m =>
        String(m.module_id ?? '').trim() === String(module_Id ?? '').trim()
    );

    if (!moduleData || !Array.isArray(moduleData.categories)) return;

    const categoryData = moduleData.categories.find(c =>
        String(c.category_id ?? '').trim() === String(category_Id ?? '').trim()
    );

    if (!categoryData || !Array.isArray(categoryData.services)) return;

    categoryData.services.forEach(serv => {
        const opt = document.createElement('option');
        opt.value = serv.service_id;
        opt.textContent = String(serv.service_name).toUpperCase();
        serviceSelect.appendChild(opt);
    });
}

function handleServiceChange(serviceSelect) {
    const cardNode = serviceSelect.closest('.quotation-item-card, tr, .item-row');
    if (!cardNode) return;

    const itemSelect = cardNode.querySelector('.select-item, .item-name');
    const module_Id = cardNode.querySelector('.input-module-id')?.value || cardNode.dataset.moduleId;
    const category_Id = cardNode.querySelector('.input-section-id')?.value || cardNode.dataset.sectionId;
    const selectedServiceId = serviceSelect.value;

    if (!itemSelect) return;
    itemSelect.innerHTML = '<option value="">Select Item...</option>';

    if (!selectedServiceId || !window.adminModulesTree) return;

    const modulesArray = Array.isArray(window.adminModulesTree)
        ? window.adminModulesTree
        : Object.values(window.adminModulesTree);

    const moduleData = modulesArray.find(m =>
        String(m.module_id ?? '').trim() === String(module_Id ?? '').trim()
    );

    if (!moduleData || !Array.isArray(moduleData.categories)) return;

    const categoryData = moduleData.categories.find(c =>
        String(c.category_id ?? '').trim() === String(category_Id ?? '').trim()
    );

    if (!categoryData || !Array.isArray(categoryData.services)) return;

    const matchedService = categoryData.services.find(s =>
        String(s.service_id).trim() === String(selectedServiceId).trim()
    );

    if (!matchedService || !Array.isArray(matchedService.items)) return;

    matchedService.items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item.item_id;
        opt.textContent = item.item_name;
        opt.dataset.internalRate = item.rate ?? 0;
        opt.dataset.unit = item.unit ?? '';
        itemSelect.appendChild(opt);
    });
}

function handleItemSelectChange(itemSelect) {
    const cardNode = itemSelect.closest('.quotation-item-card');
    const selectedOption = itemSelect.options[itemSelect.selectedIndex];
    const internalRateInput = cardNode.querySelector('.input-internal-rate');
    const unitDisplaySpan = cardNode.querySelector('.rate-unit-display');

    if (selectedOption && selectedOption.value) {
        const rateInput = cardNode.querySelector('.item-rate, .input-daily-rate');
        const markupInput = cardNode.querySelector('.item-markup, .input-markup');

        if (internalRateInput && selectedOption.dataset.internalRate !== undefined) {
            internalRateInput.value = parseFloat(selectedOption.dataset.internalRate || 0).toFixed(2);
        }

        const unitText = selectedOption.dataset.unit ? selectedOption.dataset.unit.trim() : '';
        if (unitDisplaySpan) {
            if (unitText) {
                unitDisplaySpan.textContent = `/ ${unitText}`;
                unitDisplaySpan.classList.remove('d-none');
            } else {
                unitDisplaySpan.textContent = '';
                unitDisplaySpan.classList.add('d-none');
            }
        }

        if (rateInput && selectedOption.dataset.internalRate !== undefined) {
            rateInput.value = parseFloat(selectedOption.dataset.internalRate || 0).toFixed(2);
        }
        if (markupInput && selectedOption.dataset.markup !== undefined) {
            markupInput.value = selectedOption.dataset.markup;
        }

        calculateItemTotal(cardNode);
        calculateAllTotals();
    } else {
        if (internalRateInput) internalRateInput.value = '';
        if (unitDisplaySpan) {
            unitDisplaySpan.textContent = '';
            unitDisplaySpan.classList.add('d-none');
        }
    }
}

function calculateItemTotal(card) {
    const rateInput = card.querySelector('.item-rate') || card.querySelector('.input-daily-rate');
    const qtyInput = card.querySelector('.item-qty') || card.querySelector('.input-unit-qty');
    const daysInput = card.querySelector('.item-days') || card.querySelector('.input-days');
    const markupInput = card.querySelector('.item-markup') || card.querySelector('.input-markup');

    const rate = parseFloat(rateInput ? rateInput.value : 0) || 0;
    const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
    const days = parseFloat(daysInput ? daysInput.value : 0) || 0;
    const markup = parseFloat(markupInput ? markupInput.value : 0) || 0;

    const baseCost = rate * qty * days;
    const total = baseCost * (1 + (markup / 100));

    const lineTotalEl = card.querySelector('.line-total') || card.querySelector('.line-item-total');
    if (lineTotalEl) {
        lineTotalEl.textContent = `MYR ${formatMoney(total)}`;
    }

    card.setAttribute('data-item-total', total);
}

function calculateAllTotals() {
    let grandTotal = 0;

    const allTabPanes = document.querySelectorAll('.tab-pane');

    allTabPanes.forEach(tabPane => {
        const sectionCards = tabPane.querySelectorAll('.section-card, .section-block');

        sectionCards.forEach(sectionCard => {
            let sectionSubtotal = 0;
            const items = sectionCard.querySelectorAll('.quotation-item-card');

            items.forEach(item => {
                sectionSubtotal += parseFloat(item.getAttribute('data-item-total')) || 0;
            });

            const subtotalEl = sectionCard.querySelector('.subtotal-amount') || sectionCard.querySelector('.section-subtotal');
            if (subtotalEl) {
                subtotalEl.textContent = `${formatMoney(sectionSubtotal)}`;
            }

            grandTotal += sectionSubtotal;
        });
    });

    updateActiveTabBreakdown();

    document.querySelectorAll('.overall-project-total, .grand-project-total').forEach(el => {
        el.textContent = `MYR ${formatMoney(grandTotal)}`;
    });

    updateStickyGrandTotalBar(grandTotal);
}

function updateActiveTabBreakdown() {
    const activeTab = document.querySelector('.tab-pane.active') || document.querySelector('.tab-pane.show.active');
    if (!activeTab) return;

    let activeModuleTotal = 0;
    const breakdownContainer = document.getElementById('serviceBreakdownContainer');
    if (breakdownContainer) breakdownContainer.innerHTML = '';

    const sectionCards = activeTab.querySelectorAll('.section-card, .section-block');

    sectionCards.forEach(sectionCard => {
        let sectionSubtotal = 0;
        const items = sectionCard.querySelectorAll('.quotation-item-card');

        const titleBtn = sectionCard.querySelector('.custom-dropdown-btn span') || sectionCard.querySelector('.section-title');
        const sectionTitle = titleBtn ? titleBtn.innerText.trim() : (sectionCard.getAttribute('data-section-title') || 'CATEGORY');

        items.forEach(item => {
            sectionSubtotal += parseFloat(item.getAttribute('data-item-total')) || 0;
        });

        activeModuleTotal += sectionSubtotal;

        if (breakdownContainer) {
            const breakdownRow = document.createElement('div');
            breakdownRow.className = 'd-flex justify-content-between py-2 border-bottom';
            breakdownRow.innerHTML = `
                <span class="small fw-semibold text-muted text-uppercase">${sectionTitle}</span>
                <span class="small fw-bold text-dark">MYR ${formatMoney(sectionSubtotal)}</span>
            `;
            breakdownContainer.appendChild(breakdownRow);
        }
    });

    document.querySelectorAll('.current-module-total').forEach(el => {
        el.textContent = `MYR ${formatMoney(activeModuleTotal)}`;
    });
}

function updateStickyGrandTotalBar(grandTotal) {
    const bottomSummaryLabel = document.querySelector('.all-modules-list');

    if (!bottomSummaryLabel) {
        console.warn('Target container .all-modules-list not found in HTML.');
        return;
    }

    bottomSummaryLabel.innerHTML = '';
    const selectedModules = [];

    document.querySelectorAll('.tab-pane').forEach(tabPane => {
        const tabId = tabPane.getAttribute('id');
        const tabBtn = document.querySelector(`[data-bs-target="#${tabId}"], [href="#${tabId}"]`);
        const isActiveTab = tabPane.classList.contains('active') || tabPane.classList.contains('show');

        let hasSelectedItem = false;
        const itemCards = tabPane.querySelectorAll('.quotation-item-card');

        itemCards.forEach(card => {
            const itemSelect = card.querySelector('.select-item, .item-name');
            if (itemSelect && itemSelect.value && itemSelect.value !== '') {
                hasSelectedItem = true;
            }
        });

        if (hasSelectedItem) {
            const modName = tabBtn 
                ? (tabBtn.getAttribute('data-module-name') || tabBtn.textContent.trim()).toUpperCase()
                : 'MODULE';

            selectedModules.push({
                name: modName,
                isActive: isActiveTab
            });
        }
    });

    if (selectedModules.length === 0) {
        bottomSummaryLabel.style.fontSize = '12px';
        bottomSummaryLabel.textContent = 'NO MODULES SELECTED';
        return;
    }

    selectedModules.forEach((mod, index) => {
        const modSpan = document.createElement('span');
        modSpan.textContent = mod.name;

        if (mod.isActive) {
            modSpan.className = 'small fw-bold text-primary text-decoration-underline';
        } else {
            modSpan.className = 'small fw-medium text-dark';
        }

        bottomSummaryLabel.appendChild(modSpan);

        if (index < selectedModules.length - 1) {
            const dot = document.createElement('span');
            dot.className = 'text-muted mx-1';
            dot.textContent = '•';
            bottomSummaryLabel.appendChild(dot);
        }
    });
}

function renderQuotationPreview() {
    const previewDoc = document.getElementById('quotationPreviewDocument');
    if (!previewDoc) return;

    const getVal = (idOrName) => {
        const el = document.getElementById(idOrName) || document.querySelector(`[name="${idOrName}"]`);
        return el ? (el.value.trim() || '-') : '-';
    };

    const setElementText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    };

    const startDate = getVal('project_start_date');
    const endDate = getVal('project_end_date');
    const periodVal = getVal('period');

    setElementText('preview-date-issued', new Date().toLocaleDateString('en-GB'));
    setElementText('preview-project', getVal('project'));
    
    const quotationNoVal = getVal('quotation_no');
    setElementText('preview-number', quotationNoVal !== '-' ? quotationNoVal : `${getVal('number')} (Draft — not yet saved)`);
  
    setElementText('preview-client', getVal('client'));
    setAddressHtml('preview-client_address', getVal('client_address'));
    setElementText('preview-period', (startDate !== '-' && endDate !== '-') ? `${startDate} to ${endDate} (${periodVal})` : periodVal);
    setElementText('preview-pic', getVal('pic'));
    setElementText('preview-pic_no', getVal('pic_no'));

    // PAYMENT TERMS & ADDITIONAL NOTES
    const previewPaymentTermsEl = document.getElementById('preview-payment-terms');
    if (previewPaymentTermsEl) {
        previewPaymentTermsEl.innerHTML = paymentTerms.length
            ? paymentTerms.map((t, i) =>
                `<div class="mb-1">Payment ${i + 1} : <strong>${t.percentage || '-'}</strong> - ${t.condition || '-'}</div>`
              ).join('')
            : '-';
    }

    setElementText('preview-additional-notes', getVal('additional_notes'));

    // SUBTOTAL / SST / GRAND TOTAL
    let subtotal = 0;
    document.querySelectorAll('.quotation-item-card').forEach(card => {
        subtotal += parseFloat(card.getAttribute('data-item-total')) || 0;
    });

    const sst = subtotal * 0.08;
    const grandTotalWithTax = subtotal + sst;

    setElementText('preview-subtotal', `MYR ${formatMoney(subtotal)}`);
    setElementText('preview-sst', `MYR ${formatMoney(sst)}`);
    document.querySelectorAll('.preview-grand-total').forEach(el => {
        el.textContent = `MYR ${formatMoney(grandTotalWithTax)}`;
    });

    let tableRowsHtml = '';
    let rowCounter = 1;

    document.querySelectorAll('.tab-pane').forEach(tabPane => {
        const tabId = tabPane.getAttribute('id');
        const tabBtn = document.querySelector(`[data-bs-target="#${tabId}"], [href="#${tabId}"]`);
        const moduleName = tabBtn ? (tabBtn.getAttribute('data-module-name') || tabBtn.textContent.trim()).toUpperCase() : 'MODULE';

        const itemCards = tabPane.querySelectorAll('.quotation-item-card');

        itemCards.forEach(card => {
            const itemSelect = card.querySelector('.select-item, .item-name');
            const selectedOption = itemSelect ? itemSelect.options[itemSelect.selectedIndex] : null;
            const itemName = selectedOption && selectedOption.value ? selectedOption.textContent.trim() : 'Unselected Item';
            const unitText = selectedOption && selectedOption.dataset.unit ? ` / ${selectedOption.dataset.unit}` : '';

            const qty = card.querySelector('.item-qty, .input-unit-qty')?.value || '0';
            const days = card.querySelector('.item-days, .input-days')?.value || '0';
            const rate = card.querySelector('.item-rate, .input-daily-rate')?.value || '0.00';
            const total = card.querySelector('.line-total, .line-item-total')?.textContent || 'MYR 0.00';

            // Get service name from the currently selected service dropdown
            const serviceSelect = card.querySelector('.select-service, .item-category');
            const selectedServiceId = serviceSelect ? serviceSelect.value : null;

            const module_Id = card.querySelector('.input-module-id')?.value;
            const category_Id = card.querySelector('.input-section-id')?.value;
            let categoryName = '';
            let serviceName = '';

            if (window.adminModulesTree) {
                const modulesArray = Array.isArray(window.adminModulesTree)
                    ? window.adminModulesTree
                    : Object.values(window.adminModulesTree);

                const moduleData = modulesArray.find(m =>
                    String(m.module_id ?? '').trim() === String(module_Id ?? '').trim()
                );

                if (moduleData && Array.isArray(moduleData.categories)) {
                    const categoryData = moduleData.categories.find(c =>
                        String(c.category_id ?? '').trim() === String(category_Id ?? '').trim()
                    );

                    if (categoryData) {
                        categoryName = categoryData.category_name || '';

                        if (selectedServiceId && Array.isArray(categoryData.services)) {
                            const serviceData = categoryData.services.find(s =>
                                String(s.service_id).trim() === String(selectedServiceId).trim()
                            );
                            if (serviceData) serviceName = serviceData.service_name || '';
                        }
                    }
                }
            }

            const subtitleParts = [categoryName, serviceName].filter(Boolean);
            const subtitleHtml = subtitleParts.length
                ? `<br><small class="text-muted">${subtitleParts.join(' &middot; ')}</small>`
                : '';

            tableRowsHtml += `
                <tr>
                    <td>
                        <span class="badge bg-light text-secondary border me-1">${moduleName}</span>
                        <strong>${itemName}</strong>
                        ${subtitleHtml}
                    </td>
                    <td class="text-center">${qty}</td>
                    <td class="text-center">${days}</td>
                    <td class="text-end">${formatMoney(parseFloat(rate))}${unitText}</td>
                    <td class="text-end fw-bold">${total}</td>
                </tr>
            `;
            rowCounter++;
        });
    });

    const tbody = document.getElementById('preview-table-body');
    if (tbody) {
        if (rowCounter === 1) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No items added to the quotation yet.</td>
                </tr>
            `;
        } else {
            tbody.innerHTML = tableRowsHtml;
        }
    }
}

function setAddressHtml(id, rawValue) {
    const el = document.getElementById(id);
    if (!el) return;
    const escaped = rawValue
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
    el.innerHTML = escaped.replace(/\n/g, '<br>');
}

function formatMoney(amount) {
    return (amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}