(() => {
  const dialog = document.querySelector('#feedback-dialog');
  const openButtons = [...document.querySelectorAll('[data-open-feedback]')];
  const closeButton = document.querySelector('[data-close-feedback]');

  if (!(dialog instanceof HTMLDialogElement)) return;

  openButtons.forEach(button => button.addEventListener('click', () => dialog.showModal()));
  closeButton?.addEventListener('click', () => dialog.close());
  dialog.addEventListener('click', event => {
    if (event.target === dialog) dialog.close();
  });

  if (dialog.dataset.openOnLoad === 'true') dialog.showModal();
})();