<?php

require 'include.php';

// $statement = $pdo->prepare('SELECT `game_id`,`time`,`loser_old_elo` FROM `game` WHERE `loser_id` = :player_id');
// $statement->execute([':player_id' => $_GET['player_id']]);
// $loses = $statement->fetchAll();

// $statement = $pdo->prepare('SELECT `game_id`,`time`,`winner_old_elo` FROM `game` WHERE `winner_id` = :player_id');
// $statement->execute([':player_id' => $_GET['player_id']]);
// $wins = $statement->fetchAll();

$player = get_player_by_id($_GET['player_id'], $pdo);

// if (count($loses) + count($wins) == 0) {
    $no_data = true;
// } else {
//     $no_data = false;


//     $games = [];

//     while (count($loses) + count($wins) > 0) {
//         if (count($wins) == 0 || (count($loses) != 0 && ($loses[0]["game_id"] < $wins[0]["game_id"]))) {
//             $loss = array_shift($loses);
//             if (!isset($old_time)) {
//                 $time = new DateTime($loss["time"]);
//                 $time->sub(new DateInterval('PT10M'));
//                 $old_time = $time->format('Y-m-d H:i');
//                 $old_state = "new";
//             }
//             $games[] = [$old_state, $loss["loser_old_elo"], $old_time];
//             $old_state = "loss";
//             $old_time = $loss["time"];
//         } else {
//             $win = array_shift($wins);
//             if (!isset($old_time)) {
//                 $time = new DateTime($win["time"]);
//                 $time->sub(new DateInterval('PT10M'));
//                 $old_time = $time->format('Y-m-d H:i:s');
//                 $old_state = "new";
//             }
//             $games[] = [$old_state, $win["winner_old_elo"], $old_time];
//             $old_state = "win";
//             $old_time = $win["time"];
//         }
//     }

//     $games[] = [$old_state, $player->rating, $old_time];
// }
?>
<html>

<head>
    <title>Bode Kicker Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });
        <?php echo $no_data ? "" : "google.charts.setOnLoadCallback(drawChart);"; ?>


        function drawChart() {
            var data = new google.visualization.DataTable()
            data.addColumn('datetime', 'Date & Time');
            data.addColumn('number', 'ELO');
            rows = [
                <?php
                if (!$no_data) {
                    foreach ($games as $game) {
                        $dt = preg_split("/[- :]/", $game[2]);
                        //var_dump($dt);
                        $dt[5] = (isset($dt[5])) ? $dt[5] : 0;
                        echo "[new Date('$dt[0]','" . ($dt[1] - 1) . "','$dt[2]','" . $dt[3] . "','$dt[4]','$dt[5]'), $game[1] ],";
                        //echo "[new Date('$dt[0]','" . intval($dt[1])-1 . "','$dt[2]','" . $dt[3] . "','$dt[4]','$dt[5]'), $game[1] ],";
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

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

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
            if ($no_data) {
                echo "There is no data on this player yet!";
            } else {
                echo "<div id='curve_chart' style='width: 310px; height: 310px'></div>";
            }
            ?>
        </div>
    </div>
</body>

</html>