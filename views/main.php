<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="./css/style.css" rel="stylesheet">
    <script src="./js/common.js"></script>
    <script src="./js/formValdation.js"></script>
    <script src="./js/buttons.js"></script>
    <script src="./js/gallery.js"></script>
    <script src="./js/listeners.js"></script>
    <title>Camagru</title>
    </head>
    <body>
    <?php include $header;?>
    <main>
        <?php if ($error) include $error;?>
        <?php include $main;?>
    </main>
    <?php include $footer;?>
    </body>
</html>