(function () {
  const steps = document.querySelectorAll(".ecolepedia-step");
  const nextButtons = document.querySelectorAll("[data-ecolepedia-next]");
  const prevButtons = document.querySelectorAll("[data-ecolepedia-prev]");

  let current = 0;

  function showStep(index) {
    steps.forEach((step, idx) => {
      step.style.display = idx === index ? "block" : "none";
    });
  }

  nextButtons.forEach((button) => {
    button.addEventListener("click", () => {
      if (current < steps.length - 1) {
        current += 1;
        showStep(current);
      }
    });
  });

  prevButtons.forEach((button) => {
    button.addEventListener("click", () => {
      if (current > 0) {
        current -= 1;
        showStep(current);
      }
    });
  });

  if (steps.length) {
    showStep(current);
  }
})();
