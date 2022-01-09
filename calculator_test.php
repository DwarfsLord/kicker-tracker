<?php
require 'calculator.php';

function join_elo_test()
{
    echo join_elo(750.0, 750.0) . "<br>";
    echo join_elo(750.0, 760.0) . "<br>";
    echo join_elo(750.0, 770.0) . "<br>";
    echo join_elo(750.0, 780.0) . "<br>";
    echo join_elo(750.0, 790.0) . "<br>";
    echo join_elo(750.0, 800.0) . "<br>";
    echo join_elo(750.0, 900.0) . "<br>";
    echo join_elo(750.0, 1000.0) . "<br>";
    echo join_elo(750.0, 1100.0) . "<br>";
    echo join_elo(750.0, 1200.0) . "<br>";
    echo join_elo(750.0, 1300.0) . "<br>";
    echo join_elo(750.0, 1400.0) . "<br>";
}

function split_elo_test(){
    $a = 1045;
    $b = 1355;
    echo join_elo($a, $b) . "<br><table>";
    echo "<tr><td>$a</td><td>$b</td></tr>";
    for ($i=0; $i < 25; $i++) { 
        split_elo($a, $b, 25, $a, $b);
        echo "<tr><td>$a</td><td>$b</td></tr>";
        split_elo($a, $b, 25, $a, $b);
        echo "<tr><td>$a</td><td>$b</td></tr>";
    }
    echo "</table>".join_elo($a, $b) . "<br>";
}

split_elo_test();
//join_elo_test();