<pre>
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

function calculate_elo_test()
{
    $a = 1000;
    $b = 1000;
    // $a_elo_gained = 0;
    for ($i = 0; $i < 10; $i++) {
        calculate_elo($a, $b, ($i + 1) % 2, $a_elo_gained);
        $a += $a_elo_gained;
        $b -= $a_elo_gained;
        echo "$a <br>";
    }
}


function split_elo_test()
{
    $a = 1045;
    $b = 1355;
    echo join_elo($a, $b) . "<br><table>";
    echo "<tr><td>$a</td><td>$b</td></tr>";
    for ($i = 0; $i < 10; $i++) {
        split_elo($a, $b, 25, $a, $b);
        echo "<tr><td>$a</td><td>$b</td></tr>";
        split_elo($a, $b, 25, $a, $b);
        echo "<tr><td>$a</td><td>$b</td></tr>";
    }
    echo "</table>" . join_elo($a, $b) . "<br>";
}

function add_free_elo_test()
{
    $elo1 = 750;
    $f_elo1 = 250;
    $elo2 = 750;
    $f_elo2 = 250;

    echo "<table>";
    echo "<tr><td>$elo1</td><td>$f_elo1</td><td>$elo2</td><td>$f_elo2</td></tr>";

    for ($i = 0; $i < 6; $i++) {

        add_free_elo(true, $elo1, $f_elo1, $elo2, $f_elo2);
        $elo1 += 25;
        echo "<tr><td>$elo1</td><td>$f_elo1</td><td>$elo2</td><td>$f_elo2</td></tr>";

        add_free_elo(false, $elo1, $f_elo1, $elo2, $f_elo2);
        $elo1 -= 25;
        echo "<tr><td>$elo1</td><td>$f_elo1</td><td>$elo2</td><td>$f_elo2</td></tr>";
    }

    // add_free_elo(true, $elo2, $f_elo2, $elo1,$f_elo1);
    // $elo1 += 25;
    // echo "<tr><td>$elo1</td><td>$f_elo1</td><td>$elo2</td><td>$f_elo2</td></tr>";

    echo "</table>";
}

// split_elo_test();
// calculate_elo_test();
// join_elo_test();
// add_free_elo_test();
