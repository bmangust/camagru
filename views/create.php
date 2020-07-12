<?php
if (isset($_SESSION['is_auth']) && isset($_SESSION['user']) && $_SESSION['is_auth'] == true) { ?>
<div class="createView">
    <div class="editor">
    <div id="imgViewer">
        <img src="https://image.freepik.com/free-vector/abstract-halftone-background_23-2148583453.jpg"/>
    </div>
    <div class="controls">
        <button class="controls_button" onclick="loadImage()">Load</button>
        <button class="controls_button" onclick="captureImage()">
        Capture
        </button>
        <button class="controls_button" onclick="clearEdit()">Reset</button>
    </div>
    <div class="snippets">
        <!-- PNG images for edit -->
        <div class="imgWrapper"></div>
        <div class="imgWrapper"></div>
        <div class="imgWrapper"></div>
        <div class="imgWrapper"></div>
        <div class="imgWrapper"></div>
        <div class="imgWrapper"></div>
        <div class="imgWrapper"></div>
        <div class="imgWrapper"></div>
    </div>
    </div>
    <aside class="preview">
        <div class="gallery">
            <!-- pagination gallery here -->
            <div class="imgWrapper"></div>
            <div class="imgWrapper"></div>
            <div class="imgWrapper"></div>
            <div class="imgWrapper"></div>
            <div class="imgWrapper"></div>
            <div class="imgWrapper"></div>
            <div class="imgWrapper"></div>
            <div class="imgWrapper"></div>
        </div>
    </aside>
</div>
<?php } else {
    $_SERVER['msg'] = "Authorized persons only";
    $_SERVER['class'] = "error";
    include "./views/error.php";
}