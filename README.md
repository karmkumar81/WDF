# Web Development Frameworks (ITUE203) - Lab Practicals

**Institution:** Charotar University of Science and Technology (CHARUSAT)  
**Faculty:** Faculty of Technology and Engineering (FTE)  
**Department:** Computer Engineering / Information Technology  
**Course Code & Name:** ITUE203 - Web Development Frameworks  
**Semester:** 3rd Semester, Academic Year 2026–27 (Odd)  
**Student ID:** 25CE124  
**Project Name:** StudentHub – Centralized Campus Web Portal  

---

## 📌 Project Overview
**StudentHub** is a semester-long, modular campus portal designed to unify university student services, event management, administrative record handling, and student communication into an accessible, responsive, and secure web application.

The repository follows progressive web engineering practices covering front-end semantic architecture, responsive UI design, DOM manipulation, client/server validation, RESTful JSON APIs, MySQL relational database management, session authentication, role-based access control (RBAC), and deployment.

---

## 📂 Practical Syllabus & Repository Structure

| Practical | Title | Core Technologies | Focus & Deliverables |
| :---: | :--- | :--- | :--- |
| **01** | Project Initiation, Requirement Analysis, Sitemap, Wireframe & GitHub Setup | Git, Draw.io, Markdown | Problem scope, user persona roles, 10-page sitemap, low-fidelity wireframes, README setup. |
| **02** | Semantic HTML5 Pages with Accessibility-Ready Structure | HTML5, WAI-ARIA | 10 semantic pages (Home, About, Register, Login, Dashboard, Events, Profile, Contact, Admin, FAQ), accessibility compliance. |
| **03** | Responsive UI Design using CSS Grid and Flexbox | CSS3, Flexbox, Grid | Mobile-first responsive layouts, navigation overlay, breakpoint media queries. |
| **04** | JavaScript DOM Manipulation, Event Handling & UI Interactivity | JavaScript (ES6+), DOM | Modal dialogs, carousel slider, collapsible FAQ accordions, theme switcher (Dark/Light mode with `localStorage`). |
| **05** | Registration Form with Frontend Validation & Error Handling | HTML5 Forms, JS Regex | Comprehensive client-side validation, live error messages, dynamic password strength meter. |
| **06** | Rendering External JSON Data using Fetch API, Search & Filter | Fetch API, Async JS, JSON | Dynamic card rendering, live debounced search, category filtering, client-side pagination. |
| **07** | PHP Form Processing with Server-Side Validation & CSV/JSON Storage | PHP 8, File I/O | POST handling, sanitization & server-side validation, safe file locking (`flock`), CSV/JSON record persistence. |
| **08** | MySQL Schema Design, ER Model, PDO Connectivity & Prepared Statements | MySQL, PDO | Normalized relational schema (Students, Events, Registrations), foreign key constraints, secure PDO connection wrapper. |
| **09** | Secure User Registration with Database Insert & Password Hashing | PHP, MySQLi, `password_hash` | Duplicate username/email checking, password hashing with BCRYPT, prepared statements against SQL injection. |
| **10** | Secure Login/Logout with Sessions, Role-Based Access & Timeout | PHP Sessions, RBAC | Session regeneration (`session_regenerate_id`), role-based redirection (Admin vs Student), idle session timeout, auth guards. |
| **11** | Student Management CRUD with Search and Filter | PHP, MySQLi | Complete Create, Read, Update, Delete (CRUD) operations, search and filter capabilities, CSV export, soft-delete pattern. |
| **12** | Event Management CRUD with Poster Upload & File Validation | PHP, MIME validation | Admin event CRUD, secure poster image upload (MIME check, file size cap, sanitized renaming), status management. |
| **13** | PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload | RESTful JSON API, Fetch API | Single-Page Application (SPA) interaction model, JSON endpoints, asynchronous CRUD without page refresh. |
| **14** | Admin Dashboard with Role-Based Access Control, Dynamic Menu & Audit Log | PHP, SQL Aggregation | Dynamic metric cards, latest registrations overview, active/inactive user status toggle, centralized `audit_logs` tracking. |
| **15** | Final Integration, Testing, Documentation & Deployment | PHP, Apache, MySQL | Master database consolidation (`studenthub_master.sql`), cross-module navigation flow, test plan, deployment instructions. |

---

## 🛠️ Technology Stack
- **Frontend:** HTML5, CSS3 (Flexbox & CSS Grid), Modern Vanilla JavaScript (ES6+)
- **Backend:** PHP 8.x
- **Database:** MySQL 8.x / MariaDB
- **Development Environment:** XAMPP / Apache Virtual Hosts
- **Version Control:** Git & GitHub

---

## 🚀 Getting Started Locally

### 1. Prerequisites
- [XAMPP](https://www.apachefriends.org/) (PHP 8.x + MySQL / Apache)
- Git installed on your system
- Web browser (Chrome, Firefox, or Edge)

### 2. Installation
1. Clone the repository into your web server root (e.g. `htdocs`):
   ```bash
   git clone git@github.com:karmkumar81/WDF.git
   ```
2. Start **Apache** and **MySQL** services from the XAMPP Control Panel.
3. Import the master database schema located at:
   ```text
   Practical-15/studenthub_master.sql
   ```
   into phpMyAdmin (`http://localhost/phpmyadmin`) or using MySQL CLI:
   ```bash
   mysql -u root -p < Practical-15/studenthub_master.sql
   ```
4. Access the application in your browser:
   ```text
   http://localhost/WDF/Practical-15/index.php
   ```

---

## 📄 Documentation
- Complete compiled lab work report: `25CE124_WDF_1_to_14.docx`
- Detailed Markdown reports and test cases are included within each practical folder.
