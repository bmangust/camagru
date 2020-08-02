<div class="editor">
    <div id="imgViewer" class="droppable">
        <img class="base" src="./assets/bg.jpg"/>
    </div>
    <div class="controls">
        <form action="api/image.php" method="post" enctype="multipart/form-data" class="controls_form">
            <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
            <input type="file" name="file" id="file" class="input-file">
            <label for="file" class="controls_button button_file file_label">
                <svg class="icon" viewBox="0 0 32 32">
                    <path fill="#fff" d="M15 22h-15v8h30v-8h-15zM28 26h-4v-2h4v2zM7 10l8-8 8 8h-5v10h-6v-10z"></path>
                </svg>
                <span class="file_name">Upload file</span>
            </label>
            
            <input type="button" class="controls_button" onclick="captureImage()" value="Capture"/>
            <input type="button" class="controls_button" onclick="clearEdit()" value="Reset"/>
            <input type="submit" class="controls_button" onclick="sendImages()" value="Send"/>
        </form>
        <?php include 'snippets.php'; ?>
    </div>
</div>