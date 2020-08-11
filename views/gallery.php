<?php
/**
 * подядок загрузки галереи
 * 1. клиент отправляет запрос серверу о запросе порции галереи
 * 2. сервер получает из БД пачку записей (пока ограничим количество 10 шт за раз)
 * 3. для каждой картинки нужно получить статус лайка для текущего пользователя
 * 4. отправить клиенту json типа 
 * { [
 *      { imgid: 'id',
 *        imgname: 'name.jpg',
 *        like: true/false,
 *        author: { 
 *          name:'author name',
 *          avatar: 'path/to/avatar.jpg',
 *          url: 'hash(useremail)' }
 *       },
 *       {...}
 * ] }
 * 5. клиент разбирает json и для каждого элемента массива 
 * или все это делать на сервере и формировать готовый html
 */

$params = ['offset'=>0, 'limit'=>6];
$uploads = DBOselectUploads($params);
$likes = DBOselectLikes($uploads, $_SESSION['user']);

// <img class="avatar" src="{$row['avatar']}" />
$imgs = "";
foreach ($uploads as $item => $row) {
    $url = explode('.', $row['name'])[0];
    $id = $row['id'];
    $classes = 'like';
    if (in_array($id, $likes)) {
        $classes .= ' liked';
    }
    $img = <<<EOL
<div class="imgWrapper">
    <a href="index.php?img={$url}&id={$id}"><img src="assets/uploads/{$row['name']}">
    <div class="info">
        <div class="author">
            <span class="author-name">${row['user']}</span>
        </div>
            
        <div class="{$classes}"></div>
    </div>
    </a>
</div>
EOL;
    $imgs .= $img;
}
?>
<div class="gallery">
    <?=$imgs?>
</div>