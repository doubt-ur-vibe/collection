<?php
    require_once 'function.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playing Card Collection</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>My Playing Cards</h1>
</header>
<main>
    <div class="container grid">
        <?php
        $cardSets = getAllCards($db);
        displayAllCards($cardSets);
        ?>
    </div>
</main>
</body>
</html>