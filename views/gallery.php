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
unset($_COOKIE['offset']);

$offset = 0;
if ($_GET['route'] === 'create') {
    $limit = 10;
} else {
    $limit = $_COOKIE['offset'] ?? 2;
    setcookie('offset', $offset + $limit);
    setcookie('limit', $limit);
}
$params = ['offset'=>$offset, 'limit'=>$limit];
$uploads = DBOselectAllUploads($_SESSION['user'], $params);

// <img class="avatar" src="{$row['avatar']}" />
$imgs = "";
foreach ($uploads as $item => $row) {
    $url = explode('.', $row['name'])[0];
    $id = $row['id'];
    $classes = 'like';
    if ($row['imgid'] !== NULL) {
        $classes .= ' liked';
    }
    $img = <<<EOL
<div class="imgWrapper">
    <a href="index.php?img={$url}&id={$id}">
        <img src="assets/uploads/{$row['name']}"/>
        <div class="info">
            <div class="author">
                <span class="author-name">{$row['user']}</span>
            </div>
            <div class="{$classes}"></div>
        </div>
    </a>
</div>
EOL;
    $imgs .= $img;
}
?>
<div class="gallery" id="gallery">
    <?=$imgs?>
</div>
<?php
// if in editor - show pagination buttons
// else if in gallery - show More button
if ($_GET['route'] === 'gallery'): ?>
<button id="more" class="button" onclick="moreImages()">More</button>
<?php endif; ?>