/**
 * Practical 6: Rendering External JSON Data using Fetch API, Search and Filter
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

document.addEventListener("DOMContentLoaded", () => {
  // State variables
  let currentDataset = "events";
  let rawData = [];
  let filteredData = [];
  let currentPage = 1;
  let pageSize = 6;

  // DOM Elements
  const tabs = document.querySelectorAll(".tab-btn");
  const searchInput = document.getElementById("search-input");
  const categoryFilter = document.getElementById("category-filter");
  const sortSelect = document.getElementById("sort-select");
  const pageSizeSelect = document.getElementById("page-size-select");
  const cardsContainer = document.getElementById("cards-container");
  const paginationContainer = document.getElementById("pagination");
  const spinner = document.getElementById("spinner");
  const errorAlert = document.getElementById("error-alert");

  // Dataset File Paths
  const DATA_URLS = {
    events: "data/events.json",
    students: "data/students.json",
    faqs: "data/faqs.json"
  };

  /**
   * Fetch data from local JSON file with loading & error handling
   */
  async function loadData(datasetName) {
    showLoading(true);
    showError(null);
    cardsContainer.innerHTML = "";
    paginationContainer.innerHTML = "";

    try {
      const response = await fetch(DATA_URLS[datasetName]);
      if (!response.ok) {
        throw new Error(`HTTP Error ${response.status}: Failed to load ${datasetName}.json`);
      }
      rawData = await response.json();
      populateCategoryDropdown();
      applyFiltersAndRender(true);
    } catch (err) {
      showError(`Error loading dataset: ${err.message}. Please verify file location.`);
    } finally {
      showLoading(false);
    }
  }

  function showLoading(isLoading) {
    spinner.style.display = isLoading ? "block" : "none";
  }

  function showError(msg) {
    if (msg) {
      errorAlert.textContent = msg;
      errorAlert.style.display = "block";
    } else {
      errorAlert.style.display = "none";
    }
  }

  /**
   * Extract unique categories and populate filter dropdown
   */
  function populateCategoryDropdown() {
    categoryFilter.innerHTML = `<option value="all">All Categories</option>`;
    let categories = new Set();

    if (currentDataset === "events" || currentDataset === "faqs") {
      rawData.forEach(item => { if (item.category) categories.add(item.category); });
    } else if (currentDataset === "students") {
      rawData.forEach(item => { if (item.department) categories.add(item.department); });
    }

    categories.forEach(cat => {
      const opt = document.createElement("option");
      opt.value = cat;
      opt.textContent = cat;
      categoryFilter.appendChild(opt);
    });
  }

  /**
   * Apply Search, Filter, Sort and update Pagination
   */
  function applyFiltersAndRender(resetPage = false) {
    if (resetPage) currentPage = 1;

    const searchTerm = searchInput.value.trim().toLowerCase();
    const selectedCategory = categoryFilter.value;
    const sortMode = sortSelect.value;

    // 1. Filter
    filteredData = rawData.filter(item => {
      // Category filter check
      let matchesCategory = true;
      if (selectedCategory !== "all") {
        const itemCategory = item.category || item.department;
        matchesCategory = itemCategory === selectedCategory;
      }

      // Search keyword check
      let matchesSearch = true;
      if (searchTerm) {
        if (currentDataset === "events") {
          matchesSearch = item.title.toLowerCase().includes(searchTerm) ||
                          item.location.toLowerCase().includes(searchTerm);
        } else if (currentDataset === "students") {
          matchesSearch = item.name.toLowerCase().includes(searchTerm) ||
                          item.id.toLowerCase().includes(searchTerm) ||
                          item.course.toLowerCase().includes(searchTerm);
        } else if (currentDataset === "faqs") {
          matchesSearch = item.question.toLowerCase().includes(searchTerm) ||
                          item.answer.toLowerCase().includes(searchTerm);
        }
      }

      return matchesCategory && matchesSearch;
    });

    // 2. Sort
    if (sortMode === "name-asc" || sortMode === "name-desc") {
      filteredData.sort((a, b) => {
        let nameA = (a.title || a.name || a.question).toLowerCase();
        let nameB = (b.title || b.name || b.question).toLowerCase();
        if (sortMode === "name-asc") return nameA.localeCompare(nameB);
        return nameB.localeCompare(nameA);
      });
    }

    renderCards();
    renderPagination();
  }

  /**
   * Render sliced cards for current page
   */
  function renderCards() {
    if (filteredData.length === 0) {
      cardsContainer.innerHTML = `
        <div style="grid-column: 1/-1; text-align: center; padding: 2rem; color: var(--text-muted);">
          No matching records found for the current query.
        </div>`;
      return;
    }

    const startIndex = (currentPage - 1) * pageSize;
    const pageItems = filteredData.slice(startIndex, startIndex + pageSize);

    cardsContainer.innerHTML = pageItems.map(item => {
      if (currentDataset === "events") {
        return `
          <div class="data-card">
            <span class="badge">${escapeHtml(item.category)}</span>
            <h3>${escapeHtml(item.title)}</h3>
            <p><strong>Date:</strong> ${escapeHtml(item.date)}</p>
            <p><strong>Venue:</strong> ${escapeHtml(item.location)}</p>
            <p><strong>Seats:</strong> ${item.seats} available</p>
          </div>
        `;
      } else if (currentDataset === "students") {
        return `
          <div class="data-card">
            <span class="badge">${escapeHtml(item.department)}</span>
            <h3>${escapeHtml(item.name)}</h3>
            <p><strong>Enrollment ID:</strong> ${escapeHtml(item.id)}</p>
            <p><strong>Course:</strong> B.Tech ${escapeHtml(item.course)} (${escapeHtml(item.year)} Year)</p>
            <p><strong>Cumulative GPA:</strong> ${item.gpa}</p>
          </div>
        `;
      } else {
        return `
          <div class="data-card">
            <span class="badge">${escapeHtml(item.category)}</span>
            <h3>${escapeHtml(item.question)}</h3>
            <p style="color: var(--text-muted);">${escapeHtml(item.answer)}</p>
          </div>
        `;
      }
    }).join("");
  }

  /**
   * Render Pagination Buttons
   */
  function renderPagination() {
    const totalPages = Math.ceil(filteredData.length / pageSize) || 1;
    let html = `
      <button class="page-btn" id="prev-page" ${currentPage === 1 ? "disabled" : ""}>&laquo; Prev</button>
    `;

    for (let i = 1; i <= totalPages; i++) {
      html += `
        <button class="page-btn ${i === currentPage ? "active" : ""}" data-page="${i}">${i}</button>
      `;
    }

    html += `
      <button class="page-btn" id="next-page" ${currentPage === totalPages ? "disabled" : ""}>Next &raquo;</button>
      <span class="page-info">Total ${filteredData.length} records</span>
    `;

    paginationContainer.innerHTML = html;

    // Attach listeners
    document.getElementById("prev-page").addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--;
        renderCards();
        renderPagination();
      }
    });

    document.getElementById("next-page").addEventListener("click", () => {
      if (currentPage < totalPages) {
        currentPage++;
        renderCards();
        renderPagination();
      }
    });

    paginationContainer.querySelectorAll("[data-page]").forEach(btn => {
      btn.addEventListener("click", (e) => {
        currentPage = parseInt(e.target.dataset.page, 10);
        renderCards();
        renderPagination();
      });
    });
  }

  function escapeHtml(str) {
    if (!str) return "";
    return String(str).replace(/[&<>"']/g, m => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
    })[m]);
  }

  // Event Listeners
  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      tabs.forEach(t => t.classList.remove("active"));
      tab.classList.add("active");
      currentDataset = tab.dataset.dataset;
      searchInput.value = "";
      sortSelect.value = "default";
      loadData(currentDataset);
    });
  });

  searchInput.addEventListener("input", () => applyFiltersAndRender(true));
  categoryFilter.addEventListener("change", () => applyFiltersAndRender(true));
  sortSelect.addEventListener("change", () => applyFiltersAndRender(false));
  pageSizeSelect.addEventListener("change", (e) => {
    pageSize = parseInt(e.target.value, 10);
    applyFiltersAndRender(true);
  });

  // Initial load
  loadData("events");
});
