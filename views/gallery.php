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

$offset = 0;
$limit = 6;
setcookie('offset', $offset + $limit);
setcookie('limit', $limit);
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
if ($_GET['route'] === 'gallery') {
    // $gallery_header = '';
    $buttons = '<button id="more" class="button" onclick="moreImages()">More</button>';
} else {
    $buttons = <<<EOL
<button id="prev" class="gallery_button" onclick="prevPage()" disabled>
    <svg viewBox="0 0 451.846 451.847" >
    <g><path d="M97.141,225.92c0-8.095,3.091-16.192,9.259-22.366L300.689,9.27c12.359-12.359,32.397-12.359,44.751,0   c12.354,12.354,12.354,32.388,0,44.748L173.525,225.92l171.903,171.909c12.354,12.354,12.354,32.391,0,44.744   c-12.354,12.365-32.386,12.365-44.745,0l-194.29-194.281C100.226,242.115,97.141,234.018,97.141,225.92z" fill="rgb(255,255,255)"/></g>
    </svg>
</button>
<button id="next" class="gallery_button" onclick="nextPage()">
    <svg viewBox="0 0 451.846 451.847" >
    <g><path d="M345.441,248.292L151.154,442.573c-12.359,12.365-32.397,12.365-44.75,0c-12.354-12.354-12.354-32.391,0-44.744   L278.318,225.92L106.409,54.017c-12.354-12.359-12.354-32.394,0-44.748c12.354-12.359,32.391-12.359,44.75,0l194.287,194.284   c6.177,6.18,9.262,14.271,9.262,22.366C354.708,234.018,351.617,242.115,345.441,248.292z" fill="rgb(255,255,255)"/></g>
    </svg>
</button>
EOL;
    $gallery_header = '<h3>Gallery preview</h3>';
}
?>
<?=@$gallery_header?>
<div class="gallery" id="gallery"><?=$imgs?></div>
<?=$buttons?>