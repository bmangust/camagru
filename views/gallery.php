<?php
    $uploads = DBOselectUploads();
    $imgs = "";
    foreach ($uploads as $item => $row) {
        $imgs .= "<div class=\"imgWrapper\"><img src=\"assets/uploads/{$row['name']}\"></div>";
    }
    ?>
<div class="gallery">
    <?=$imgs?>
</div>