<html>
<head>
    <title>Bode Kicker Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
    </script>
</head>
<body>


<?php
require 'include.php';

function get_player_by_id($id, $pdo)
{
    $statement = $pdo->prepare("SELECT * FROM `player` WHERE `player_id` LIKE :player_id");
    $statement->execute([':player_id' => $id]);
    $player = $statement->fetchObject('Player');

    return $player;
}

function get_chances($winner_rating, $loser_rating, $konst_A = 91, $konst_K = 67)
{
    if ($winner_rating > $loser_rating) {
        $win_percent = 1-(1/(exp(abs($winner_rating-$loser_rating)/$konst_A)+1));
    } else {
        $win_percent = (1/(exp(abs($winner_rating-$loser_rating)/$konst_A)+1));
    }

    $winner_new_rating = $winner_rating + ceil($konst_K * (1-$win_percent));
    $loser_new_rating = $loser_rating - ceil($konst_K * (1-$win_percent));

    return [$winner_new_rating, $loser_new_rating, $win_percent];
}

echo "<h1>The Upset:</h1>";//----------------------------------------------------------------------

$statement = $pdo->query('SELECT * FROM `game` WHERE `loser_old_elo` != 1000 ORDER BY (CAST(`winner_old_elo` AS SIGNED)- CAST(`loser_old_elo` AS SIGNED));');
$games = $statement->fetchAll(PDO::FETCH_ASSOC);

$upsetter = get_player_by_id($games[0]["winner_id"], $pdo);
$upsetted = get_player_by_id($games[0]["loser_id"], $pdo);

$chances = get_chances($games[0]['winner_old_elo'], $games[0]['loser_old_elo']);

echo "Upsetter: $upsetter->name (".$games[0]['winner_old_elo'].") (". round(100*$chances[2]) ."%)<br>Upsetted: $upsetted->name (".$games[0]['loser_old_elo'].")";


echo "<h1>The Obvious:</h1>";//----------------------------------------------------------------------

$obvious_game = end($games);

$winner = get_player_by_id($obvious_game["winner_id"], $pdo);
$loser = get_player_by_id($obvious_game["loser_id"], $pdo);

$chances = get_chances($obvious_game['winner_old_elo'], $obvious_game['loser_old_elo']);

echo "Winner: $winner->name (".$obvious_game['winner_old_elo'].") (". round(100*$chances[2]) ."%)<br>Loser: $loser->name (".$obvious_game['loser_old_elo'].")";

//---------------------------------------------------------------------- OPTIMIST

//SELECT (CAST(`winner_old_elo` AS SIGNED)- CAST(`loser_old_elo` AS SIGNED)), `game_id`, `winner_id` FROM `game` WHERE `winner_id` = 1 OR `loser_id` = 1;

$statement = $pdo->query('SELECT * FROM `player` ORDER BY `player`.`rating` DeSC');
$statement->setFetchMode(PDO::FETCH_CLASS, 'Player');
$players = $statement->fetchAll();

$matchups = [];
$advantages = [];
$game_count = [];

$scoring = [];

foreach ($players as $player) {
    $statement = $pdo->query("SELECT (CAST(`winner_old_elo` AS SIGNED)- CAST(`loser_old_elo` AS SIGNED)), `game_id`, `winner_id` FROM `game` WHERE `winner_id` = $player->player_id OR `loser_id` = $player->player_id;");
    $games = $statement->fetchAll(PDO::FETCH_NUM);
    $advantage_average = 0;
    foreach ($games as $game) {
        if ($game[2] == $player->player_id) {
            $elo_advantage=$game[0];
        } else {
            $elo_advantage=-1*$game[0];
        }
        $matchups[$player->player_id][] = [$elo_advantage,$game[1]];
        $advantage_average += $elo_advantage;
    }
    
    $advantages[$player->player_id] = count($games)==0?0:$advantage_average/count($games);

    $game_count[$player->player_id] = count($games);

    $scoring[$player->player_id] = $player->rating+30*count($games);
}
//var_dump($matchups);
echo "<h1>The Optimist:</h1>";

while (true) {
    $highest_advantage = array_search(min($advantages), $advantages);
    if (!isset($matchups[$highest_advantage]) || (count($matchups[$highest_advantage])<3)) {
        unset($advantages[$highest_advantage]);
    } else {
        break;
    }
}

$optimist = get_player_by_id($highest_advantage, $pdo);
echo $optimist->name . " (Average advantage: ".round($advantages[$highest_advantage], 2).')';

echo "<h1>The Vulture:</h1>";//----------------------------------------------------------------------

while (true) {
    $highest_advantage = array_search(max($advantages), $advantages);
    if (!isset($matchups[$highest_advantage]) || (count($matchups[$highest_advantage]))<3) {
        unset($advantages[$highest_advantage]);
    } else {
        break;
    }
}

$optimist = get_player_by_id($highest_advantage, $pdo);
echo $optimist->name. " (Average advantage: ".round($advantages[$highest_advantage], 2).')';

echo "<h1>The Realist:</h1>";//----------------------------------------------------------------------

$abs_advantages = array_map("abs", $advantages);
while (true) {
    $highest_advantage = array_search(min($abs_advantages), $abs_advantages);
    if (!isset($matchups[$highest_advantage]) || (count($matchups[$highest_advantage]))<3) {
        unset($abs_advantages[$highest_advantage]);
    } else {
        break;
    }
}

$optimist = get_player_by_id($highest_advantage, $pdo);
echo $optimist->name. " (Average advantage: ".round($advantages[$highest_advantage], 2).') Games played: '.$game_count[$highest_advantage];

echo "<h1>The Activists:</h1>";//----------------------------------------------------------------------
$most_games = array_keys($game_count, max($game_count));
foreach ($most_games as $most_game) {
    echo get_player_by_id($most_game, $pdo)->name." played " . max($game_count) . " games!<br>";
    unset($game_count[$most_game]);
}
$most_games = array_keys($game_count, max($game_count));
foreach ($most_games as $most_game) {
    echo get_player_by_id($most_game, $pdo)->name." played " . max($game_count) . " games!<br>";
    unset($game_count[$most_game]);
}
$most_games = array_keys($game_count, max($game_count));
foreach ($most_games as $most_game) {
    echo get_player_by_id($most_game, $pdo)->name." played " . max($game_count) . " games!<br>";
    unset($game_count[$most_game]);
}

echo "<h1>The Players:</h1>";//----------------------------------------------------------------------
for ($k=0; $k < 20; $k++) { 
    $most_scoring = array_keys($scoring, max($scoring));
    foreach ($most_scoring as $most_game) {
        echo get_player_by_id($most_game, $pdo)->name." scored " . max($scoring) . " points!<br>";
        unset($scoring[$most_game]);
    }
}



// echo '<pre>' . var_export($advantages, true) . '</pre>';
// ksort($advantages);
// echo '<pre>' . var_export($advantages, true) . '</pre>';
// sort($advantages);
// echo '<pre>' . var_export($advantages, true) . '</pre>';

//SELECT * FROM `game` ORDER BY (CAST(`winner_old_elo` AS SIGNED)- CAST(`loser_old_elo` AS SIGNED));
?>
<script>
    function drawChart(rows, name, div, colour, grid = false) {
        var data = new google.visualization.DataTable()
        data.addColumn('datetime', 'Date & Time');
        data.addColumn('number', name);
        data.addRows(rows);

        if (grid) {
            var options = {
                curveType: 'function',
                backgroundColor: { fill:'transparent' },
                legend: { position: 'none' },
                series: {
                    0: { color: colour },
                },
                vAxis: {
                    viewWindowMode:'explicit',
                    viewWindow:{
                        max:1200,
                        min:950
                    }
                },
                hAxis: {
                    viewWindowMode:'explicit',
                    viewWindow:{
                        max:new Date("2021-12-12 18:0:0"),
                        min:new Date("2021-12-16 18:0:0")
                    }
                },
            };
        } else {
            var options = {
                curveType: 'function',
                backgroundColor: { fill:'transparent' },
                legend: { position: 'none' },
                series: {
                    0: { color: colour },
                },
                vAxis: {
                    gridlines: {
                        color: 'transparent'
                    }, 
                    viewWindowMode:'explicit',
                    viewWindow:{
                        max:1200,
                        min:950
                    }
                },
                hAxis: {
                    gridlines: {
                        color: 'transparent'
                    },
                    labels: {
                        color: 'transparent'
                    }, 
                    viewWindowMode:'explicit',
                    viewWindow:{
                        max:new Date("2021-12-12 18:0:0"),
                        min:new Date("2021-12-16 18:0:0")
                    }
                },
            };
        }

        

        var dateFormatter = new google.visualization.DateFormat({pattern: 'dd/MM/yyyy HH:mm'});
           dateFormatter.format(data, 0);

        var chart = new google.visualization.LineChart(div);

        chart.draw(data, options);
      }
      <?php
        function drawChartPHP($player_id, $color, $pdo,$id)
        {
            $statement = $pdo->prepare('SELECT `game_id`,`time`,`loser_old_elo` FROM `game` WHERE `loser_id` = :player_id');
            $statement->execute([':player_id' => $player_id]);
            $loses = $statement->fetchAll();

            $statement = $pdo->prepare('SELECT `game_id`,`time`,`winner_old_elo` FROM `game` WHERE `winner_id` = :player_id');
            $statement->execute([':player_id' => $player_id]);
            $wins = $statement->fetchAll();

            $statement = $pdo->prepare("SELECT *  FROM `player` WHERE `player_id` LIKE :player_id");
            $statement->execute([':player_id' => $player_id]);
            $player = $statement->fetchObject('Player');

            if (count($loses)+count($wins) == 0) {
                $no_data = true;
            }else {
                $no_data = false;


                $games = [];

                while (count($loses)+count($wins) > 0) {
                    if (count($wins) == 0 || (count($loses) != 0 && ($loses[0]["game_id"]<$wins[0]["game_id"]))) {
                        $loss = array_shift($loses);
                        if (!isset($old_time)) {
                            $time = new DateTime($loss["time"]);
                            $time->sub(new DateInterval('PT10M'));
                            $old_time = $time->format('Y-m-d H:i');
                            $old_state = "new";
                        }
                        $games[] = [$old_state, $loss["loser_old_elo"], $old_time];
                        $old_state = "loss";
                        $old_time = $loss["time"];
                    } else {
                        $win = array_shift($wins);
                        if (!isset($old_time)) {
                            $time = new DateTime($win["time"]);
                            $time->sub(new DateInterval('PT10M'));
                            $old_time = $time->format('Y-m-d H:i:s');
                            $old_state = "new";
                        }
                        $games[] = [$old_state, $win["winner_old_elo"], $old_time];
                        $old_state = "win";
                        $old_time = $win["time"];
                    }
                }

                $games[] = [$old_state, $player->rating, $old_time];
                $games[] = ["current", $player->rating, "2021-12-16 18:0:0"];
            }
            echo "drawChart([";
            // $games = array ( 0 => array ( 0 => 'new', 1 => '1041', 2 => '2021-12-12 18:02:20', ), 1 => array ( 0 => 'win', 1 => '1078', 2 => '2021-12-12 18:12:20', ), 2 => array ( 0 => 'win', 1 => '1113', 2 => '2021-12-13 14:20:48', ), 3 => array ( 0 => 'loss', 1 => '1073', 2 => '2021-12-13 14:34:03', ), 4 => array ( 0 => 'win', 1 => '1082', 2 => '2021-12-13 15:48:52', ), 5 => array ( 0 => 'win', 1 => '1127', 2 => '2021-12-14 17:07:24', ), 6 => array ( 0 => 'win', 1 => '1144', 2 => '2021-12-15 13:26:00', ), 7 => array ( 0 => 'win', 1 => '1161', 2 => '2021-12-15 16:03:37', ), );
                foreach ($games as $game) {
                    $dt = preg_split("/[- :]/", $game[2]);
                    //var_dump($dt);
                    $dt[5] = (isset($dt[5])) ? $dt[5] : 0 ;
                    echo "[new Date('$dt[0]','" . ($dt[1]-1) . "','$dt[2]','" . $dt[3] . "','$dt[4]','$dt[5]'), $game[1] ],";
                }
            echo "],'ELO',document.getElementById('curve_chart$id'),'$color',". ($id == 1?"true":"false") .");";
        }?>
      function drawCharts() {
            <?php
            $statement = $pdo->query('SELECT * FROM `player` ORDER BY `player`.`rating` DeSC');
            $statement->setFetchMode(PDO::FETCH_CLASS, 'Player');
            $players = $statement->fetchAll();
            drawChartPHP($players[0]->player_id,"#e2431e",$pdo,1);
            drawChartPHP($players[1]->player_id,"#e7711b",$pdo,2);
            drawChartPHP($players[2]->player_id,"#f1ca3a",$pdo,3);
            drawChartPHP($players[3]->player_id,"#6f9654",$pdo,4);
            drawChartPHP($players[4]->player_id,"#1c91c0",$pdo,5);
            drawChartPHP($players[5]->player_id,"#43459d",$pdo,6);
            ?>
            
      }
      google.charts.setOnLoadCallback(drawCharts);
    //   '#6f9654''#1c91c0''#43459d'
</script>
<div class='chartbox'>
    <div id='curve_chart1' class='chart' style='width: 1000px; height: 1000px'></div>
    <div id='curve_chart2' class='chart' style='width: 1000px; height: 1000px'></div>
    <div id='curve_chart3' class='chart' style='width: 1000px; height: 1000px'></div>
    <div id='curve_chart4' class='chart' style='width: 1000px; height: 1000px'></div>
    <div id='curve_chart5' class='chart' style='width: 1000px; height: 1000px'></div>
    <div id='curve_chart6' class='chart' style='width: 1000px; height: 1000px'></div>
</div>
</body>