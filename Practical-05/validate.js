/**
 * Practical 5: Registration Form with Frontend Validation & Password Strength Meter
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registration-form");
  const successAlert = document.getElementById("success-alert");

  // Form Fields
  const fullname = document.getElementById("fullname");
  const email = document.getElementById("email");
  const mobile = document.getElementById("mobile");
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirm-password");
  const course = document.getElementById("course");
  const year = document.getElementById("year");
  const genderRadios = document.getElementsByName("gender");
  const terms = document.getElementById("terms");

  // Strength meter elements
  const strengthFill = document.getElementById("strength-fill");
  const strengthText = document.getElementById("strength-text");

  // Regular Expression Definitions
  const REGEX = {
    fullname: /^[A-Za-z\s]{3,50}$/,
    email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
    mobile: /^[6-9]\d{9}$/,
    password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/
  };

  // Helper: Show/Hide Error
  function setError(inputElement, errorId, show) {
    const errorEl = document.getElementById(errorId);
    if (show) {
      if (inputElement && inputElement.classList) {
        inputElement.classList.add("is-invalid");
        inputElement.classList.remove("is-valid");
      }
      errorEl.classList.add("visible");
    } else {
      if (inputElement && inputElement.classList) {
        inputElement.classList.remove("is-invalid");
        inputElement.classList.add("is-valid");
      }
      errorEl.classList.remove("visible");
    }
  }

  // Real-time Field Validators
  function validateFullName() {
    const isValid = REGEX.fullname.test(fullname.value.trim());
    setError(fullname, "fullname-error", !isValid);
    return isValid;
  }

  function validateEmail() {
    const isValid = REGEX.email.test(email.value.trim());
    setError(email, "email-error", !isValid);
    return isValid;
  }

  function validateMobile() {
    const isValid = REGEX.mobile.test(mobile.value.trim());
    setError(mobile, "mobile-error", !isValid);
    return isValid;
  }

  function evaluatePasswordStrength(pwd) {
    let score = 0;
    if (pwd.length >= 8) score++;
    if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) score++;
    if (/\d/.test(pwd)) score++;
    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pwd)) score++;

    if (pwd.length === 0) {
      strengthFill.style.width = "0%";
      strengthFill.style.backgroundColor = "transparent";
      strengthText.textContent = "Password Strength: Empty";
    } else if (score <= 1) {
      strengthFill.style.width = "25%";
      strengthFill.style.backgroundColor = "#ef4444";
      strengthText.textContent = "Password Strength: Weak";
    } else if (score === 2 || score === 3) {
      strengthFill.style.width = "65%";
      strengthFill.style.backgroundColor = "#f59e0b";
      strengthText.textContent = "Password Strength: Moderate";
    } else {
      strengthFill.style.width = "100%";
      strengthFill.style.backgroundColor = "#16a34a";
      strengthText.textContent = "Password Strength: Strong";
    }

    return score >= 4;
  }

  function validatePassword() {
    const isStrong = evaluatePasswordStrength(password.value);
    const isValid = REGEX.password.test(password.value);
    setError(password, "password-error", !isValid);
    return isValid;
  }

  function validateConfirmPassword() {
    const matches = confirmPassword.value.length > 0 && confirmPassword.value === password.value;
    setError(confirmPassword, "confirm-password-error", !matches);
    return matches;
  }

  function validateCourse() {
    const isValid = course.value !== "";
    setError(course, "course-error", !isValid);
    return isValid;
  }

  function validateYear() {
    const isValid = year.value !== "";
    setError(year, "year-error", !isValid);
    return isValid;
  }

  function validateGender() {
    const isSelected = Array.from(genderRadios).some(r => r.checked);
    const errorEl = document.getElementById("gender-error");
    if (isSelected) errorEl.classList.remove("visible");
    else errorEl.classList.add("visible");
    return isSelected;
  }

  function validateTerms() {
    const isChecked = terms.checked;
    setError(terms, "terms-error", !isChecked);
    return isChecked;
  }

  // Event Listeners for Real-time Feedback
  fullname.addEventListener("input", validateFullName);
  email.addEventListener("input", validateEmail);
  mobile.addEventListener("input", validateMobile);
  password.addEventListener("input", () => {
    validatePassword();
    if (confirmPassword.value) validateConfirmPassword();
  });
  confirmPassword.addEventListener("input", validateConfirmPassword);
  course.addEventListener("change", validateCourse);
  year.addEventListener("change", validateYear);
  genderRadios.forEach(r => r.addEventListener("change", validateGender));
  terms.addEventListener("change", validateTerms);

  // Form Submission Handler
  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const isNameValid = validateFullName();
    const isEmailValid = validateEmail();
    const isMobileValid = validateMobile();
    const isPasswordValid = validatePassword();
    const isConfirmValid = validateConfirmPassword();
    const isCourseValid = validateCourse();
    const isYearValid = validateYear();
    const isGenderValid = validateGender();
    const isTermsValid = validateTerms();

    const formIsValid = isNameValid && isEmailValid && isMobileValid &&
      isPasswordValid && isConfirmValid && isCourseValid &&
      isYearValid && isGenderValid && isTermsValid;

    if (formIsValid) {
      successAlert.classList.add("visible");
      successAlert.scrollIntoView({ behavior: "smooth" });
      form.reset();
      evaluatePasswordStrength("");
      // Reset classes
      document.querySelectorAll(".form-control").forEach(el => {
        el.classList.remove("is-valid");
      });
    } else {
      successAlert.classList.remove("visible");
    }
  });
});
