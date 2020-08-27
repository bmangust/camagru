<?php
if (!isset($_SESSION['is_auth']) || !isset($_SESSION['user']) || $_SESSION['is_auth'] != true) {
    $_SESSION['msg'][] = "Authorized persons only. Please, log in";
    $_SESSION['class'] = "error";
    header("Location: ./index.php?route=menu");
} else {?>
<div class="createView">
    <?php include 'editor.php'; ?>
    <aside class="sidebar">
        <div class="sidebar__switcher gallerySwither">Show gallery</div>
        <div class="sidebar__gallery noDisplay">
            <div class="sidebar__closer gallerySwither">
                <svg viewBox="0 0 512 512">
                    <path d="M284.286,256.002L506.143,34.144c7.811-7.811,7.811-20.475,0-28.285c-7.811-7.81-20.475-7.811-28.285,0L256,227.717    L34.143,5.859c-7.811-7.811-20.475-7.811-28.285,0c-7.81,7.811-7.811,20.475,0,28.285l221.857,221.857L5.858,477.859    c-7.811,7.811-7.811,20.475,0,28.285c3.905,3.905,9.024,5.857,14.143,5.857c5.119,0,10.237-1.952,14.143-5.857L256,284.287    l221.857,221.857c3.905,3.905,9.024,5.857,14.143,5.857s10.237-1.952,14.143-5.857c7.811-7.811,7.811-20.475,0-28.285    L284.286,256.002z" fill="rgb(255,255,255)"/>
                </svg>
            </div>
            <?php include 'gallery.php'; ?>
        </div>
        <div class="controls">
            <div class="controls__element">
                <label for="rotation">Rotation</label>
                <span class="controls__value" id="rotationValue">0</span>
                <input type="range" id="rotation" name="rotation" min="0" max="360" value="0"/>
            </div>
            
            <div class="controls__element">
                <label for="scale">Scale</label>
                <span class="controls__value" id="scaleValue">0</span>
                <input type="range" id="scale" name="scale" min="0" max="300" value="100"/>
            </div>
            
            <div class="controls__element">
                <label for="opacity">Opacity</label>
                <span class="controls__value" id="opacityValue">0</span>
                <input type="range" id="opacity" name="opacity" min="0" max="100" value="100"/>
            </div>
        </div>
    </aside>

</div>
<?php }