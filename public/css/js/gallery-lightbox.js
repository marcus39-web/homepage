const lightbox = document.querySelector('.photo-lightbox');
const lightboxImage = lightbox?.querySelector('.photo-lightbox-image');
const closeButton = lightbox?.querySelector('.photo-lightbox-close');

if (lightbox instanceof HTMLDialogElement && lightboxImage instanceof HTMLImageElement && closeButton instanceof HTMLButtonElement) {
  document.querySelectorAll('.photo-open').forEach((button) => {
    button.addEventListener('click', () => {
      const imageUrl = button.dataset.fullImage;
      if (!imageUrl) {
        return;
      }

      lightboxImage.src = imageUrl;
      lightboxImage.alt = button.dataset.imageAlt ?? '';
      lightbox.showModal();
    });
  });

  closeButton.addEventListener('click', () => lightbox.close());
  lightbox.addEventListener('click', (event) => {
    if (event.target === lightbox) {
      lightbox.close();
    }
  });
}
