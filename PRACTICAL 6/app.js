let currentDataset = "events";
let rawData = [];
let filteredData = [];
let currentPage = 1;
const itemsPerPage = 6;

const tabEvents = document.getElementById("tabEvents");
const tabStudents = document.getElementById("tabStudents");
const searchInput = document.getElementById("searchInput");
const categoryFilter = document.getElementById("categoryFilter");
const sortBy = document.getElementById("sortBy");
const container = document.getElementById("itemsContainer");
const resultsInfo = document.getElementById("resultsInfo");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");
const pageIndicator = document.getElementById("pageIndicator");

function loadData(type) {
    currentDataset = type;
    currentPage = 1;
    searchInput.value = "";
    container.innerHTML = '<div class="loading">Loading records from JSON...</div>';

    const fileName = type === "events" ? "events.json" : "students.json";

    fetch(fileName)
        .then(function(response) {
            if (!response.ok) {
                throw new Error("Network response error: " + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            rawData = data;
            populateFilterOptions();
            applyFiltersAndRender();
        })
        .catch(function(error) {
            container.innerHTML = '<div class="no-results">Error loading JSON data: ' + error.message + '</div>';
        });
}

function populateFilterOptions() {
    categoryFilter.innerHTML = '<option value="all">All Categories</option>';
    if (currentDataset === "events") {
        categoryFilter.innerHTML += `
            <option value="Technical">Technical</option>
            <option value="Sports">Sports</option>
            <option value="Cultural">Cultural</option>
        `;
        sortBy.innerHTML = `
            <option value="default">Default Order</option>
            <option value="nameAsc">Title (A - Z)</option>
            <option value="nameDesc">Title (Z - A)</option>
            <option value="dateAsc">Date (Earliest First)</option>
        `;
    } else {
        categoryFilter.innerHTML = '<option value="all">All Branches</option>';
        categoryFilter.innerHTML += `
            <option value="CE">CE</option>
            <option value="IT">IT</option>
            <option value="EC">EC</option>
            <option value="ME">ME</option>
            <option value="CL">CL</option>
        `;
        sortBy.innerHTML = `
            <option value="default">Default Order</option>
            <option value="nameAsc">Name (A - Z)</option>
            <option value="nameDesc">Name (Z - A)</option>
            <option value="cpiDesc">CPI (Highest First)</option>
            <option value="cpiAsc">CPI (Lowest First)</option>
        `;
    }
}

function applyFiltersAndRender() {
    const query = searchInput.value.toLowerCase().trim();
    const selectedFilter = categoryFilter.value;
    const selectedSort = sortBy.value;

    filteredData = rawData.filter(function(item) {
        let matchesSearch = false;
        let matchesFilter = false;

        if (currentDataset === "events") {
            matchesSearch = item.title.toLowerCase().includes(query) || item.venue.toLowerCase().includes(query);
            matchesFilter = selectedFilter === "all" || item.category === selectedFilter;
        } else {
            matchesSearch = item.name.toLowerCase().includes(query) || item.roll_no.toLowerCase().includes(query);
            matchesFilter = selectedFilter === "all" || item.branch === selectedFilter;
        }

        return matchesSearch && matchesFilter;
    });

    if (selectedSort === "nameAsc") {
        filteredData.sort(function(a, b) {
            const nameA = currentDataset === "events" ? a.title : a.name;
            const nameB = currentDataset === "events" ? b.title : b.name;
            return nameA.localeCompare(nameB);
        });
    } else if (selectedSort === "nameDesc") {
        filteredData.sort(function(a, b) {
            const nameA = currentDataset === "events" ? a.title : a.name;
            const nameB = currentDataset === "events" ? b.title : b.name;
            return nameB.localeCompare(nameA);
        });
    } else if (selectedSort === "dateAsc") {
        filteredData.sort(function(a, b) {
            return new Date(a.date) - new Date(b.date);
        });
    } else if (selectedSort === "cpiDesc") {
        filteredData.sort(function(a, b) {
            return b.cpi - a.cpi;
        });
    } else if (selectedSort === "cpiAsc") {
        filteredData.sort(function(a, b) {
            return a.cpi - b.cpi;
        });
    }

    renderCards();
    updatePaginationControls();
}

function renderCards() {
    container.innerHTML = "";

    resultsInfo.textContent = `Showing ${filteredData.length} records found`;

    if (filteredData.length === 0) {
        container.innerHTML = '<div class="no-results">No records match your criteria.</div>';
        return;
    }

    const startIndex = (currentPage - 1) * itemsPerPage;
    const paginatedItems = filteredData.slice(startIndex, startIndex + itemsPerPage);

    paginatedItems.forEach(function(item) {
        const card = document.createElement("div");
        card.className = "card";

        if (currentDataset === "events") {
            const badgeClass = "badge badge-" + item.category.toLowerCase();
            card.innerHTML = `
                <span class="${badgeClass}">${item.category}</span>
                <h3>${item.title}</h3>
                <p><strong>Date:</strong> ${item.date}</p>
                <p><strong>Venue:</strong> ${item.venue}</p>
                <p><strong>Available Seats:</strong> ${item.seats}</p>
            `;
        } else {
            card.innerHTML = `
                <span class="badge badge-branch">Branch: ${item.branch}</span>
                <h3>${item.name}</h3>
                <p><strong>Roll No:</strong> ${item.roll_no}</p>
                <p><strong>Semester:</strong> ${item.semester}</p>
                <p><strong>CPI:</strong> ${item.cpi.toFixed(2)}</p>
            `;
        }

        container.appendChild(card);
    });
}

function updatePaginationControls() {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage) || 1;

    if (currentPage > totalPages) {
        currentPage = totalPages;
    }

    pageIndicator.textContent = `Page ${currentPage} of ${totalPages}`;
    prevBtn.disabled = currentPage <= 1;
    nextBtn.disabled = currentPage >= totalPages;
}

tabEvents.addEventListener("click", function() {
    tabEvents.classList.add("active");
    tabStudents.classList.remove("active");
    loadData("events");
});

tabStudents.addEventListener("click", function() {
    tabStudents.classList.add("active");
    tabEvents.classList.remove("active");
    loadData("students");
});

searchInput.addEventListener("input", function() {
    currentPage = 1;
    applyFiltersAndRender();
});

categoryFilter.addEventListener("change", function() {
    currentPage = 1;
    applyFiltersAndRender();
});

sortBy.addEventListener("change", function() {
    currentPage = 1;
    applyFiltersAndRender();
});

prevBtn.addEventListener("click", function() {
    if (currentPage > 1) {
        currentPage--;
        renderCards();
        updatePaginationControls();
    }
});

nextBtn.addEventListener("click", function() {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        renderCards();
        updatePaginationControls();
    }
});

loadData("events");
