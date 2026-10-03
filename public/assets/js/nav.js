document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.querySelector(".nav-toggle");
  const links = document.querySelector(".site-nav-links");

  if (!toggle || !links) return;

  toggle.addEventListener("click", () => {
    const expanded = toggle.getAttribute("aria-expanded") === "true";
    toggle.setAttribute("aria-expanded", expanded ? "false" : "true");
    links.classList.toggle("is-open");
  });
});
