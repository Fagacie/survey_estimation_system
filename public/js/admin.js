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

        // First, filter modules (sections)
        const sections = document.querySelectorAll(".data-group");
        sections.forEach(function (section) {
            const sectionModule = section.dataset.module || "";
            if (moduleValue === "" || sectionModule === moduleValue) {
                section.style.display = "";
            } else {
                section.style.display = "none";
            }
        });

        // Then, filter rows by search text
        const rows = document.querySelectorAll(".data-body tr");
        rows.forEach(function (row) {
            if (row.classList.contains("empty-row")) return;

            const rowText = row.textContent.toLowerCase();
            if (rowText.includes(searchValue)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
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

