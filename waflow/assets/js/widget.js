document.addEventListener('DOMContentLoaded', () => {
  const widget = document.querySelector('[data-waflow-widget]');
  if (!widget) {
    return;
  }

  const trigger = widget.querySelector('.waflow-trigger');
  const panel = widget.querySelector('.waflow-panel');

  if (trigger && panel) {
    trigger.addEventListener('click', () => {
      const isHidden = panel.hasAttribute('hidden');
      if (isHidden) {
        panel.removeAttribute('hidden');
        widget.classList.add('waflow-open');
      } else {
        panel.setAttribute('hidden', 'hidden');
        widget.classList.remove('waflow-open');
      }

      logClick();
    });
  }

  const directLinks = widget.querySelectorAll('[data-waflow-direct]');
  directLinks.forEach((link) => {
    link.addEventListener('click', () => {
      logClick();
    });
  });

  function logClick() {
    if (!window.waflowWidget) {
      return;
    }

    const data = new FormData();
    data.append('action', 'waflow_widget_click');
    data.append('nonce', window.waflowWidget.nonce);
    data.append('page_title', document.title || '');
    data.append('page_url', window.location.href || '');

    fetch(window.waflowWidget.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: data,
    }).catch(() => {});
  }
});
