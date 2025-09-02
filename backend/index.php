<?php
require_once('config/conn.php');
require_once('config/helper.php');

$data = [];

echo generateResponseBody(200, $data, 'Welcome to Quiz App');