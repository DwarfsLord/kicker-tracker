<?php

function join_elo($a, $b)
{
    return min(
        min($a, $b) + abs($a - $b) * 0.5 + (0.03 * abs($a - $b)) ** 2,
        max($a, $b)
    );
}

function split_elo($a, $b, $elo_change, &$res_a, &$res_b)
{
    $BASE = 2;
    $DIVISOR = 70;
    $responsibility_a = 1 / (1 + $BASE ** (($a - $b) / $DIVISOR));
    $responsibility_a = ($elo_change < 0) ? 1 - $responsibility_a : $responsibility_a;
    // echo 1-$responsibility_a."<br>";

    $res_a = round($responsibility_a * $elo_change * 2);
    $res_b = round((1 - $responsibility_a) * $elo_change * 2);

    //Guarantee change
    if ($res_a  == 0) {
        $res_a = $elo_change <=> 0;
        $res_b = $res_b + (0 <=> $elo_change);
        // echo "call 1 <br>";
    } else if ($res_b == 0) {
        $res_b = $elo_change <=> 0;
        $res_a = $res_a + (0 <=> $elo_change);
        // echo "call 2 <br>";
    }

    $res_a += $a;
    $res_b += $b;

    //Guarantee combined elo increase
    if ($elo_change > 0) {
        $old_combined_elo = join_elo($a, $b);
        while ($old_combined_elo > join_elo($res_a, $res_b)) {
            $res_a += $res_a <=> $res_b;
            $res_b += $res_b <=> $res_a;
            // echo "call 3 <br>";

        }
    }
}