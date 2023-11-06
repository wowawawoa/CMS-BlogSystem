<?php

ob_start();

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "cms_blogsystem");
define("DB_PORT", 3307);

// $db['DB_HOST'] = "localhost";
// $db['DB_USER'] = "root";
// $db['DB_PASS'] = "";
// $db['DB_NAME'] = "cms_blogsystem";
// $db['DB_PORT'] = 3307;

// foreach ($db as $key => $value) {
//   define($key, $value);
// }

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

$query = "SET NAMES utf8";
mysqli_query($connection, $query);
