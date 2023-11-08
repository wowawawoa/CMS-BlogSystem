<?php

// Database Helper Functions

function confirmQuery($result)
{
  global $connection;

  if (!$result) {
    die("QUERY FAILED ." . mysqli_error($connection));
  }
}

function redirect($location)
{
  header("Location: " . $location);
  exit;
}

function query($query)
{
  global $connection;

  $result = mysqli_query($connection, $query);
  confirmQuery($result);

  return $result;
}

function fetchRecords($result)
{
  return mysqli_fetch_array($result);
}

function ifItIsMethod($method = null)
{
  if ($_SERVER['REQUEST_METHOD'] === strtoupper($method)) {
    return true;
  }

  return false;
}

// End Database Helper Functions

// General Functions

function get_user_name()
{
  return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

// End General Functions

// User Helper Functions

function isLoggedIn()
{
  if (isset($_SESSION['user_role'])) {
    return true;
  }

  return false;
}

function loggedInUserId()
{
  if (isLoggedIn()) {
    $result = query("SELECT * FROM users WHERE username = '" . $_SESSION['username'] . "'");
    confirmQuery($result);
    $user = mysqli_fetch_array($result);

    return mysqli_num_rows($result) >= 1 ? $user['user_id'] : false;
  }

  return false;
}

function userLikedThisPost($post_id)
{
  $result = query("SELECT * FROM likes WHERE user_id=" . loggedInUserId() . " AND post_id={$post_id}");
  confirmQuery($result);
  return mysqli_num_rows($result) >= 1 ? true : false;
}

function getPostLikes($post_id)
{
  $result = query("SELECT * FROM likes WHERE post_id = $post_id");
  confirmQuery($result);
  echo mysqli_num_rows($result);
}

function checkIfUserIsLoggedInAndRedirect($redirectLocation = null)
{
  if (isLoggedIn()) {
    redirect($redirectLocation);
  }
}

function insert_categories()
{
  global $connection;

  if (isset($_POST['submit'])) {
    $cat_title = escape($_POST['cat_title']);

    if ($cat_title === "" || empty($cat_title)) {
      echo "This field should not be empty";
    } else {
      $query = "INSERT INTO categories(cat_title) ";
      $query .= "VALUE('{$cat_title}') ";

      $create_category_query = mysqli_query($connection, $query);

      if (!$create_category_query) {
        die('QUERY FAILED' . mysqli_error($connection));
      }
    }
  }
}

function findAllCategories()
{
  global $connection;

  $query = "SELECT * FROM categories";
  $select_categories = mysqli_query($connection, $query);

  while ($row = mysqli_fetch_assoc($select_categories)) {
    $cat_id = $row['cat_id'];
    $cat_title = $row['cat_title'];

    echo "<tr>";
    echo "<td>{$cat_id}</td>";
    echo "<td>{$cat_title}</td>";
    echo "<td><a href='categories.php?delete={$cat_id}'>Delete</a></td>";
    echo "<td><a href='categories.php?edit={$cat_id}'>Edit</a></td>";
    echo "</tr>";
  }
}

function deleteCategories()
{
  global $connection;

  if (isset($_GET['delete'])) {
    $the_cat_id = escape($_GET['delete']);
    $query = "DELETE FROM categories WHERE cat_id = {$the_cat_id} ";
    $delete_query = mysqli_query($connection, $query);
    redirect("categories.php");
  }
}

function users_online()
{
  if (isset($_GET['onlineusers'])) {
    global $connection;

    if (!$connection) {
      session_start();
      include("../includes/db.php");
      $session = session_id();
      $time = time();
      $time_out_in_seconds = 05;
      $time_out = $time - $time_out_in_seconds;

      $query = "SELECT * FROM users_online WHERE session = '$session'";
      $send_query = mysqli_query($connection, $query);
      $count = mysqli_num_rows($send_query);

      if ($count === NULL) {
        mysqli_query($connection, "INSERT INTO users_online(session, time) VALUES('$session', '$time')");
      } else {
        mysqli_query($connection, "UPDATE users_online SET time = '$time' WHERE session = '$session'");
      }

      $users_online_query = mysqli_query($connection, "SELECT * FROM users_online WHERE time > '$time_out'");
      $count_user = mysqli_num_rows($users_online_query);

      echo $count_user;
    }
  }
}

users_online();

function escape($string)
{
  global $connection;

  return mysqli_real_escape_string($connection, trim($string));
}

function recordCount($table)
{
  $select_all_posts = query("SELECT * FROM " . $table);
  $count_result = mysqli_num_rows($select_all_posts);

  return $count_result;
}

function count_records($result)
{
  return mysqli_num_rows($result);
}

function checkStatus($table, $column, $status)
{
  $check_status = query("SELECT * FROM $table WHERE $column = '$status'");

  return mysqli_num_rows($check_status);
}

function checkUserRole($table, $column, $role)
{
  $check_user_role = query("SELECT * FROM $table WHERE $column = '$role'");

  return mysqli_num_rows($check_user_role);
}

function is_admin()
{
  if (isLoggedIn()) {
    $result = query("SELECT user_role FROM users WHERE user_id = " . loggedInUserId() . "");
    $row = fetchRecords($result);

    if ($row['user_role'] === 'admin') {
      return true;
    } else {
      return false;
    }
  }
  return false;
}

function username_exists($username)
{
  $result = query("SELECT username FROM users WHERE username = '$username'");

  if (mysqli_num_rows($result) > 0) {
    return true;
  } else {
    return false;
  }
}

function email_exists($email)
{
  $result = query("SELECT user_email FROM users WHERE user_email = '$email'");

  if (mysqli_num_rows($result) > 0) {
    return true;
  } else {
    return false;
  }
}

function register_user($username, $email, $password)
{
  global $connection;

  $hashed_password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 10));

  $query = "INSERT INTO users (username, user_email, user_password, user_role) ";
  $query .= "VALUES ('{$username}', '{$email}', '{$hashed_password}', 'subscriber')";
  $register_user_query = mysqli_query($connection, $query);
  confirmQuery($register_user_query);
}

function login_user($username, $password, $success_redirect_to, $fail_redirect_to)
{
  global $connection;

  $query = "SELECT * FROM users WHERE username = '{$username}' ";
  $select_user_query = mysqli_query($connection, $query);
  confirmQuery($select_user_query);

  while ($row = mysqli_fetch_array($select_user_query)) {
    $db_user_id = $row['user_id'];
    $db_username = $row['username'];
    $db_user_password = $row['user_password'];
    $db_user_firstname = $row['user_firstname'];
    $db_user_lastname = $row['user_lastname'];
    $db_user_role = $row['user_role'];
  }

  $check_password = password_verify($password, $db_user_password);

  if ($username === $db_username && $check_password) {
    $_SESSION['username'] = $db_username;
    $_SESSION['firstname'] = $db_user_firstname;
    $_SESSION['lastname'] = $db_user_lastname;
    $_SESSION['user_role'] = $db_user_role;
    $_SESSION['user_id'] = $db_user_id;

    redirect($success_redirect_to);
  } else {
    redirect($fail_redirect_to);
  }
}

function imagePlaceholder($image = '')
{
  if (!$image) {
    return 'img_placeholder.jpg';
  } else {
    return $image;
  }
}

function get_all_user_posts()
{
  return query("SELECT * FROM posts INNER JOIN users ON posts.post_user = users.username WHERE users.user_id=" . loggedInUserId() . "");
}

function get_all_posts_user_comments()
{
  return query("SELECT * FROM posts
  INNER JOIN comments ON posts.post_id = comments.comment_post_id
  WHERE post_user= '" . get_user_name() . "'");
}

function get_all_user_categories()
{
  return query("SELECT DISTINCT categories.cat_title
  FROM posts
  JOIN categories ON posts.post_category_id = categories.cat_id
  WHERE posts.post_user= '" . get_user_name() . "'");
}

function get_all_user_published_posts()
{
  return query("SELECT * FROM posts WHERE post_user= '" . get_user_name() . "'" . " AND post_status='published'");
}

function get_all_user_draft_posts()
{
  return query("SELECT * FROM posts WHERE post_user= '" . get_user_name() . "'" . " AND post_status='draft'");
}


function get_all_user_approved_posts_comments()
{
  return query("SELECT * FROM posts
  INNER JOIN comments ON posts.post_id = comments.comment_post_id
  WHERE post_user= '" . get_user_name() . "'" . " AND comment_status='approved'");
}


function get_all_user_unapproved_posts_comments()
{
  return query("SELECT * FROM posts
  INNER JOIN comments ON posts.post_id = comments.comment_post_id
  WHERE post_user= '" . get_user_name() . "'" . " AND comment_status='unapproved'");
}
