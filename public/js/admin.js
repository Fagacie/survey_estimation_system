document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const createButton = document.getElementById("createButton");

    const moduleFilter = document.getElementById("moduleFilter");

    // =========================================================
    // 1. Real-time Search & Module Filter Logic
    // =========================================================
    function applyFilters() {
        const searchValue = searchInput ? searchInput.value.toLowerCase().trim() : "";
        const moduleValue = moduleFilter ? moduleFilter.value.toLowerCase() : "";
        const sections = Array.from(document.querySelectorAll(".catalog-page .data-group"));
        let visibleItems = 0;

        sections.forEach(function (section) {
            const sectionModule = (section.dataset.module || "").toLowerCase();
            const moduleMatches = !moduleValue || sectionModule === moduleValue;
            const rows = Array.from(section.querySelectorAll(".data-body tr"));
            let sectionVisibleItems = 0;

            rows.forEach(function (row) {
                if (row.classList.contains("empty-row")) {
                    row.style.display = searchValue ? "none" : "";
                    return;
                }

                const rowText = row.textContent.toLowerCase();
                const matches = moduleMatches && rowText.includes(searchValue);
                row.style.display = matches ? "" : "none";
                if (matches) {
                    sectionVisibleItems += 1;
                    visibleItems += 1;
                }
            });

            const showSection = moduleMatches && (!searchValue || sectionVisibleItems > 0);
            section.style.display = showSection ? "" : "none";
        });

        const resultCount = document.getElementById("catalogResultCount");
        const noResults = document.getElementById("catalogNoResults");
        if (resultCount) {
            resultCount.textContent = visibleItems + (visibleItems === 1 ? " item" : " items");
        }
        if (noResults) {
            noResults.style.display = visibleItems === 0 ? "block" : "none";
            noResults.hidden = visibleItems > 0;
        }
    }

    if (searchInput) {
        searchInput.addEventListener("input", applyFilters);
    }
    
    if (moduleFilter) {
        moduleFilter.addEventListener("change", applyFilters);
    }

    // =========================================================
    // 2. Animation Feedback for Create Button
    // =========================================================
    if (createButton) {
        createButton.addEventListener("click", function () {
            createButton.classList.add("clicked");
            setTimeout(function () {
                createButton.classList.remove("clicked");
            }, 180);
        });
    }

    // =========================================================
    // 3. Event Delegation for Action Buttons (Edit / Delete)
    // =========================================================
    // Uses Event Delegation to ensure animation and confirmation work 
    // dynamically even after filtering or table re-renders.
    document.addEventListener("click", function (event) {
        const button = event.target.closest(".action-button");

        if (button) {
            // Click animation feedback
            button.classList.add("clicked");
            setTimeout(function () {
                button.classList.remove("clicked");
            }, 180);

            // Confirmation check if the button is a Delete action
            if (button.classList.contains("delete-button") || button.dataset.action === "delete") {
                const confirmed = confirm("Are you sure you want to delete this item?");
                if (!confirmed) {
                    event.preventDefault(); // Cancel form submission if rejected
                }
            }
        }
    });
});

