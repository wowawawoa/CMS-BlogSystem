<?php session_start(); ?>
<?php include "db.php"; ?>
<?php include "../admin/functions.php"; ?>

<?php

if (isset($_POST['login'])) {
  $username = escape(trim($_POST['username']));
  $password = escape(trim($_POST['password']));
  $success_redirect_to = "../admin";
  $fail_redirect_to = "../index.php";

  login_user($username, $password, $success_redirect_to, $fail_redirect_to);
}

?>