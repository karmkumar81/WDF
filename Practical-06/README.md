# Practical 6: Rendering External JSON Data using Fetch API, Search and Filter

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  

---

## 1. Key Questions & Technical Analysis

### Q1: How is JSON fetched, parsed, and rendered?
1. **Fetching:** `fetch('data/events.json')` returns a Promise that resolves to the HTTP Response object.
2. **Parsing:** `await response.json()` parses the JSON text body into a native JavaScript Array/Object.
3. **Rendering:** The dataset is mapped to HTML template literals using `.map()` and injected safely into the DOM container using `container.innerHTML = htmlString`.

---

### Q2: Which array methods are used for search, filter, sort, and pagination?
- **Filter & Search:** `Array.prototype.filter()` produces a subset matching category criteria and search substring query:
  ```javascript
  rawData.filter(item => matchesCategory && matchesSearch);
  ```
- **Sorting:** `Array.prototype.sort()` sorts items alphabetically based on localized string comparison (`localeCompare()`):
  ```javascript
  filteredData.sort((a, b) => a.title.localeCompare(b.title));
  ```
- **Pagination:** `Array.prototype.slice(startIndex, endIndex)` segments the array into discrete pages without altering the master filtered array:
  ```javascript
  const pageItems = filteredData.slice(startIndex, startIndex + pageSize);
  ```
- **Rendering:** `Array.prototype.map().join('')` transforms data items into HTML markup strings.

---

### Q3: How are loading and error states handled?
- **Loading State:** A spinner element (`#spinner`) is displayed before calling `fetch()` and hidden in the `finally` block once the request resolves or rejects.
- **Error State:** Network failures or non-200 HTTP codes (`!response.ok`) are caught in a `try...catch` block. A user-friendly alert box (`#error-alert`) presents the error message without breaking the page.
- **Empty State:** If query filters match zero items, an informative "No matching records found" message is displayed.

---

### Q4: How is modularity maintained in JavaScript files?
- Functions are decomposed into single-responsibility helpers:
  - `loadData()` &mdash; handles HTTP retrieval and lifecycle.
  - `populateCategoryDropdown()` &mdash; inspects unique categories dynamically.
  - `applyFiltersAndRender()` &mdash; applies search, category filters, and sorting.
  - `renderCards()` &mdash; generates DOM elements for current page slice.
  - `renderPagination()` &mdash; generates pagination controls and page counters.
  - `escapeHtml()` &mdash; sanitizes output strings against XSS.

---

## 2. Included Datasets
- `data/events.json` (16 campus events across 4 categories)
- `data/students.json` (16 enrolled student profiles across 4 departments)
- `data/faqs.json` (15 categorized university FAQs)
