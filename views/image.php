<?php
    $img = DBOgetImageInfo($_GET['id']);
    $numberOfLikes = DBOgetNumberOfLikes($_GET['id']);
    $likes = DBOselectLikes($_GET['id'], $_SESSION['user']);
    $classes = $likes ? "lightbox_like liked" : "lightbox_like";
    $comments = '';
    foreach ($img as $index => $comment) {
        $comments .= <<<EOL
<div class="lightbox_comment">
    <div class="lightbox_comment__author">
        <a class="lightbox_comment__author_profile" href="http://localhost/camagru/index.php?route=profile&user={$comment['author']}">{$comment['author']}</a>
    </div>
    <div class="comment_body">
        <p>{$comment['message']}</p>
    </div>
</div>
EOL;
    }
?>
<div class="fullImage">
  <div class="lightbox_wrapper" id="lightbox_wrapper">
    <div class="lightbox_main">
      <img id="<?=$img[0]['id']?>" src="<?="assets/uploads/{$img[0]['imgSrc']}"?>"/>
    </div>
    <div class="lightbox_aside">
      <div class="lightbox_info">
        <div class="lightbox_info__author">
          <span id="author_name"><?=$img[0]['imgAuthor']?></span>
        </div>
        <div class="lightbox_info__likes">
          <div class="<?=$classes?>"></div>
          <span><?=$numberOfLikes?></span>
        </div>
      </div>
      <div class="lightbox_comments" id="lightbox_comments"><?=$comments?></div>
      <div class="lightbox_comments__controls">
        <textarea name="comment" class="lightbox_comments__input" id="comments_input" placeholder="Your comment"/></textarea>
        <button class="button lightbox_comments__submit" onclick="addComment(<?=$img[0]['id']?>)">Send</button>
      </div>
    </div>
  </div>
</div>