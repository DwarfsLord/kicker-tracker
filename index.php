<?php

require 'include.php';
require 'calculator.php';

$konst_A = 43;
$konst_K = 50;

if (isset($_GET['name'])) {
    $player_added = add_player($_GET['name'], $pdo);
}

if (isset($_GET['winner']) && isset($_GET['loser'])) {
    $winner = get_player_by_name($_GET['winner'], $pdo);
    $loser = get_player_by_name($_GET['loser'], $pdo);

    if ($winner != false && $loser != false) {
        $state = add_game1($winner, $loser, $pdo);

        calculate_elo($winner->elo1, $loser->elo1, true, $elo_gained);

        add_free_elo(true, $winner->elo1, $winner->free_elo1, $winner->elo2, $winner->free_elo2);
        $winner->elo1 += $elo_gained;
        $state &= set_player($winner, $pdo);

        add_free_elo(false, $loser->elo1, $loser->free_elo1, $loser->elo2, $loser->free_elo2);
        $loser->elo1 -= $elo_gained;
        $state &= set_player($loser, $pdo);

        if ($state == false) {
            echo 'PROBLEM!!!!';
        }

        $game_added = $state;
    }
}

if (isset($_GET['winner1']) && isset($_GET['winner2']) && isset($_GET['loser1']) && isset($_GET['loser2'])) {
    $winner1 = get_player_by_name($_GET['winner1'], $pdo);
    $winner2 = get_player_by_name($_GET['winner2'], $pdo);
    $loser1 = get_player_by_name($_GET['loser1'], $pdo);
    $loser2 = get_player_by_name($_GET['loser2'], $pdo);

    if ($winner1 != false && $winner2 != false && $loser1 != false && $loser2 != false) {
        $state = add_game2($winner1, $winner2, $loser1, $loser2, $pdo);

        $winner_elo = join_elo($winner1->elo2, $winner2->elo2);
        $loser_elo = join_elo($loser1->elo2, $loser2->elo2);

        calculate_elo($winner_elo, $loser_elo, true, $elo_gained);

        add_free_elo(true, $winner1->elo2, $winner1->free_elo2, $winner1->elo1, $winner1->free_elo1);
        add_free_elo(true, $winner2->elo2, $winner2->free_elo2, $winner2->elo1, $winner2->free_elo1);
        add_free_elo(false, $loser1->elo2, $loser1->free_elo2, $loser1->elo1, $loser1->free_elo1);
        add_free_elo(false, $loser2->elo2, $loser2->free_elo2, $loser2->elo1, $loser2->free_elo1);

        split_elo($winner1->elo2, $winner2->elo2, $elo_gained);
        split_elo($loser1->elo2, $loser2->elo2, -1*$elo_gained);
        
        $state &= set_player($winner1, $pdo);
        $state &= set_player($winner2, $pdo);
        $state &= set_player($loser1, $pdo);
        $state &= set_player($loser2, $pdo);

        if ($state == false) {
            echo 'PROBLEM!!!!';
        }

        $game_added = $state;
    }
}

$players = get_players($pdo);

?>

<html>

<head>
    <title>Bode Kicker Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <script>
        var stateObj = {
            a: "b"
        };
        history.replaceState(stateObj, "reset_GET", "index.php");

        <?php
        if (isset($player_added) && $player_added) {
            echo "alert('Player added!');";
        }
        if (isset($game_added)) {
            echo "alert('Game added!');";
        }
        ?>
    </script>
</head>

<body>
    <div id='root'>
        <div class='box header'>
            <div class='title'>
                Kicker Elo
            </div>
            <div class='new_player'>
                <a href='player.php'>Add player</a>
            </div>
            <div class='config'>
                <div class='new_game single'>
                    <a href='game.php'>Enter 1v1</a>
                </div>
                <div class='new_game double'>
                    <a href='game2.php'>Enter 2v2</a>
                </div>
            </div>
        </div>
        <?php
        $i = 1;
        foreach ($players as $player) {
            $colour = ($player->elo1 > $player->elo2) ? " single" : " double";
            $colour = ($player->elo1 == $player->elo2) ? "" : $colour;
            echo ("
            <div class='box player$colour'>
                <div id='rank'>$i</div>
                <div id='name'>$player->name</div>
                <div id='rating'>" . max($player->elo1, $player->elo2) . "</div>
                <a href='history.php?player_id=$player->player_id' class='player_hist'><span class='link-spanner'></span></a>
            </div>");
            $i++;
        }
        ?>
    </div>
</body>

</html>