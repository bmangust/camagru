<?php
$avatar = $user['avatar'] ? $user['avatar'].'.png' : 'default.png';
$avatar_path = join(DIRECTORY_SEPARATOR, ['assets', 'avatars', $avatar]);
$user_info = $user['info'] ?? 'No info';
?>

<div class="profile">
  <div class="profile__aside">
    <div class="profile__avatar">
      <img id="avatar" src="<?=$avatar_path?>" alt="<?=$_SESSION['user']?> avatar"/>
    </div>
    <div class="profile__info"><p><?=$user_info?></p></div>
    <!-- <button class="button" onclick="messageUser()">DM user</button> -->
  </div>
  <div class="profile__gallery">
    <div class="gallery" id="gallery"></div>
  </div>
</div>