document.addEventListener('DOMContentLoaded', () => {
  const widget = document.querySelector('[data-waflow]');
  if (!widget) {
    return;
  }

  const trigger = widget.querySelector('.waflow-trigger');
  const panel = widget.querySelector('.waflow-panel');

  if (!trigger || !panel) {
    return;
  }

  trigger.addEventListener('click', () => {
    const isHidden = panel.hasAttribute('hidden');
    if (isHidden) {
      panel.removeAttribute('hidden');
      widget.classList.add('waflow-open');
    } else {
      panel.setAttribute('hidden', 'hidden');
      widget.classList.remove('waflow-open');
    }
  });
});
