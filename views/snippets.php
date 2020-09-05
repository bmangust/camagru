<div class="snippets">
<?php
$numberOfSnippets = 8;
$folder = 'assets/png/';
for ($i = 1; $i <= $numberOfSnippets; $i++) {
    $line = DBOselectSnippet(['id'=>$i]);
    if (is_array($line) && isset($line['name'])) {
        $path = join('_', array($i, $line['name']));
        echo "<div class='imgWrapper snippetWrapper'><img class='snippet' src='{$folder}{$path}.png' draggable='false'/></div>".PHP_EOL;
    }
}
?>
</div>