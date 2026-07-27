# Practical 2: Accessibility Checklist & Semantic Evaluation

**Course:** Web Development Frameworks (ITUE203)  
**Standard:** WCAG 2.1 Level AA Guidelines  

---

## 1. Semantic Structure Verification
| Requirement | Status | Implementation Details |
| :--- | :---: | :--- |
| Primary Landmarks | PASS | Used `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<aside>`, and `<footer>` on all 11 pages. |
| Single `<h1>` per page | PASS | Every document defines exactly one distinct `<h1>` heading identifying page purpose. |
| Heading Hierarchy | PASS | Nested logically (`<h1>` &rarr; `<h2>` &rarr; `<h3>`) without skipping levels. |
| Semantic Tables | PASS | Included `<caption>`, `<thead>`, `<tbody>`, `<tr>`, and `<th scope="col">` for tabular data. |
| Semantic Lists | PASS | Used `<ol>` for sequential breadcrumb navigation and `<ul>` for menu items and notices. |

---

## 2. Accessibility (WCAG 2.1 AA) Features
1. **Skip Navigation Link (`.skip-link`):**
   - Implemented `<a href="#main-content" class="skip-link">Skip to main content</a>`.
   - Hidden by default; visible immediately on `Tab` focus for keyboard and screen-reader users.
2. **Accessible Forms:**
   - Every input field has an explicitly linked `<label for="...">`.
   - Required fields feature `aria-required="true"` and visible required asterisks (`aria-hidden="true"`).
   - Form fields include `aria-describedby` referencing hint text (`#email-hint`, `#pwd-hint`).
   - Appropriate `autocomplete` attributes (`name`, `email`, `tel`, `new-password`, `username`) aid autofill technologies.
3. **ARIA Landmarks & Current Page:**
   - Navigation bars labeled with `aria-label="Main Navigation"`.
   - Active page highlighted semantically using `aria-current="page"`.
   - Breadcrumb navigation labeled with `aria-label="Breadcrumb"`.
4. **Contrast & Focus Indicators:**
   - Text color `#0f172a` against `#f8fafc` background maintains > 7:1 contrast ratio (exceeding WCAG AA 4.5:1).
   - Explicit 2px-3px focus ring indicators provided on all interactive controls (`a:focus`, `input:focus`, `button:focus`).
