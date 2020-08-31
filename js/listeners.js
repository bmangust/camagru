window.onload = () => {
  sendRestoreEmail();
  addSnippetClickListener();
  if (window.location.href.includes("route=create")) {
    initWebcam();
    initControls();
    addUploadListener();
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
  } else if (window.location.href.includes("route=settings")) {
    selectNotifications();
    addChangeAvatarListener();
  }
};
