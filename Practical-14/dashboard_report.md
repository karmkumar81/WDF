# Practical 14: Admin Dashboard, RBAC, Dynamic Menus & Audit Logging

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  

---

## 1. Key Questions & Technical Analysis

### Q1: Are dashboard values loaded dynamically from the database?
- Yes. The KPI overview cards execute SQL aggregation queries dynamically on each request:
  - `SELECT COUNT(*) FROM students`
  - `SELECT COUNT(*) FROM events WHERE status = 'open'`
  - `SELECT COUNT(*) FROM registrations`
  - `SELECT COUNT(*) FROM users`

---

### Q2: Is access restricted to admin users?
- Yes. `auth_check.php` verifies both session identity and role permissions:
  ```php
  if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
      die("403 Forbidden: Administrator Access Required");
  }
  ```
- Unauthorized or regular student sessions are rejected immediately.

---

### Q3: Are role-based menus working?
- Yes. The navigation header conditionally alters its markup based on `$_SESSION['user_role']`:
  - **Admin Users:** Display links to Overview Stats, Student Records (CRUD), Event Management, and Audit Trail.
  - **Student Users:** Present only student-relevant views (My Profile, Campus Events).

---

### Q4: Are important admin actions recorded in `audit_logs`?
- Yes. When an administrator modifies a user's status (`toggle_status.php`) or performs system-level actions:
  ```php
  INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)
  ```
- Captures administrator identity, action timestamp, client IP address, and details for security traceability.

---

## 2. Advanced & Intermediate Extensions
- **Intermediate Extension:** Implemented instant **Active/Inactive User Status Toggle** (`toggle_status.php`) allowing admins to suspend or reactivate accounts with automatic audit trail entry.
- **Advanced Extension:** Relational `JOIN` queries between `registrations`, `students`, and `events` displaying live attendee bookings.
