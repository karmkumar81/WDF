# Practical 15: Final Integration, Testing, Documentation, and Local Deployment

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  
**Faculty:** Faculty of Technology and Engineering (FTE)  
**Semester:** 3rd, 2026-27 (Odd)  

---

## 1. Project Overview
This repository contains the complete, structured implementation of all **15 Practicals** for the ITUE203 Web Development Frameworks course at CHARUSAT. Each practical has been developed in a dedicated folder strictly fulfilling the problem definition, key questions, dataset requirements, and extensions specified in the official practical list:

- **`Practical-01/`**: Project initiation, problem scope, user roles, interactive sitemap, wireframes, and Git configuration.
- **`Practical-02/`**: 11 Semantic HTML5 pages with WCAG 2.1 AA accessibility checklist, skip-to-content link, and accessible forms.
- **`Practical-03/`**: Responsive layouts for Home, About, Register, Dashboard, and Events with CSS Grid, Flexbox, and mobile-first media queries.
- **`Practical-04/`**: JavaScript DOM manipulation, image/content slider, accessible modal dialog, collapsible FAQ accordion, notification banner, hamburger menu, and dark/light theme switcher with `localStorage`.
- **`Practical-05/`**: Student registration form with real-time regular expression validation, password strength meter, and error handling.
- **`Practical-06/`**: External JSON data rendering via Fetch API (`events.json`, `students.json`, `faqs.json` with >= 15 records each), live search, multi-criteria category filter, alphabetical sorting, and pagination.
- **`Practical-07/`**: PHP form processing with server-side sanitization, regex validation, atomic file storage in CSV and JSON formats using `flock(LOCK_EX)`, CSRF token protection, and stored record viewing.
- **`Practical-08/`**: 3NF normalized MySQL database schema (`schema.sql`), Mermaid ER model, secure PHP database connector (`db.php`) supporting PDO and MySQLi with native prepared statements and error handling.
- **`Practical-09/`**: Secure user registration with duplicate email/username check, `password_hash()`, MySQLi prepared statements, and audit logging.
- **`Practical-10/`**: Secure login/logout with `password_verify()`, session ID regeneration, 15-minute session inactivity timeout, role-based access control (RBAC), and persistent remember-me tokens.
- **`Practical-11/`**: Student management CRUD module with parameterized search, department filter, JavaScript delete confirmation, CSV export, and soft delete functionality.
- **`Practical-12/`**: Event management CRUD with flyer/poster file upload, file size validation (<2MB), extension whitelisting, MIME type inspection, image renaming, and `.htaccess` script execution prevention.
- **`Practical-13/`**: RESTful PHP JSON API (`GET`, `POST`, `PUT`, `DELETE`) with standard HTTP status codes, consumed by an asynchronous Fetch API SPA with debounced live search and modal dialogs.
- **`Practical-14/`**: Admin dashboard with real-time SQL aggregation metrics, role-based dynamic navigation bar, active/inactive user status toggle, and administrative audit logging.
- **`Practical-15/`**: Final integration suite, master database SQL dump (`studenthub_master.sql`), master launcher hub (`index.php`), local XAMPP and Apache Virtual Host deployment guide, and end-to-end test report.

---

## 2. Launching the Integration Suite
To test the entire system:
1. Import [`Practical-15/studenthub_master.sql`](studenthub_master.sql) into MySQL using phpMyAdmin or MySQL CLI.
2. Open `Practical-15/index.php` in a web browser via local server (e.g. `http://localhost/PRACTICALS/Practical-15/index.php`).
