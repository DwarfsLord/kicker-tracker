<?php

require 'include.php';

$konst_A = 91;
$konst_K = 67/2; //changed from /1 on the 15th at 00:00

if (isset($_GET['name'])) {
    $statement = $pdo->prepare("INSERT INTO `player` (`player_id`, `name`, `rating`) VALUES (NULL, :new_name, '1000')");
    if($statement->execute([':new_name' => $_GET['name']])){
        $player_added = true;
    }
}

if (isset($_GET['winner']) && isset($_GET['loser'])) {
    $statement = $pdo->prepare("SELECT *  FROM `player` WHERE `name` LIKE :winner ORDER BY `rating` DESC");
    $statement->execute([':winner' => $_GET['winner']]);
    $winner = $statement->fetchObject('Player');

    $statement = $pdo->prepare("SELECT *  FROM `player` WHERE `name` LIKE :loser ORDER BY `rating` DESC");
    $statement->execute([':loser' => $_GET['loser']]);
    $loser = $statement->fetchObject('Player');

    if ($winner != null && $loser != null) {
        if ($winner->rating > $loser->rating) {
            $win_percent = 1-(1/(exp(abs($winner->rating-$loser->rating)/$konst_A)+1));
        } else {
            $win_percent = (1/(exp(abs($winner->rating-$loser->rating)/$konst_A)+1));
        }

        $winner_new_rating = $winner->rating + ceil($konst_K * (1-$win_percent));
        $loser_new_rating = $loser->rating - ceil($konst_K * (1-$win_percent));

        $statement = $pdo->prepare("UPDATE `player` SET `rating` = '$winner_new_rating' WHERE `player`.`player_id` = $winner->player_id");
        $statement->execute();
    
        $statement = $pdo->prepare("UPDATE `player` SET `rating` = '$loser_new_rating' WHERE `player`.`player_id` = $loser->player_id");
        $statement->execute();
        
        $statement = $pdo->prepare("INSERT INTO `game` (`game_id`, `winner_id`, `winner_old_elo`, `loser_id`, `loser_old_elo`) VALUES (NULL, '$winner->player_id', '$winner->rating', '$loser->player_id', '$loser->rating')");
        $statement->execute();

        $game_added = true;
    }
}

$statement = $pdo->query('SELECT * FROM `player` ORDER BY `player`.`rating` DeSC');
$statement->setFetchMode(PDO::FETCH_CLASS, 'Player');
$players = $statement->fetchAll();




?>

<html>
<head>
    <title>Bode Kicker Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <script>
        var stateObj = {a:"b"};
        history.replaceState(stateObj, "reset_GET", "index.php");

        <?php
            if(isset($player_added)){
                echo "alert('Player added!');";
            }
            if(isset($game_added)){
                echo "alert('Game added!');";
            }
        ?>
    </script>
</head>
<body>
    <div id='root'>
        <div class='box header'>
            <div class='title'>
                Kicker Elo<?php /*isset($win_percent)?print(" $win_percent"):print("");*/ ?>
            </div>
            <div class='config' style='flex-direction: column;'>
                Fall season has ended!<br>
                Final Scores:
            </div>

            <!-- <div class='config'>
                <div class='new_game'>
                    <a href='game.php'>Enter game</a>
                </div>
                <div class='new_player'>
                    <a href='player.php'>Add player</a>
                </div>
            </div>
            <div class='config' style='flex-direction: column;'>
                <p id='countdown'>Season ends soon!</p>
                <script>
                    // The data/time we want to countdown to
                    var countDownDate = new Date("Dec 16, 2021 18:00:00").getTime();

                    // Run myfunc every second
                    var myfunc = setInterval(function() {

                    var now = new Date().getTime();
                    var timeleft = countDownDate - now;
                        
                    // Calculating the days, hours, minutes and seconds left
                    var days = Math.floor(timeleft / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((timeleft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((timeleft % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((timeleft % (1000 * 60)) / 1000);
                        
                    // Result is output to the specific element
                    if (days == 0) {
                        document.getElementById("countdown").innerHTML = "Season ends in " + hours + "h " + minutes + "m " + seconds + "s"
                    
                    } else {
                        document.getElementById("countdown").innerHTML = "Season ends in " + days + "d " + hours + "h " + minutes + "m " + seconds + "s"
                    
                    }
                    }, 1000);
                </script>
            </div> -->
        </div>
        <?php
        $i = 1;
        foreach($players as $player){
            echo("
            <div class='box player'>
                <div id='rank'>$i</div>
                <div id='name'>$player->name</div>
                <div id='rating'>$player->rating</div>
                <a href='history.php?player_id=$player->player_id' class='player_hist'><span class='link-spanner'></span></a>
            </div>");
            $i++;
        }
        ?>
    </div>
</body>
</html>