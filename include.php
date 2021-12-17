<?php
require 'identity.php';

class Player{
    public $player_id;
    public $name;
    public $rating;
}

$pdo = new PDO("mysql:host=$MYSQL_SERVER;dbname=$MYSQL_DB_NAME", $MYSQL_USER, $MYSQL_PASSWORD);

//$pdo = new PDO('mysql:host=localhost;dbname=elo', 'root', '');

//$pdo->query("SET time_zone = 'Europe/Berlin'");