window.onload = () => {
  sendRestoreEmail();
  addUploadListener();
  addSnippetClickListener();
  if (window.location.href.includes("route=create")) {
    initWebcam();
  }
  const gallery = $`#gallery`;
  if (gallery) {
    loadImages();
  }
};
