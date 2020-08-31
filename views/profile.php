<?php
$user_profile = DBOselectUser($_GET['user']);
$avatar = $user_profile['avatar'] ? $user_profile['avatar'].'.png' : 'default.png';
$avatar_path = join(DIRECTORY_SEPARATOR, ['assets', 'avatars', $avatar]);
$user_info = $user_profile['info'] ?? 'No info';
?>

<div class="profile">
  <div class="profile__aside">
    <div class="profile__avatar">
      <img id="avatar" src="<?=$avatar_path?>" alt="<?=$_SESSION['user']?> avatar"/>
    </div>
    <div class="profile__info"><p><?=$user_info?></p></div>
  </div>
  <div class="profile__gallery">
    <div class="gallery" id="gallery"></div>
  </div>
</div>