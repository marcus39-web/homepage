(() => {
  const dialog = document.querySelector('#feedback-dialog');
  const openButton = document.querySelector('[data-open-feedback]');
  const closeButton = document.querySelector('[data-close-feedback]');

  if (!(dialog instanceof HTMLDialogElement)) return;

  openButton?.addEventListener('click', () => dialog.showModal());
  closeButton?.addEventListener('click', () => dialog.close());
  dialog.addEventListener('click', event => {
    if (event.target === dialog) dialog.close();
  });

  if (dialog.dataset.openOnLoad === 'true') dialog.showModal();
})();