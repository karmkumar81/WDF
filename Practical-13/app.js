/**
 * Practical 13: Fetch API / AJAX Operations (Single Page Asynchronous CRUD)
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

document.addEventListener("DOMContentLoaded", () => {
  const API_URL = "api.php";
  const tbody = document.getElementById("student-tbody");
  const liveSearch = document.getElementById("live-search");
  const recordCount = document.getElementById("record-count");
  const toast = document.getElementById("toast");
  const modal = document.getElementById("student-modal");
  const modalTitle = document.getElementById("modal-title");
  const studentForm = document.getElementById("student-form");

  const formId = document.getElementById("form-id");
  const formName = document.getElementById("form-name");
  const formEnrollment = document.getElementById("form-enrollment");
  const formEmail = document.getElementById("form-email");
  const formMobile = document.getElementById("form-mobile");
  const formDept = document.getElementById("form-dept");

  let searchTimeout = null;

  /**
   * Display Toast Notification
   */
  function showToast(message, isError = false) {
    toast.textContent = message;
    toast.className = `toast ${isError ? "toast-error" : "toast-success"}`;
    toast.style.display = "block";
    setTimeout(() => {
      toast.style.display = "none";
    }, 4000);
  }

  /**
   * Fetch Students from API with optional search parameter
   */
  async function loadStudents(query = "") {
    try {
      const url = query ? `${API_URL}?search=${encodeURIComponent(query)}` : API_URL;
      const res = await fetch(url);
      const json = await res.json();

      if (!json.success) throw new Error(json.message || "Failed to load records");

      renderTable(json.data);
      recordCount.textContent = `Showing ${json.data.length} records`;
    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; color: var(--error); padding: 1.5rem;">Error: ${err.message}</td></tr>`;
      showToast(err.message, true);
    }
  }

  /**
   * Render student rows
   */
  function renderTable(students) {
    if (students.length === 0) {
      tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; color: var(--text-muted); padding: 2rem;">No students found matching query.</td></tr>`;
      return;
    }

    tbody.innerHTML = students.map(s => `
      <tr>
        <td>${s.id}</td>
        <td><strong>${escapeHtml(s.enrollment_no)}</strong></td>
        <td>${escapeHtml(s.name)}</td>
        <td>${escapeHtml(s.email)}</td>
        <td>${escapeHtml(s.mobile)}</td>
        <td>${escapeHtml(s.department)}</td>
        <td>${s.gpa || "8.50"}</td>
        <td><span style="color: ${s.status === 'active' ? '#16a34a' : '#dc2626'}; font-weight: bold;">${escapeHtml(s.status)}</span></td>
        <td>
          <button class="btn-sm btn-edit" data-id="${s.id}">Edit</button>
          <button class="btn-sm btn-delete" data-id="${s.id}" data-name="${escapeHtml(s.name)}">Delete</button>
        </td>
      </tr>
    `).join("");

    // Attach row action listeners
    tbody.querySelectorAll(".btn-edit").forEach(btn => {
      btn.addEventListener("click", () => openEditModal(btn.dataset.id));
    });

    tbody.querySelectorAll(".btn-delete").forEach(btn => {
      btn.addEventListener("click", () => deleteStudent(btn.dataset.id, btn.dataset.name));
    });
  }

  /**
   * Open Modal in Create Mode
   */
  document.getElementById("btn-open-create").addEventListener("click", () => {
    studentForm.reset();
    formId.value = "";
    formEnrollment.removeAttribute("readonly");
    formEnrollment.style.background = "white";
    modalTitle.textContent = "Add Student via Fetch API";
    modal.style.display = "flex";
  });

  /**
   * Open Modal in Edit Mode
   */
  async function openEditModal(id) {
    try {
      const res = await fetch(`${API_URL}?id=${id}`);
      const json = await res.json();
      if (!json.success) throw new Error(json.message);

      const s = json.data;
      formId.value = s.id;
      formName.value = s.name;
      formEnrollment.value = s.enrollment_no;
      formEnrollment.setAttribute("readonly", "true");
      formEnrollment.style.background = "#f1f5f9";
      formEmail.value = s.email;
      formMobile.value = s.mobile;
      formDept.value = s.department;

      modalTitle.textContent = `Edit Student: ${s.name} (${s.enrollment_no})`;
      modal.style.display = "flex";
    } catch (err) {
      showToast(err.message, true);
    }
  }

  // Close modal
  document.getElementById("btn-cancel").addEventListener("click", () => {
    modal.style.display = "none";
  });

  /**
   * Handle Create / Edit Submission via fetch()
   */
  studentForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const id = formId.value;
    const isEdit = Boolean(id);

    const payload = {
      name: formName.value.trim(),
      enrollment_no: formEnrollment.value.trim(),
      email: formEmail.value.trim(),
      mobile: formMobile.value.trim(),
      department: formDept.value
    };

    if (isEdit) payload.id = parseInt(id, 10);

    try {
      const res = await fetch(API_URL, {
        method: isEdit ? "PUT" : "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
      const json = await res.json();

      if (!json.success) {
        throw new Error(json.message || "Operation failed.");
      }

      modal.style.display = "none";
      showToast(isEdit ? "Student updated successfully!" : "Student created successfully!");
      loadStudents(liveSearch.value);
    } catch (err) {
      showToast(err.message, true);
    }
  });

  /**
   * Asynchronous Delete with fetch()
   */
  async function deleteStudent(id, name) {
    if (!confirm(`Are you sure you want to delete student "${name}"?`)) return;

    try {
      const res = await fetch(`${API_URL}?id=${id}`, {
        method: "DELETE"
      });
      const json = await res.json();

      if (!json.success) throw new Error(json.message || "Failed to delete student.");

      showToast(`Student "${name}" deleted.`);
      loadStudents(liveSearch.value);
    } catch (err) {
      showToast(err.message, true);
    }
  }

  /**
   * Debounced Live Search (Intermediate Extension)
   */
  liveSearch.addEventListener("input", () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      loadStudents(liveSearch.value);
    }, 300); // 300ms debounce
  });

  function escapeHtml(str) {
    if (!str) return "";
    return String(str).replace(/[&<>"']/g, m => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
    })[m]);
  }

  // Initial load
  loadStudents();
});
