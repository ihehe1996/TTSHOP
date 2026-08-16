<?php

require_once 'init.php';

doAction('init');

$ttDispatcher = Dispatcher::getInstance();


$ttDispatcher->dispatch();



View::output();

