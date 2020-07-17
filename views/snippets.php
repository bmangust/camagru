<div class="snippets">
<?php
$numberOfSnippets = 10;
$folder = 'assets/png/';
for ($i = 0; $i < $numberOfSnippets; $i++) {
    $line = selectSnippet(['id'=>$i]);
    if (is_array($line) && isset($line['name'])) {
        $path = join('_', array($i, $line['name']));
        echo "<div class='imgWrapper'><img src='{$folder}{$path}.png'/></div>".PHP_EOL;
    }
}
?>
</div>