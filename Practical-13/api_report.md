# Practical 13: PHP JSON API & Asynchronous Fetch CRUD

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  

---

## 1. Key Questions & Technical Analysis

### Q1: Do PHP endpoints return valid JSON responses?
- Yes. The PHP API always outputs `header('Content-Type: application/json; charset=UTF-8')`.
- All response structures adhere to a standard envelope:
  ```json
  {
    "success": true,
    "status": 200,
    "message": "Student created successfully.",
    "data": { "id": 21, "name": "..." },
    "timestamp": "2026-10-07T10:30:00+05:30"
  }
  ```

---

### Q2: Are GET and POST requests handled properly?
- Handled via `$_SERVER['REQUEST_METHOD']`:
  - `GET`: Retrieves all records or a single record (`?id=...`).
  - `POST`: Parses JSON body payload (`file_get_contents('php://input')`) to create new records.
  - `PUT`: Parses JSON body payload to update existing records.
  - `DELETE`: Deletes records by ID query parameter or JSON payload.

---

### Q3: Are loading, success, and error states shown on the frontend?
- **Loading State:** Table displays "Loading data via Fetch API..." during initial fetch and asynchronous operations.
- **Success State:** Dynamic green toast notification appears (`.toast-success`) confirming the action.
- **Error State:** Network failures or validation conflicts trigger red toast alerts (`.toast-error`) without refreshing the page.

---

### Q4: Is input validation performed before database operations?
- Yes. The backend inspects JSON input fields for required constraints (name, email RFC format, mobile 10-digit regex, department whitelist) before executing MySQL queries.
- Invalid requests return HTTP `400 Bad Request` with an array of specific field errors.

---

## 2. Advanced & Intermediate Extensions
- **Intermediate Extension:** Implemented **Debounced Search** (`setTimeout` with 300ms window) on the search input, minimizing unnecessary API requests while typing.
- **Advanced Extension:** REST HTTP status codes (`200 OK`, `201 Created`, `400 Bad Request`, `404 Not Found`, `409 Conflict`, `500 Internal Error`) with centralized error formatting.
