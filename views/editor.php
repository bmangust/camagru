<div class="editor">
    <div autoplay="true" id="imgViewer" class="droppable">
        <video autoplay="true" id="video">
            <img class="base" src="./assets/bg.jpg"/>
        </video>
        <canvas></canvas>
    </div>
    <div class="controls">
        <form action="api/image.php" method="post" enctype="multipart/form-data" class="controls_form" onsubmit="return sendImages()">
            <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
            <input type="file" name="file" id="file" class="input-file">
            <label for="file" class="button controls_button button_file file_label">
                <svg class="icon" viewBox="0 0 32 32">
                    <path fill="#fff" d="M15 22h-15v8h30v-8h-15zM28 26h-4v-2h4v2zM7 10l8-8 8 8h-5v10h-6v-10z"></path>
                </svg>
                <span class="file_name">Upload file</span>
            </label>
            
            <input type="button" class="button controls_button" onclick="captureImage()" id="capture" value="Capture"/>
            <input type="button" class="button controls_button" onclick="clearEdit()" value="Reset"/>
            <input type="submit" class="button controls_button" value="Send"/>
        </form>
        <?php include 'snippets.php'; ?>
    </div>
</div>