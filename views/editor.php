<div class="editor">
    <div autoplay="true" id="imgViewer" class="droppable">
        <video autoplay="true" id="video">
            <img class="base" src="./assets/bg.jpg"/>
        </video>
        <canvas></canvas>
    </div>
    <?php include 'snippets.php'; ?>
</div>
<aside class="sidebar">
    <div class="controls">
        <!-- <div class="controls__element">
            <label for="rotation">Rotation</label>
            <span class="controls__value" id="rotationValue">0</span>
            <input type="range" id="rotation" name="rotation" min="0" max="360" value="0"/>
        </div>
        
        <div class="controls__element">
            <label for="scale">Scale</label>
            <span class="controls__value" id="scaleValue">1</span>
            <input type="range" id="scale" name="scale" min="0" max="300" value="100"/>
        </div> -->
        
        <div class="controls__element">
            <label for="opacity">Opacity</label>
            <span class="controls__value" id="opacityValue">1</span>
            <input type="range" id="opacity" name="opacity" min="0" max="100" value="100"/>
        </div>
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
    </div>
</aside>