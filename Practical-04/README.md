# Practical 4: JavaScript DOM Manipulation, Event Handling, and UI Interactivity

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  

---

## 1. Key Questions & Technical Analysis

### Q1: How are DOM elements selected and modified?
- **Selection Methods:**
  - `document.getElementById('id')`: Highly optimized lookup returning a single unique element.
  - `document.querySelectorAll('.selector')`: Standard method returning a static `NodeList` matching CSS selectors.
  - `document.querySelector('.selector')`: Returns the first matching element.
- **Modification Methods:**
  - `element.classList.add()`, `.remove()`, `.toggle()`: Safely alters CSS classes without clobbering existing classes.
  - `element.setAttribute('attr', 'value')`: Dynamically configures HTML attributes (e.g., `aria-expanded`, `data-theme`).
  - `element.textContent`: Safely updates text content without risking Cross-Site Scripting (XSS).

---

### Q2: Are event listeners attached correctly?
- Used `element.addEventListener('click', handler)` exclusively inside `document.addEventListener('DOMContentLoaded', ...)`.
- Avoided obsolete inline HTML handlers (`onclick="..."`) to maintain separation of concerns.
- Implemented event delegation and event bubbling safety checks (e.g., `e.target === modalOverlay` to detect backdrop clicks).

---

### Q3: Is localStorage used for remembering theme choice?
- Yes. When the user changes the theme:
  1. `localStorage.setItem('studenthub_theme', newTheme)` saves `'light'` or `'dark'` persistently in the browser's key-value store.
  2. On page reload, `localStorage.getItem('studenthub_theme')` immediately restores the chosen theme to `<html data-theme="...">`, avoiding visual flickering.

---

### Q4: Does interactivity improve usability without breaking accessibility?
- **ARIA Integration:** All interactive components update ARIA state in sync with DOM changes:
  - Hamburger menu sets `aria-expanded="true/false"`.
  - Accordion buttons manage `aria-expanded` attributes.
  - Dialog modal includes `role="dialog"`, `aria-modal="true"`, and keyboard `Escape` dismissal.
- **Keyboard Navigation:** Modals auto-focus the close button upon opening and return focus to the trigger button upon dismissal.

---

## 2. Components Implemented
1. **Notification Banner:** Dismissible banner with close button.
2. **Theme Switcher:** Dark/Light mode toggler with `localStorage` persistence.
3. **Hamburger Menu:** Mobile navigation toggle with `aria-expanded`.
4. **Content / Image Slider:** Autoplay, Next/Prev navigation, and indicator dots.
5. **Modal Popup:** Accessible dialog with backdrop click & `ESC` key listener.
6. **Collapsible FAQ Accordion:** Clean collapsible panels with active toggle indicators.
