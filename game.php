<?php
require 'include.php';

$statement = $pdo->query('SELECT * FROM `player` ORDER BY `player`.`rating` DeSC');
$statement->setFetchMode(PDO::FETCH_CLASS, 'Player');
$players = $statement->fetchAll();
?>

<html>
<head>
    <title>Bode Kicker Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id='root'>
        <div class='box header'>
            <div class='title'>
                Kicker Elo
            </div>
            <div class='config'>
                <div class='new_game'>
                    Entering game...
                </div>
            </div>
        </div>
        <form action="/index.php" class='box add_game'>
            <div class='add_game_label'>
                <label for="field_winner">Winner:</label>
            </div>
            <div class='add_game_list'>
                <input autofocus type="text" required="required" value="" id="field_winner" name="winner" class="field" list="players"
                    pattern="^(<?php
                        $first = true;
                        foreach ($players as $player) {
                            if ($first) {
                                echo preg_quote($player->name);
                                $first = false;
                            }else {
                                echo "|".preg_quote($player->name);
                            }
                        }
                    ?>)$">
            </div>
            <div class='add_game_label'>
                <label for="field_loser">Loser:</label>
            </div>
            <div class='add_game_list'>
                <input type="text" required="required" value="" id="field_winner" name="loser" class="field" list="players"
                    pattern="^(<?php
                        $first = true;
                        foreach ($players as $player) {
                            if ($first) {
                                echo preg_quote($player->name);
                                $first = false;
                            }else {
                                echo "|".preg_quote($player->name);
                            }
                        }
                    ?>)$">
                <datalist id="players">
                    <?php
                        foreach ($players as $player) {
                            echo "<option value='$player->name'>";
                        }
                    ?>
                </datalist>
            </div>
            <div class='add_game_ack'>
                <input type="submit" value="Go!" class="submit">
            </div>
        </form>
    </div>
</body>
</html>