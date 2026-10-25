"use strict";

/* Reusable server-side pagination:
 * 1. Give the list container a unique id and data-pagination attribute.
 * 2. Add data-page-link to pagination anchors, with href pointing to a complete page that opens independently.
 * 3. Optional data-pagination-heading / status / summary attributes provide focus and status feedback.
 * PHP validates parameters, applies LIMIT/OFFSET, and converts JSON to HTML; JavaScript only updates the view.
 * No JSON API is required; fetch retrieves the next HTML page and replaces only the list container with the matching ID.
 */
document.addEventListener("DOMContentLoaded", () => {
  if (!window.fetch || !window.AbortController) return; // Keep normal link navigation available in older browsers.
  let pending = null;
  let generation = 0;
  const loadPage = async (url, id, pushHistory) => {
    const region = document.getElementById(id);
    if (!region) return;
    if (pending) pending.abort();
    const request = new AbortController();
    pending = request;
    const requestId = ++generation;
    const status = region.querySelector("[data-pagination-status]");
    region.setAttribute("aria-busy", "true");
    if (status) status.textContent = "Loading…";
    try {
      const response = await fetch(url.href, { signal: request.signal, credentials: "same-origin" });
      if (!response.ok) throw new Error("Page unavailable");
      const html = await response.text();
      if (requestId !== generation) return;
      const parsed = new DOMParser().parseFromString(html, "text/html");
      const replacement = parsed.getElementById(id);
      if (!replacement || !replacement.hasAttribute("data-pagination") || replacement.querySelector('[role="alert"]')) {
        throw new Error("Invalid page response");
      }
      replacement.removeAttribute("aria-busy");
      const nextStatus = replacement.querySelector("[data-pagination-status]");
      const summary = replacement.querySelector("[data-pagination-summary]");
      // The summary already displays the count; reserve status for loading and error messages to avoid duplication.
      if (nextStatus) nextStatus.textContent = summary ? "" : "Page loaded.";
      region.replaceWith(replacement);
      if (pushHistory) history.pushState({ paginationId: id }, "", url.href);
      const heading = replacement.querySelector("[data-pagination-heading]");
      if (heading) heading.focus({ preventScroll: true });
      replacement.scrollIntoView({ block: "start", behavior: "auto" });
    } catch (error) {
      if (error.name === "AbortError") return;
      if (!pushHistory) { window.location.reload(); return; }
      if (status) status.textContent = "Unable to load this page. Please try again.";
    } finally {
      if (requestId === generation) {
        region.removeAttribute("aria-busy");
        pending = null;
      }
    }
  };
  // Event delegation avoids rebinding each button after the list is replaced.
  document.addEventListener("click", event => {
    const link = event.target.closest("a[data-page-link]");
    if (!link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target || link.hasAttribute("download")) return;
    const region = link.closest("[data-pagination]");
    const url = new URL(link.href, window.location.href);
    if (!region || !region.id || url.origin !== window.location.origin) return;
    event.preventDefault();
    if (!history.state || !history.state.paginationId) {
      history.replaceState(Object.assign({}, history.state, { paginationId: region.id }), "", window.location.href);
    }
    loadPage(url, region.id, true);
  });
  window.addEventListener("popstate", event => {
    if (event.state && event.state.paginationId) loadPage(new URL(window.location.href), event.state.paginationId, false);
  });
});

// Run the script after the HTML page has loaded.
document.addEventListener("DOMContentLoaded", () => {
  // Check whether the user prefers reduced motion.
  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

  // Move to a page section with optional smooth scrolling.
  const scrollToSection = (selector) => {
    const section = document.querySelector(selector);

    if (section) {
      section.scrollIntoView({
        behavior: prefersReducedMotion.matches ? "auto" : "smooth",
        block: "start"
      });
    }
  };

  // Home-page buttons scroll to sections; account and detail links use PHP routes.
  document.querySelectorAll(".hero-buttons button").forEach((button) => {
    const label = button.textContent.trim().toLowerCase();

    if (label === "explore services") {
      button.addEventListener("click", () => scrollToSection(".most-used-section"));
    } else if (label === "announcements") {
      button.addEventListener("click", () => scrollToSection(".stay-informed-section"));
    }
  });

});

// Client-side example used only by template/Template_form.php.
// Production forms use their own PHP submission and validation handlers.
document.addEventListener("DOMContentLoaded", () => {
  const formContainer = document.querySelector("form[data-demo-form]");

  if (formContainer) {
    // Keep demonstration messages inside the demo form, away from production pages.
    const showMessage = (message) => {
      let status = formContainer.querySelector("[data-demo-status]");
      if (!status) {
        status = document.createElement("p");
        status.setAttribute("data-demo-status", "");
        status.setAttribute("role", "status");
        status.setAttribute("aria-live", "polite");
        formContainer.append(status);
      }
      status.textContent = message;
    };
    const inputs = [...formContainer.querySelectorAll('input[type="text"]')];

    // Check text fields that are explicitly marked as required.
    formContainer.addEventListener("submit", (event) => {
      event.preventDefault();
      let firstInvalidInput = null;

      inputs.forEach((input) => {
        const isEmpty = input.required && input.value.trim() === "";
        input.setAttribute("aria-invalid", String(isEmpty));

        if (isEmpty && !firstInvalidInput) {
          firstInvalidInput = input;
        }
      });

      if (firstInvalidInput) {
        firstInvalidInput.focus();
        showMessage("Please complete all fields before submitting the form.");
        return;
      }

      showMessage("Demo validation passed. This example does not submit or save data.");
    });

    // Remove the invalid state when the user starts typing.
    inputs.forEach((input) => {
      input.addEventListener("input", () => input.removeAttribute("aria-invalid"));
    });
  }

});

/* Booking calendar: links work without JS; JS reveals the form without a reload. */
document.addEventListener('DOMContentLoaded', function () {
  const root = document.querySelector('[data-booking-calendar]');
  if (!root) return;
  const dataNode = document.getElementById('booking-dates');
  const form = document.getElementById('booking-form');
  if (!dataNode || !form) return;
  const dates = JSON.parse(dataNode.textContent);
  const sessionSelect = form.querySelector('[name="session_id"]');
  const updateSessionSummary = function () {
    form.querySelector('[data-booking-session-summary]').textContent = sessionSelect.value ? sessionSelect.options[sessionSelect.selectedIndex].textContent : '';
  };
  sessionSelect.addEventListener('change', updateSessionSummary);
  root.addEventListener('click', function (event) {
    const link = event.target.closest('[data-booking-date]');
    if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0) return;
    const date = link.getAttribute('data-booking-date');
    if (!dates[date]) return;
    event.preventDefault();
    const select = form.querySelector('[name="session_id"]');
    select.textContent = '';
    select.add(new Option('Choose a time', ''));
    dates[date].forEach(function (slot) { select.add(new Option(slot.label, slot.id)); });
    if (dates[date].length === 1) select.value = String(dates[date][0].id);
    form.querySelector('[name="date"]').value = date;
    form.querySelector('[data-booking-form-date]').textContent = 'Selected date: ' + date + ' (Perth time)';
    updateSessionSummary();
    root.querySelectorAll('[data-booking-date]').forEach(function (day) { day.removeAttribute('aria-current'); });
    link.setAttribute('aria-current', 'date');
    root.querySelector('[data-booking-date-status]').textContent = 'Selected date: ' + date + ' (Perth time)';
    form.hidden = false;
    form.querySelector('h2').focus();
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});

// A phone response requires a contact number; keep native browser validation.
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('rates-enquiry');
  if (!form) return;
  const phone = form.querySelector('[name="phone"]');
  const updatePhoneRequired = function () {
    const method = form.querySelector('[name="response_method"]:checked');
    phone.required = !!method && method.value === 'phone';
  };
  form.querySelectorAll('[name="response_method"]').forEach(function (radio) {
    radio.addEventListener('change', updatePhoneRequired);
  });
  updatePhoneRequired();
});

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('member-feedback');
  if (!form) return;
  const checkbox = form.querySelector('[name="response_requested"]');
  const email = form.querySelector('[name="email"]');
  const updateRequired = function () { email.required = checkbox.checked; };
  checkbox.addEventListener('change', updateRequired);
  updateRequired();
});

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-admin-edit]').forEach(function (row) {
    row.addEventListener('dblclick', function (event) {
      if (event.target.closest('a, button, input, select, textarea')) return;
      window.location.href = row.getAttribute('data-admin-edit');
    });
  });
});
