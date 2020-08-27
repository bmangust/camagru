window.onload = () => {
  sendRestoreEmail();
  addUploadListener();
  addSnippetClickListener();
  if (window.location.href.includes("route=create")) {
    initWebcam();
    const gallerySwither = $$(".gallerySwither");
    gallerySwither.forEach((el) =>
      el.addEventListener("click", () => toggleGallery())
    );
  } else if (window.location.href.includes("route=gallery")) {
    loadImages();
  }
};
