# Practical 9: User Registration Test Report & Security Analysis

**Course:** Web Development Frameworks (ITUE203)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  

---

## 1. Key Questions & Technical Analysis

### Q1: Is `password_hash()` used before storing passwords?
- Yes. The raw plain-text password is encrypted via:
  ```php
  $passwordHash = password_hash($password, PASSWORD_DEFAULT);
  ```
- This implements the standard bcrypt hashing algorithm with a cryptographically secure random salt automatically generated per password. Plaintext passwords are never written to the database.

---

### Q2: Is duplicate email/username checked?
- Yes. Before executing the `INSERT` statement, MySQLi prepared statements check whether the email already exists in `users` or if the enrollment ID exists in `students`:
  ```php
  $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  ```
- If records exist (`$stmt->num_rows > 0`), registration halts and returns: `"This email is already registered in the system."`

---

### Q3: Are frontend and backend validations consistent?
- Both client and server validate:
  - Full Name: 3–50 alphabetic characters.
  - Email: Valid RFC email address format.
  - Mobile: 10 digits starting with 6–9.
  - Password: Minimum 8 characters with lowercase, uppercase, and numbers.
- Backend validation guarantees security even if client JavaScript is bypassed or disabled.

---

## 2. Test Execution Summary

| Test Case | Scenario Description | Tested Inputs | Expected Behavior | Actual Behavior | Result |
| :---: | :--- | :--- | :--- | :--- | :---: |
| TC-01 | Valid Registration | Name: Alice Patel<br>Enrollment: 24IT001<br>Email: alice@charusat.edu.in<br>Password: Student@2026! | Account inserted in `users` and `students`; audit log created | Success banner shown; DB records committed | PASS |
| TC-02 | Invalid Email Format | Email: `notanemail` | Rejected with email error message | Error: "A valid email address is required" | PASS |
| TC-03 | Weak Password | Password: `pass` | Rejected (< 8 characters, no uppercase) | Error: "Password must be at least 8 chars..." | PASS |
| TC-04 | Password Mismatch | Pwd: `Student@2026!`<br>Confirm: `Different@2026!` | Form rejected | Error: "Passwords do not match" | PASS |
| TC-05 | Duplicate Email Attempt | Email: `alice@charusat.edu.in` | Rejected by DB duplicate check | Error: "This email is already registered in the system" | PASS |
| TC-06 | Duplicate Enrollment | Enrollment: `24IT001` | Rejected by DB duplicate check | Error: "This enrollment number is already registered" | PASS |
