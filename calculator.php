<?php

function join_elo($a, $b)
{
    return min(
        min($a, $b) + abs($a - $b) * 0.5 + (0.03 * abs($a - $b)) ** 2,
        max($a, $b)
    );
}

function calculate_elo($a, $b, $a_won, &$a_elo_gained)
{
    $MAX_ELO = 50;
    $CONST_A = 43; //43.4294

    $b_win_percent = (1 / (exp(($a - $b) / $CONST_A) + 1));

    $a_elo_gained = $a_won ?
        ceil($MAX_ELO * ($b_win_percent)) :
        -1 * ceil($MAX_ELO * (1 - $b_win_percent));
}

function split_elo($a, $b, $elo_change, &$res_a, &$res_b)
{
    $BASE = 2;
    $DIVISOR = 70;

    $responsibility_a = 1 / (1 + $BASE ** (($a - $b) / $DIVISOR));
    $responsibility_a = ($elo_change < 0) ? 1 - $responsibility_a : $responsibility_a;

    $res_a = round($responsibility_a * $elo_change * 2);
    $res_b = round((1 - $responsibility_a) * $elo_change * 2);

    //Guarantee change
    if ($res_a  == 0) {
        $res_a = $elo_change <=> 0;
        $res_b = $res_b + (0 <=> $elo_change);
    } else if ($res_b == 0) {
        $res_b = $elo_change <=> 0;
        $res_a = $res_a + (0 <=> $elo_change);
    }

    $res_a += $a;
    $res_b += $b;

    //Guarantee combined elo increase
    if ($elo_change > 0) {
        $old_combined_elo = join_elo($a, $b);
        while ($old_combined_elo > join_elo($res_a, $res_b)) {
            $res_a += $res_a <=> $res_b;
            $res_b += $res_b <=> $res_a;
        }
    }
}
