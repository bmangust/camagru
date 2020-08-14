window.onload = () => {
  sendRestoreEmail();
  addUploadListener();
  addSnippetClickListener();
  const gallery = $`#gallery`;
  if (gallery) {
    loadImages();
  }
};
