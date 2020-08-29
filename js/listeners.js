window.onload = () => {
  sendRestoreEmail();
  addUploadListener();
  addSnippetClickListener();
  if (window.location.href.includes("route=create")) {
    initWebcam();
    initControls();
    getGallerySize().then((val) => (gallerySize = val));
    const gallerySwither = $$(".gallerySwither");
    gallerySwither.forEach((el) =>
      el.addEventListener("click", () => toggleGallery())
    );
  } else if (window.location.href.includes("route=gallery")) {
    getGallerySize().then((val) => (gallerySize = val));
    loadImages();
  } else if (window.location.href.includes("route=profile")) {
    loadImages();
  }
};
