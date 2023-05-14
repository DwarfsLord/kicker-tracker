<?php

require 'include.php';

$statement = $pdo->prepare('SELECT `game_id`, `time`, `loser1_id`, `loser1_old_elo`, `loser2_id`, `loser2_old_elo` FROM `game` WHERE `loser1_id` = :player_id OR `loser2_id` = :player_id');
$statement->execute([':player_id' => $_GET['player_id']]);
$loses2 = $statement->fetchAll();

$loses["single"] = [];
$loses["double"] = [];
$i = 0;
$j = 0;


if (count($loses2) != 0) {
    foreach ($loses2 as $loss) {
        if ($loss["loser1_id"] == $_GET['player_id']) {
            if (isset($loss["loser2_id"])) {
                $loses["double"][$j]["loser_old_elo"] = $loss["loser1_old_elo"];
                $loses["double"][$j]["game_id"] = $loss["game_id"];
                $loses["double"][$j]["time"] = $loss["time"];
                $j++;
            } else {
                $loses["single"][$i]["loser_old_elo"] = $loss["loser1_old_elo"];
                $loses["single"][$i]["game_id"] = $loss["game_id"];
                $loses["single"][$i]["time"] = $loss["time"];
                $i++;
            }
        } else {
            $loses["double"][$j]["loser_old_elo"] = $loss["loser2_old_elo"];
            $loses["double"][$j]["game_id"] = $loss["game_id"];
            $loses["double"][$j]["time"] = $loss["time"];
            $j++;
        }
    }
}

$statement = $pdo->prepare('SELECT `game_id`, `time`, `winner1_id`, `winner1_old_elo`, `winner2_id`, `winner2_old_elo` FROM `game` WHERE `winner1_id` = :player_id OR `winner2_id` = :player_id');
$statement->execute([':player_id' => $_GET['player_id']]);
$wins2 = $statement->fetchAll();

$wins["single"] = [];
$wins["double"] = [];
$i = 0;
$j = 0;


if (count($wins2) != 0) {
    foreach ($wins2 as $win) {
        if ($win["winner1_id"] == $_GET['player_id']) {
            if (isset($win["winner2_id"])) {
                $wins["double"][$j]["winner_old_elo"] = $win["winner1_old_elo"];
                $wins["double"][$j]["game_id"] = $win["game_id"];
                $wins["double"][$j]["time"] = $win["time"];
                $j++;
            } else {
                $wins["single"][$i]["winner_old_elo"] = $win["winner1_old_elo"];
                $wins["single"][$i]["game_id"] = $win["game_id"];
                $wins["single"][$i]["time"] = $win["time"];
                $i++;
            }
        } else {
            $wins["double"][$j]["winner_old_elo"] = $win["winner2_old_elo"];
            $wins["double"][$j]["game_id"] = $win["game_id"];
            $wins["double"][$j]["time"] = $win["time"];
            $j++;
        }
    }
}

$player = get_player_by_id($_GET['player_id'], $pdo);

function format_games(array $wins, array $loses, Player|false $player)
{
    $games = [];

    if (count($loses) + count($wins) != 0) {


        while (count($loses) + count($wins) > 0) {
            if (count($wins) == 0 || (count($loses) != 0 && ($loses[0]["game_id"] < $wins[0]["game_id"]))) {
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

        $games[] = [$old_state, $player->elo1, $old_time]; // TODO


    }

    return $games;
}

$games1 = format_games($wins["single"], $loses["single"], $player);
$games2 = format_games($wins["double"], $loses["double"], $player);

$no_data1 = (count($games1) == 0);
$no_data2 = (count($games2) == 0);
?>
<html>

<head>
    <title>Kicker Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });
        <?php echo $no_data1 ? "" : "google.charts.setOnLoadCallback(drawChart1);"; ?>
        <?php echo $no_data2 ? "" : "google.charts.setOnLoadCallback(drawChart2);"; ?>


        function drawChart1() {
            var data = new google.visualization.DataTable()
            data.addColumn('datetime', 'Date & Time');
            data.addColumn('number', 'ELO');
            rows = [
                <?php
                if (!$no_data1) {
                    foreach ($games1 as $game) {
                        $dt = preg_split("/[- :]/", $game[2]);
                        $dt[5] = (isset($dt[5])) ? $dt[5] : 0;
                        echo "[new Date('$dt[0]','" . ($dt[1] - 1) . "','$dt[2]','" . $dt[3] . "','$dt[4]','$dt[5]'), $game[1] ],";
                    }
                }
                ?>
            ];
            data.addRows(rows);

            var options = {
                curveType: 'function',
                backgroundColor: {
                    fill: 'transparent'
                },
                legend: {
                    position: 'none'
                },
            };

            var dateFormatter = new google.visualization.DateFormat({
                pattern: 'dd/MM/yyyy HH:mm'
            });
            dateFormatter.format(data, 0);

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart1'));

            chart.draw(data, options);
        }

        function drawChart2() {
            var data = new google.visualization.DataTable()
            data.addColumn('datetime', 'Date & Time');
            data.addColumn('number', 'ELO');
            rows = [
                <?php
                if (!$no_data2) {
                    foreach ($games2 as $game) {
                        $dt = preg_split("/[- :]/", $game[2]);
                        $dt[5] = (isset($dt[5])) ? $dt[5] : 0;
                        echo "[new Date('$dt[0]','" . ($dt[1] - 1) . "','$dt[2]','" . $dt[3] . "','$dt[4]','$dt[5]'), $game[1] ],";
                    }
                }
                ?>
            ];
            data.addRows(rows);

            var options = {
                curveType: 'function',
                backgroundColor: {
                    fill: 'transparent'
                },
                legend: {
                    position: 'none'
                },
            };

            var dateFormatter = new google.visualization.DateFormat({
                pattern: 'dd/MM/yyyy HH:mm'
            });
            dateFormatter.format(data, 0);

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart2'));

            chart.draw(data, options);
        }
    </script>
</head>

<body>
    <div id='root'>
        <div class='box header'>
            <div class='title'>
                Kicker Elo
            </div>
            <div class='config'>
                <div class='new_game'>
                    Stats: <?php echo $player->name; ?>
                </div>
            </div>
            <div class='config'>
                <div class='new_game'>
                    <a href='index.php'>Return</a>
                </div>
            </div>
        </div>
        <div class='box graph'>
            <?php
            if ($no_data1) {
                if($player->free_elo1 == 0){
                    echo "1v1: $player->elo1";
                }else{
                    echo "1v1: $player->elo1 (+$player->free_elo1)";
                }
            } else {
                if($player->free_elo1 == 0){
                    echo "1v1: $player->elo1";
                }else{
                    echo "1v1: $player->elo1 (+$player->free_elo1)";
                }
                echo "<br><div id='curve_chart1' style='width: 310px; height: 310px'></div>";
            }
            ?>
        </div>
        <div class='box graph'>
            <?php
            if ($no_data2) {
                if($player->free_elo2 == 0){
                    echo "2v2: $player->elo2";
                }else{
                    echo "2v2: $player->elo2 (+$player->free_elo2)";
                }
            } else {
                if($player->free_elo2 == 0){
                    echo "2v2: $player->elo2";
                }else{
                    echo "2v2: $player->elo2 (+$player->free_elo2)";
                }
                echo "<br><div id='curve_chart2' style='width: 310px; height: 310px'></div>";
            }
            ?>
        </div>
    </div>
</body>

</html>