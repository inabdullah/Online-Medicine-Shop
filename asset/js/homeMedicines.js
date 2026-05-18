let selectedCategory = "";
let selectedType = "";

function escapeHtml(text) {
    return String(text ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function showMedicines(medicines) {
    const medicineList = document.getElementById("medicineList");

    if (!medicines || medicines.length === 0) {
        medicineList.innerHTML = '<p class="empty-message">No medicines found.</p>';
        return;
    }

    let html = "";

    medicines.forEach(function (medicine) {
        const stockText = Number(medicine.availability) > 0 ? "Available" : "Out of stock";
        const price = Number(medicine.price).toFixed(2);

        html += `
            <div class="medicine-card">
                <h3>${escapeHtml(medicine.name)}</h3>
                <p><strong>Vendor:</strong> ${escapeHtml(medicine.vendor_name)}</p>
                <p><strong>Genre:</strong> ${escapeHtml(medicine.category_name)}</p>
                <p><strong>Type:</strong> ${escapeHtml(medicine.category_type)}</p>
                <p><strong>Price:</strong> ${price} Tk</p>
                <p><strong>Availability:</strong> ${escapeHtml(stockText)} (${escapeHtml(medicine.availability)})</p>
            </div>
        `;
    });

    medicineList.innerHTML = html;
}

function loadMedicines() {
    const searchText = document.getElementById("searchText").value.trim();
    const vendor = document.getElementById("vendorFilter").value;
    selectedCategory = document.getElementById("genreFilter").value;

    const params = new URLSearchParams();
    params.set("q", searchText);
    params.set("vendor", vendor);
    params.set("genre", selectedCategory);
    params.set("type", selectedType);

    fetch("../api/medicines/search?" + params.toString())
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            if (data.success) {
                showMedicines(data.medicines);
            } else {
                document.getElementById("medicineList").innerHTML =
                    '<p class="empty-message">' + escapeHtml(data.message) + "</p>";
            }
        })
        .catch(function () {
            document.getElementById("medicineList").innerHTML =
                '<p class="empty-message">Could not load medicines.</p>';
        });
}

function setActiveButton(buttons, activeButton) {
    buttons.forEach(function (button) {
        button.classList.remove("active-filter");
    });
    activeButton.classList.add("active-filter");
}

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("searchText").addEventListener("keyup", loadMedicines);
    document.getElementById("vendorFilter").addEventListener("change", loadMedicines);
    document.getElementById("genreFilter").addEventListener("change", function () {
        selectedCategory = this.value;

        const activeCategoryButton = document.querySelector(".category-btn[data-category='" + selectedCategory + "']");
        if (activeCategoryButton) {
            setActiveButton(document.querySelectorAll(".category-btn"), activeCategoryButton);
        }

        loadMedicines();
    });

    document.querySelectorAll(".category-btn").forEach(function (button) {
        button.addEventListener("click", function () {
            selectedCategory = this.dataset.category;
            document.getElementById("genreFilter").value = selectedCategory;
            setActiveButton(document.querySelectorAll(".category-btn"), this);
            loadMedicines();
        });
    });

    document.querySelectorAll(".type-btn").forEach(function (button) {
        button.addEventListener("click", function () {
            selectedType = this.dataset.type;
            setActiveButton(document.querySelectorAll(".type-btn"), this);
            loadMedicines();
        });
    });

    document.getElementById("clearFilters").addEventListener("click", function () {
        selectedCategory = "";
        selectedType = "";
        document.getElementById("searchText").value = "";
        document.getElementById("vendorFilter").value = "";
        document.getElementById("genreFilter").value = "";
        setActiveButton(document.querySelectorAll(".category-btn"), document.querySelector(".category-btn[data-category='']"));
        setActiveButton(document.querySelectorAll(".type-btn"), document.querySelector(".type-btn[data-type='']"));
        loadMedicines();
    });

    loadMedicines();
});
