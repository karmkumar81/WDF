# Practical 5: Form Validation Test Report & Analysis

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  

---

## 1. Key Questions & Technical Analysis

### Q1: Are correct input types and attributes used?
- Used `type="text"`, `type="email"`, `type="tel"`, and `type="password"` with appropriate `autocomplete` hints (`name`, `email`, `tel`, `new-password`).
- Applied `novalidate` to the `<form>` to suppress inconsistent native browser popups in favor of custom accessible inline error messages.

---

### Q2: Are validation errors displayed near the relevant fields?
- Every input has a dedicated sibling `<div class="error-msg">` positioned immediately below the field.
- Erroneous fields receive red borders (`.is-invalid`), and valid fields receive green borders (`.is-valid`), giving clear visual guidance.

---

### Q3: Is password strength checked?
- Yes. The password strength meter scores the password dynamically on 4 criteria:
  1. Minimum 8 characters
  2. Mixed case (uppercase and lowercase)
  3. At least one numeric digit (0-9)
  4. At least one special symbol (`!@#$%^&*...`)
- Visual feedback updates smoothly from **Weak (Red)** &rarr; **Moderate (Orange)** &rarr; **Strong (Green)**.

---

### Q4: Is the form accessible using keyboard and screen-reader-friendly labels?
- Each input element is explicitly bound to a `<label for="...">`.
- Errors are clearly styled, contrast compliant, and toggled via JavaScript.
- All controls can be traversed seamlessly using standard `Tab` and `Shift+Tab` keystrokes.

---

## 2. Test Cases Coverage Table

| Test Case ID | Field | Input Value | Expected Result | Actual Result | Status |
| :---: | :--- | :--- | :--- | :--- | :---: |
| TC-01 | Full Name | `Al` | Reject (Min 3 characters) | Shows error | PASS |
| TC-02 | Full Name | `Alice Patel 123` | Reject (Contains numbers) | Shows error | PASS |
| TC-03 | Full Name | `Alice Patel` | Accept | Marked valid | PASS |
| TC-04 | Email | `alice.patel` | Reject (Missing domain) | Shows error | PASS |
| TC-05 | Email | `alice@charusat.edu.in` | Accept | Marked valid | PASS |
| TC-06 | Mobile | `12345` | Reject (Not 10 digits) | Shows error | PASS |
| TC-07 | Mobile | `5555555555` | Reject (Does not start with 6-9) | Shows error | PASS |
| TC-08 | Mobile | `9876543210` | Accept | Marked valid | PASS |
| TC-09 | Password | `pass123` | Reject (No uppercase, symbol, < 8 chars) | Weak indicator | PASS |
| TC-10 | Password | `Admin@2026!` | Accept (Meets all 4 conditions) | Strong indicator | PASS |
| TC-11 | Confirm Pwd | `Mismatch@123` | Reject (Must match password) | Shows error | PASS |
| TC-12 | Terms | Unchecked | Reject form submission | Shows error | PASS |
