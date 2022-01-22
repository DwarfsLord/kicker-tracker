<?php

declare(strict_types=1);

class Player
{
    public int $player_id;
    public string $name;
    public int $elo1;
    public int $free_elo1;
    public int $elo2;
    public int $free_elo2;
}

// require 'identity.php';
// $pdo = new PDO("mysql:host=$MYSQL_SERVER;dbname=$MYSQL_DB_NAME", $MYSQL_USER, $MYSQL_PASSWORD);

$pdo = new PDO('mysql:host=localhost;dbname=elo', 'root', '');

//$pdo->query("SET time_zone = 'Europe/Berlin'");

function add_player(string $name, PDO $pdo): bool
{
    $statement = $pdo->prepare("INSERT INTO `player` (`name`) VALUES (:new_name)");
    return $statement->execute([':new_name' => $name]);
}

function add_game(Player $winner, Player $loser, PDO $pdo): bool
{

    $statement = $pdo->prepare(
        "INSERT INTO `game` (`winner_id`, `winner_old_elo`, `loser_id`, `loser_old_elo`) VALUES (:winner_id, :winner_old_elo, :loser_id, :loser_old_elo)"
    );
    return $statement->execute(
        [
            ':winner_id' => $winner->player_id,
            ':winner_old_elo' => $winner->elo1,
            ':loser_id' => $loser->player_id,
            ':loser_old_elo' => $loser->player_id
        ]
    );
}

function get_player(string $name, PDO $pdo): Player|false
{
    $statement = $pdo->prepare("SELECT *  FROM `player` WHERE `name` LIKE :name");
    $statement->execute([':name' => $name]);
    return $statement->fetchObject('Player');
}

/**
 * @return array<int, Player>
 */
function get_players(PDO $pdo)
{
    $statement = $pdo->query('SELECT * FROM `player` ORDER BY GREATEST(`player`.`elo1`,`player`.`elo2`) DESC, `player`.`player_id` ASC');
    $statement->setFetchMode(PDO::FETCH_CLASS, 'Player');
    $return = $statement->fetchAll();
    return ($return != false) ? $return : [];
}

function set_player(Player $player, PDO $pdo): bool
{
    $statement = $pdo->prepare(
        "UPDATE `player` SET `name` = :name, `elo1` = :elo1, `free_elo1` = :free_elo1, `elo2` = :elo2, `free_elo2` = :free_elo2 WHERE `player`.`player_id` = :player_id"
    );
    return $statement->execute(
        [
            ':player_id' => $player->player_id,
            ':name' => $player->name,
            ':elo1' => $player->elo1,
            ':free_elo1' => $player->free_elo1,
            ':elo2' => $player->elo2,
            ':free_elo2' => $player->free_elo2
        ]
    );
}
