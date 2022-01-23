<?php
require 'include.php';

$players = get_players($pdo);

function regex_print_players(array $players)
{
    $first = true;
    foreach ($players as $player) {
        $name = str_replace("\-", "-", preg_quote($player->name));
        if ($first) {
            echo $name;
            $first = false;
        } else {
            echo "|" . $name;
        }
    }
}
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
                <input autofocus type="text" required="required" value="" id="field_winner" name="winner" class="field" list="players" pattern="^(<?php regex_print_players($players); ?>)$">
            </div>
            <div class='add_game_label'>
                <label for="field_loser">Loser:</label>
            </div>
            <div class='add_game_list'>
                <input type="text" required="required" value="" id="field_winner" name="loser" class="field" list="players" pattern="^(<?php regex_print_players($players); ?>)$">
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