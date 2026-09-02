"use strict";

// Run the script after the HTML page has loaded.
document.addEventListener("DOMContentLoaded", () => {
  // Check whether the user prefers reduced motion.
  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

  // Show an accessible message in the bottom-right corner.
  const showMessage = (message) => {
    let status = document.querySelector("#page-status");

    if (!status) {
      status = document.createElement("div");
      status.id = "page-status";
      status.setAttribute("role", "status");
      status.setAttribute("aria-live", "polite");
      status.style.position = "fixed";
      status.style.right = "16px";
      status.style.bottom = "16px";
      status.style.zIndex = "1000";
      status.style.maxWidth = "360px";
      status.style.padding = "12px 16px";
      status.style.borderRadius = "4px";
      status.style.background = "#08263D";
      status.style.color = "#FFFFFF";
      status.style.boxShadow = "0 4px 12px rgba(0, 0, 0, 0.2)";
      document.body.append(status);
    }

    status.textContent = message;
    window.clearTimeout(showMessage.timeoutId);
    showMessage.timeoutId = window.setTimeout(() => {
      status.remove();
    }, 3500);
  };

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

  // Add actions to the template buttons.
  document.querySelectorAll("button").forEach((button) => {
    const label = button.textContent.trim().toLowerCase();

    if (label === "explore services") {
      button.addEventListener("click", () => scrollToSection(".most-used-section"));
    } else if (label === "announcements") {
      button.addEventListener("click", () => scrollToSection(".stay-informed-section"));
    } else if (label === "login / sign up") {
      button.addEventListener("click", () => {
        window.location.href = "./Template_General_Login.html";
      });
    } else if (label === "logout") {
      button.addEventListener("click", () => {
        window.location.href = "./Template_General.html";
      });
    } else if (label === "account") {
      button.addEventListener("click", () => {
        showMessage("The account page will be connected during development.");
      });
    } else if (label === "read more") {
      button.addEventListener("click", () => {
        const cardTitle = button.closest(".announcement-card")
          ?.querySelector("h3")
          ?.textContent.trim();
        showMessage(`${cardTitle || "This announcement"} will open on the completed website.`);
      });
    }
  });

  // Find the sample form on the form template page.
  const formContainer = document.querySelector(".form-table-container");

  if (formContainer) {
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

      showMessage("Form validated successfully. Server submission will be added later.");
    });

    // Remove the invalid state when the user starts typing.
    inputs.forEach((input) => {
      input.addEventListener("input", () => input.removeAttribute("aria-invalid"));
    });
  }

  // Prevent unfinished placeholder links from jumping to the page top.
  document.querySelectorAll('a[href="#"]').forEach((link) => {
    link.addEventListener("click", (event) => {
      event.preventDefault();
      showMessage(`${link.textContent.trim() || "This link"} will be connected during development.`);
    });
  });
});
