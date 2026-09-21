# Practical 10: Secure Login/Logout, Sessions, and Role-Based Access Control

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  

---

## 1. Key Questions & Technical Analysis

### Q1: Are sessions started and destroyed securely?
- **Starting:** `session_start()` is initialized after configuring strict cookie parameters: `httponly: true` (blocks XSS theft), `samesite: 'Lax'` (blocks CSRF cross-origin leakage).
- **Session ID Regeneration:** `session_regenerate_id(true)` is invoked immediately upon successful credential validation, preventing Session Fixation attacks.
- **Destroying:** `logout.php` empties `$_SESSION = []`, expires the session cookie via `setcookie(session_name(), '', time() - 42000, ...)`, and executes `session_destroy()`.

---

### Q2: Is `password_verify()` used correctly?
- Yes. When the user logs in, their plaintext password is tested against the database hash using:
  ```php
  password_verify($password, $user['password_hash'])
  ```
- This function performs constant-time string comparison, effectively neutralizing side-channel timing attacks.

---

### Q3: Is unauthorized dashboard access blocked?
- Yes. The middleware `requireAuth($role)` in `auth_check.php` runs at the very beginning of both `student_dashboard.php` and `admin_dashboard.php`.
- Unauthenticated visitors are intercepted before any HTML is sent to the client and redirected with an HTTP `Location: login.php` header.

---

### Q4: Is session timeout implemented?
- Yes. A 15-minute (900 seconds) inactivity threshold is checked on every page load:
  ```php
  if (time() - $_SESSION['last_activity'] > 900) {
      session_destroy();
      header("Location: login.php?msg=Session expired due to inactivity");
      exit;
  }
  $_SESSION['last_activity'] = time();
  ```

---

### Q5: Are admin and student users redirected to appropriate dashboards?
- Upon successful authentication in `login.php`:
  - If `$user['role'] === 'admin'`, the server redirects to `admin_dashboard.php`.
  - If `$user['role'] === 'student'`, the server redirects to `student_dashboard.php`.
- If an authenticated student attempts to manually access `admin_dashboard.php`, `requireAuth('admin')` blocks access and redirects them back to `student_dashboard.php`.
