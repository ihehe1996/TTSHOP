<?php

require_once 'globals.php';

echo 'test.php';

$db = Database::getInstance();
$db_prefix = DB_PREFIX;

$db->beginTransaction();


$sql = "INSERT INTO `{$db_prefix}test` (`content`) VALUES ('11')";
$db->query($sql);


$db->commit();
