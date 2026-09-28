# Practical 11: Student Management CRUD with Search, Filter & Soft Delete

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  

---

## 1. Key Questions & Technical Analysis

### Q1: Are SELECT, INSERT, UPDATE, and DELETE operations working?
- **SELECT (Read):** Implemented in `index.php` using parameterized multi-condition queries with wildcards for search and department filters.
- **INSERT (Create):** Implemented in `add_student.php` using prepared statements with duplicate enrollment detection.
- **UPDATE (Edit):** Implemented in `edit_student.php` safely bound to primary key IDs (`WHERE id = ?`).
- **DELETE (Delete):** Implemented in `delete_student.php` with confirmation modal and soft delete support (`UPDATE students SET status = 'inactive'`).

---

### Q2: Is search by name/email/course implemented?
- Yes. The query dynamically binds parameterized `LIKE ?` filters:
  ```sql
  WHERE (name LIKE ? OR email LIKE ? OR enrollment_no LIKE ?) AND department = ?
  ```
- Protects completely against SQL injection through MySQLi parameter binding (`bind_param`).

---

### Q3: Are delete operations confirmed before execution?
- Yes. The delete action link features a JavaScript confirmation trigger:
  ```html
  onclick="return confirm('Are you sure you want to delete or deactivate student ...?');"
  ```
- Prevents accidental record removal or deactivation.

---

## 2. Advanced & Intermediate Extensions
- **Intermediate Extension:** `export_csv.php` allows instant download of current search and filter results into an Excel-ready CSV document.
- **Advanced Extension:** Soft delete logic preserves academic audit integrity by flagging `status = 'inactive'` rather than permanently truncating relational records.
