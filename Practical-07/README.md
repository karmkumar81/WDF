# Practical 7: PHP Form Processing with Server-Side Validation and File Storage

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  

---

## 1. Key Questions & Technical Analysis

### Q1: Is the form submitted using POST?
- Yes. The form specifies `method="POST"` and is processed when `$_SERVER['REQUEST_METHOD'] === 'POST'`.
- Using `POST` ensures user payloads are submitted in the HTTP request body rather than appended to the URL query string (preventing sensitive data logging in browser history and server access logs).

---

### Q2: Are inputs validated and sanitized server-side?
- **Sanitization:**
  - `trim()` removes spurious leading and trailing whitespace.
  - `filter_var($email, FILTER_SANITIZE_EMAIL)` strips illegal email characters.
  - `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` encodes HTML special characters (`<`, `>`, `&`, `"`, `'`) to neutralize XSS vulnerabilities.
- **Validation:**
  - Names checked against `/^[A-Za-z\s]{3,50}$/`.
  - Emails validated via `filter_var($email, FILTER_VALIDATE_EMAIL)`.
  - Mobile numbers validated via `/^[6-9]\d{9}$/`.
  - Department verified against a strict whitelist array (`['IT', 'CSE', 'CE', 'AIML']`).
  - Messages validated for a minimum 10-character length.

---

### Q3: Is file writing handled safely?
- **Exclusive Locking (`LOCK_EX`):**
  - For CSV writing: Uses `flock($csvHandle, LOCK_EX)` before `fputcsv()` to prevent race conditions when multiple users submit concurrently.
  - For JSON writing: Uses `file_put_contents(..., ..., LOCK_EX)` ensuring atomic writes.
- **File Integrity:** Files are stored within a protected `data/` directory with verified directory permissions.

---

### Q4: Are success and error responses displayed clearly?
- Inline field errors (`$errors['field']`) are highlighted near the corresponding inputs with red borders and error messages.
- A prominent success alert confirms when data is successfully validated and written to disk.
- Inputs retain their submitted values upon validation failure to minimize user retyping.

---

## 2. Advanced & Intermediate Extensions Implemented
1. **CSRF Protection:** Generates a secure random 32-byte session token (`bin2hex(random_bytes(32))`) and verifies it using `hash_equals()`.
2. **Dynamic Record Display:** Dynamically renders stored inquiries directly beneath the form in a table loaded from `data/submissions.json`.
