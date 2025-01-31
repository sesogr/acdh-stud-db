const showhidetoggle = (checkbox) =>
  document
    .querySelectorAll("tr." + checkbox.dataset.dupeId)
    .forEach((el) => el.classList.toggle("hide"));

document.addEventListener("DOMContentLoaded", () =>
  document
    .querySelectorAll('input[type="checkbox"]')
    .forEach((cb) => !cb.checked && showhidetoggle(cb)),
);
