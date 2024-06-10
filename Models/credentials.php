<?php

$dsn = "mysql:host=localhost;dbname=sae";
$login = "root";
$mdp = "root";
$bd = new PDO($dsn, $login, $mdp);
$bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>