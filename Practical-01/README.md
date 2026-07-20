# Practical 1: Project Initiation, Requirement Analysis, Sitemap, Wireframe, and GitHub Setup

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  
**Faculty:** Faculty of Technology and Engineering (FTE)  
**Semester:** 3rd, 2026-27 (Odd)  

---

## 1. Problem Definition & Scope

### 1.1 Project Overview
**StudentHub** is a centralized, web-based campus portal designed for university students, faculty, and administrative staff at CHARUSAT. The portal unifies academic notices, event registrations, student profiles, and administrative record management into a responsive, accessible, and secure platform.

### 1.2 Target User Roles
1. **Guest / Visitor:** Can browse public pages (Home, About, Events overview, Contact, FAQ).
2. **Student:** Can register, log in, manage personal profile, browse and register for campus events, view announcements, and submit queries/feedback.
3. **Administrator:** Can manage student records (CRUD), create and moderate campus events with poster uploads, monitor user statistics on an admin dashboard, review audit logs, and resolve feedback.

### 1.3 Key Modules
- **Authentication & RBAC:** Secure sign-up, password hashing (`password_hash`), session management, role-based redirection, and logout.
- **Student Profile & Management:** Add, view, edit, search, and filter student records.
- **Event Management:** Admin-managed event catalog with flyer/poster uploads, category filtering, and registrations.
- **Interactive UI & Helpdesk:** Dynamic FAQs, feedback forms, and announcements loaded via JSON / REST APIs.
- **Admin Dashboard & Audit Logging:** Aggregated statistics and security audit logging.

### 1.4 Navigation Flow (10+ Pages)
1. `index.html` — Homepage with hero banner, quick links, and featured campus highlights
2. `about.html` — University & portal overview, mission, team, and contact points
3. `register.html` — Student registration form with client-side & server-side validation
4. `login.html` — Role-aware authentication portal
5. `student_dashboard.html` — Student personal portal with registered events and announcements
6. `admin_dashboard.html` — Administrator moderation panel with stats and audit trail
7. `events.html` — Campus events catalog with search, filter, and registration links
8. `profile.html` — Student user profile view and edit screen
9. `contact.html` — Campus contact directory and feedback submission form
10. `faq.html` — Interactive accordion-based frequently asked questions
11. `feedback.html` — Structured feedback and inquiry submission page

---

## 2. Key Questions & Technical Analysis

### Q1: What is a URL and what are the parts of a URL?
A **Uniform Resource Locator (URL)** is a specific type of Uniform Resource Identifier (URI) that specifies the address of a resource on the web and the mechanism used to retrieve it.

```text
https://studenthub.charusat.ac.in:443/portal/events.php?category=tech&page=1#schedule
\___/   \_______________________/ \__/ \________________/ \_________________/ \______/
  |                 |               |           |                  |             |
Scheme/          Host /           Port        Path            Query String    Fragment
Protocol      Domain Name
```

- **Scheme / Protocol (`https`):** Specifies the protocol used to transfer data (e.g., `http`, `https`, `ftp`). HTTPS encrypts traffic using TLS.
- **Host / Domain (`studenthub.charusat.ac.in`):** Identifies the web server hosting the resource.
- **Port (`:443`):** Network port number on the host. Default is `80` for HTTP and `443` for HTTPS (often omitted in standard browser URLs).
- **Path (`/portal/events.php`):** Hierarchical path to the resource or endpoint on the server filesystem.
- **Query String (`?category=tech&page=1`):** Key-value pairs containing non-hierarchical parameters passed to server scripts. Starts with `?` and separates pairs with `&`.
- **Fragment / Hash (`#schedule`):** Internal anchor pointing to a specific section or element ID within the HTML document. Not transmitted to the server in HTTP requests.

---

### Q2: How is an HTML file processed in a web browser?
When a browser navigates to a URL, it executes the **Critical Rendering Path (CRP)**:

1. **DNS Lookup & Connection Establishment:** The browser resolves the domain to an IP address via DNS, then opens a TCP connection (SYN-SYN/ACK-ACK handshake) and performs a TLS handshake for HTTPS.
2. **HTTP Request & Response:** The browser sends an `HTTP GET` request. The server returns the HTML document (`Content-Type: text/html`).
3. **DOM (Document Object Model) Construction:**
   - **Bytes to Characters:** Browser converts raw bytes to text characters based on character encoding (e.g., `UTF-8`).
   - **Tokenization:** Converts characters into W3C standard tokens (`<html>`, `<body>`, `<p>`).
   - **Node Creation:** Tokens become element nodes with attributes.
   - **Tree Building:** Nodes form a hierarchical tree structure (DOM Tree).
4. **CSSOM (CSS Object Model) Construction:** External stylesheets (`<link rel="stylesheet">`) and inline styles are fetched, parsed, and arranged into the CSSOM tree.
5. **Render Tree Creation:** DOM and CSSOM are combined. Invisible elements (`display: none`, `<head>`) are excluded. Computed styles are assigned to each visible node.
6. **Layout / Reflow:** The browser calculates the exact geometry, position, and dimensions of each node on the viewport.
7. **Painting / Repaint:** Vector pixels are filled with colors, borders, shadows, and text onto layers.
8. **Compositing:** Layers are drawn to the screen via the GPU in the correct stacking order.

---

### Q3: How will page navigation flow be managed among all HTML pages?
- **Unified Semantic Navigation Bar (`<nav>`):** Every HTML page contains an identical, accessible navigation bar with relative hyperlinks (`./index.html`, `./events.html`, etc.).
- **Active State Indication:** The current active page is highlighted using an `aria-current="page"` attribute and an `.active` CSS class.
- **Role-Based Links:** Public links are accessible directly; authenticated routes indicate session status (Student Portal vs. Admin Portal).
- **Consistent Breadcrumbs:** Sub-pages include semantic breadcrumb trails (`Home > Events > Tech Fest`) for intuitive hierarchy navigation.

---

### Q4: How will GitHub commits be maintained after each practical?
- **Branching Strategy:** Use the `main` branch for verified practical submissions. Feature branches (e.g., `feature/practical-02-html5`) can be merged sequentially.
- **Commit Message Convention:** Follow standard Conventional Commits:
  - `feat(p01): initiate StudentHub project structure, sitemap and wireframes`
  - `feat(p02): implement semantic HTML5 skeletons with accessibility tags`
  - `feat(p03): add responsive CSS grid and flexbox layout`
  - ...
- **Granular Commits:** Commit work systematically after completing each specific practical milestone.

---

## 3. StudentHub Project Directory Structure
```text
Practical-01/
├── README.md              # Project analysis, scope, questions, and architecture
├── sitemap.html           # Interactive visual sitemap of all 10+ portal pages
├── wireframes.html        # Low-fidelity UI wireframes for key screens
└── .gitignore             # Git ignore file for development artifacts
```

## 4. Verification & Submission Artefacts
- **Problem Scope:** Defined 3 distinct roles and 6 functional modules.
- **Pages Defined:** 11 distinct pages planned with URL mappings.
- **Visual Sitemap:** Included in `sitemap.html`.
- **Wireframes:** Included in `wireframes.html`.
