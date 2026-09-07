# Practical 8: MySQL Schema Design, ER Model, PDO Connectivity, and Prepared Statements

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  

---

## 1. Key Questions & Technical Analysis

### Q1: Is the schema normalized properly?
- Yes. The database adheres strictly to **Third Normal Form (3NF)**:
  - **1NF:** All fields contain atomic scalar data types. No multi-valued lists.
  - **2NF:** The Many-to-Many relationship between students and events is split into the associative relation `registrations` with composite uniqueness `(student_id, event_id)`.
  - **3NF:** No transitive dependencies. Student academic attributes are separated from user login credentials.

---

### Q2: Are primary keys and foreign keys correctly defined?
- **Primary Keys:** Every table defines a surrogate auto-incrementing integer `id PRIMARY KEY`.
- **Foreign Keys:**
  - `students.user_id` &rarr; `users.id` (`ON DELETE SET NULL ON UPDATE CASCADE`)
  - `registrations.student_id` &rarr; `students.id` (`ON DELETE CASCADE ON UPDATE CASCADE`)
  - `registrations.event_id` &rarr; `events.id` (`ON DELETE CASCADE ON UPDATE CASCADE`)
  - `audit_logs.user_id` &rarr; `users.id` (`ON DELETE SET NULL ON UPDATE CASCADE`)
- **Unique Constraints:** `users.email`, `students.enrollment_no`, and `(student_id, event_id)`.

---

### Q3: Is the database connection handled securely?
- Implemented in `db.php` using both PDO and MySQLi:
  - **PDO Exception Mode:** `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` ensures fatal errors trigger catchable exceptions rather than leaking database paths to end users.
  - **Emulated Prepares Disabled:** `PDO::ATTR_EMULATE_PREPARES => false` forces native prepared statements on the MySQL server, preventing SQL injection exploits.
  - **UTF-8 Charset:** `utf8mb4` prevents character-encoding based SQL injection attacks.

---

## 2. Advanced Extension: Stored Procedure
- Added `sp_RegisterStudentForEvent` in `schema.sql`:
  - Enforces transactional seat booking and duplicate registration checks entirely inside MySQL.
