const toggleButton = document.getElementById("sidebarToggle");
const sidebar = document.getElementById("sidebar");
const toggles = document.querySelectorAll('[data-bs-toggle="collapse"]');

toggleButton.addEventListener("click", () => {
  sidebar.classList.toggle("show");
});

toggles.forEach((toggle) => {
  const targetId = toggle.getAttribute("href");
  const targetCollapse = document.querySelector(targetId);
  const icon = toggle.querySelector(".icon-collapse");

  if (targetCollapse && icon) {
    targetCollapse.addEventListener("show.bs.collapse", () => {
      icon.classList.add("rotate");
    });

    targetCollapse.addEventListener("hide.bs.collapse", () => {
      icon.classList.remove("rotate");
    });
  }
});
