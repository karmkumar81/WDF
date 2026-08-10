/**
 * Practical 4: JavaScript DOM Manipulation, Event Handling, and UI Interactivity
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

document.addEventListener("DOMContentLoaded", () => {
  /* ==========================================================
   * 1. Theme Switcher with localStorage Persistence
   * ========================================================== */
  const themeToggleBtn = document.getElementById("theme-toggle");
  const htmlRoot = document.documentElement;

  // Retrieve saved theme or default to light
  const currentTheme = localStorage.getItem("studenthub_theme") || "light";
  htmlRoot.setAttribute("data-theme", currentTheme);
  updateThemeButtonText(currentTheme);

  themeToggleBtn.addEventListener("click", () => {
    const isDark = htmlRoot.getAttribute("data-theme") === "dark";
    const newTheme = isDark ? "light" : "dark";

    htmlRoot.setAttribute("data-theme", newTheme);
    localStorage.setItem("studenthub_theme", newTheme);
    updateThemeButtonText(newTheme);
  });

  function updateThemeButtonText(theme) {
    themeToggleBtn.textContent = theme === "dark" ? "☀️ Light Mode" : "🌙 Dark Mode";
  }

  /* ==========================================================
   * 2. Notification Banner Dismissal
   * ========================================================== */
  const banner = document.getElementById("notification-banner");
  const bannerCloseBtn = document.getElementById("banner-close");

  bannerCloseBtn.addEventListener("click", () => {
    banner.classList.add("hidden");
  });

  /* ==========================================================
   * 3. Hamburger Menu Toggle (Mobile Nav)
   * ========================================================== */
  const hamburgerBtn = document.getElementById("hamburger-btn");
  const navLinks = document.getElementById("nav-links");

  hamburgerBtn.addEventListener("click", () => {
    const isExpanded = navLinks.classList.toggle("open");
    hamburgerBtn.setAttribute("aria-expanded", String(isExpanded));
  });

  /* ==========================================================
   * 4. Content / Image Slider
   * ========================================================== */
  const slides = document.querySelectorAll(".slide");
  const dots = document.querySelectorAll(".dot");
  const prevBtn = document.getElementById("prev-slide");
  const nextBtn = document.getElementById("next-slide");
  let activeSlideIndex = 0;
  let autoplayInterval;

  function showSlide(index) {
    if (index >= slides.length) activeSlideIndex = 0;
    else if (index < 0) activeSlideIndex = slides.length - 1;
    else activeSlideIndex = index;

    slides.forEach((slide, idx) => {
      slide.classList.toggle("active", idx === activeSlideIndex);
    });

    dots.forEach((dot, idx) => {
      dot.classList.toggle("active", idx === activeSlideIndex);
    });
  }

  prevBtn.addEventListener("click", () => {
    showSlide(activeSlideIndex - 1);
    resetAutoplay();
  });

  nextBtn.addEventListener("click", () => {
    showSlide(activeSlideIndex + 1);
    resetAutoplay();
  });

  dots.forEach((dot) => {
    dot.addEventListener("click", (e) => {
      const targetIdx = parseInt(e.target.dataset.slide, 10);
      showSlide(targetIdx);
      resetAutoplay();
    });
  });

  function startAutoplay() {
    autoplayInterval = setInterval(() => {
      showSlide(activeSlideIndex + 1);
    }, 4000);
  }

  function resetAutoplay() {
    clearInterval(autoplayInterval);
    startAutoplay();
  }

  startAutoplay();

  /* ==========================================================
   * 5. Modal Popup Dialog with ESC Key and Backdrop Click
   * ========================================================== */
  const modalOverlay = document.getElementById("modal-overlay");
  const openModalBtn = document.getElementById("open-modal-btn");
  const closeModalBtn = document.getElementById("modal-close");
  const confirmModalBtn = document.getElementById("modal-confirm");

  function openModal() {
    modalOverlay.classList.add("active");
    closeModalBtn.focus();
  }

  function closeModal() {
    modalOverlay.classList.remove("active");
    openModalBtn.focus();
  }

  openModalBtn.addEventListener("click", openModal);
  closeModalBtn.addEventListener("click", closeModal);
  confirmModalBtn.addEventListener("click", closeModal);

  // Close when clicking background outside of the modal box
  modalOverlay.addEventListener("click", (e) => {
    if (e.target === modalOverlay) {
      closeModal();
    }
  });

  // Close on Escape keyboard key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modalOverlay.classList.contains("active")) {
      closeModal();
    }
  });

  /* ==========================================================
   * 6. Collapsible FAQ Accordion
   * ========================================================== */
  const accordionHeaders = document.querySelectorAll(".accordion-header");

  accordionHeaders.forEach((header) => {
    header.addEventListener("click", () => {
      const item = header.parentElement;
      const isOpen = item.classList.contains("active");

      // Optional: close other open items for single-open behavior
      document.querySelectorAll(".accordion-item").forEach((otherItem) => {
        otherItem.classList.remove("active");
        const btn = otherItem.querySelector(".accordion-header");
        if (btn) btn.setAttribute("aria-expanded", "false");
      });

      if (!isOpen) {
        item.classList.add("active");
        header.setAttribute("aria-expanded", "true");
      }
    });
  });
});
