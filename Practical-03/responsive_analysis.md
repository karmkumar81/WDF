# Practical 3: Responsive UI Design Analysis & Documentation

**Course:** Web Development Frameworks (ITUE203)  
**Layout Techniques:** CSS Grid, CSS Flexbox, Mobile-First Media Queries  

---

## 1. Key Questions & Technical Analysis

### Q1: How does the layout adapt for mobile, tablet, and desktop screens?
- **Mobile (< 640px):** Single-column layout. Navigation menu stacks vertically. Hero buttons stack. Event cards and statistics render in a single column (`grid-template-columns: 1fr`).
- **Tablet (640px – 1023px):** Multi-column grid activates. Stats grid displays 2 columns (`repeat(2, 1fr)`). Form fields arrange into 2-column paired rows (`form-row-2`). Event cards display 2 per row.
- **Desktop (≥ 1024px):** Navigation transitions to a horizontal inline bar (`flex-direction: row`). Event cards expand to 3 columns (`repeat(3, 1fr)`). Stats grid expands to 4 columns. Dashboard splits into an asynchronous 2-column view (3fr main content + 1fr sidebar).

---

### Q2: Which layout technique is used and why?
1. **Flexbox (`display: flex`):** Used for **one-dimensional** layouts (single row or column):
   - Navigation bar brand and links alignment (`justify-content: space-between`).
   - Hero action buttons and footer layout.
   - Form group label-to-input vertical stacks.
2. **CSS Grid (`display: grid`):** Used for **two-dimensional** layouts (rows and columns simultaneously):
   - Event cards catalog (`grid-template-columns: repeat(auto-fit, minmax(...))`).
   - Metric overview cards (`stats-grid`).
   - Two-column dashboard split (`3fr 1fr`).
   - Form field pairings (`form-row-2`).

---

### Q3: Are typography, spacing, colors, and contrast readable?
- **Fluid Typography:** Implemented `clamp(1.8rem, 4vw, 2.75rem)` ensuring headers adapt seamlessly to screen size without harsh jumps or overflowing.
- **Accessible Color Contrast:** Dark slate `#0f172a` against white `#ffffff` and `#f8fafc` exceeds WCAG AA 4.5:1 ratio.
- **Consistent Spacing:** Built on an 8px grid system (`0.5rem`, `1rem`, `1.5rem`, `2rem`) for predictability across viewports.

---

### Q4: Is CSS organized consistently without unnecessary inline styling?
- All layout rules, breakpoints, and component tokens are centralized in [`styles.css`](styles.css).
- Mobile-first methodology eliminates repetitive desktop overrides and keeps stylesheets clean, maintainable, and lightweight.
