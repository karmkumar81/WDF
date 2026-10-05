# Practical 12: Event Management CRUD with Poster Upload & File Validation

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  

---

## 1. Key Questions & Technical Analysis

### Q1: Are add, update, and delete functionalities implemented correctly?
- **Add (`add_event.php`):** Validates event title, category, date, venue, seats, and securely stores uploaded flyer/poster image.
- **Update (`edit_event.php`):** Supports in-place updates. If a new poster is provided, the old image is unlinked from disk and the database is updated with the new filename.
- **Delete (`delete_event.php`):** Prompts for user confirmation, drops the database row, and deletes the poster file from the `uploads/` folder.

---

### Q2: Is the UI linked with the database correctly?
- The index view queries `events` dynamically, displaying formatted dates, venue locations, remaining seats, open/closed badges, and rendered poster image thumbnails.
- Category and status filters allow filtering by event type and availability.

---

### Q3: Are poster uploads validated for type and size?
- **File Size Verification:** `$file['size'] > 2 * 1024 * 1024` rejects files larger than 2MB.
- **File Extension Whitelisting:** Rejects all extensions except `['jpg', 'jpeg', 'png', 'webp']`.
- **MIME Type Inspection:** Uses PHP `finfo` (`finfo_file(..., $file['tmp_name'])`) to verify true binary content against `['image/jpeg', 'image/png', 'image/webp']`, preventing malicious scripts disguised as images (`shell.php.jpg`).

---

### Q4: Are success and failure messages displayed?
- Flash alert banners (`.alert-success` and `.alert-error`) appear prominently above forms and tables after redirect, displaying status feedback.

---

## 2. Advanced Security Enhancements Implemented
1. **Sanitized Unique Filenames:** `uniqid('event_', true) . '.' . $ext` prevents attackers from overwriting existing files or using path traversal payloads (`../../etc/passwd`).
2. **Directory Script Execution Block:** Created [`uploads/.htaccess`](uploads/.htaccess) with `Deny from all` on executable extensions (`php`, `pl`, `py`, `sh`, `exe`) to completely eliminate Remote Code Execution (RCE) risks.
