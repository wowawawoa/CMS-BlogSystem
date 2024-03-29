<?php

if (isset($_POST['create_user'])) {
  $user_firstname = escape($_POST['user_firstname']);
  $user_lastname = escape($_POST['user_lastname']);
  $user_role = escape($_POST['user_role']);

  // $user_image = $_FILES['image']['name'];
  // $user_image_temp = $_FILES['image']['tmp_name'];

  $username = escape($_POST['username']);
  $user_email = escape($_POST['user_email']);
  $user_password = escape($_POST['user_password']);

  if (!empty($user_firstname) && !empty($user_lastname) && !empty($user_role) && !empty($username) && !empty($user_email) && !empty($user_password)) {
    $hashed_password = password_hash($user_password, PASSWORD_BCRYPT, array('cost' => 10));

    $query = "INSERT INTO users(user_firstname, user_lastname, user_role, username, user_email, user_password) ";

    $query .= "VALUES('{$user_firstname}', '{$user_lastname}', '{$user_role}', '{$username}', '{$user_email}', '{$hashed_password}') ";

    $create_user_query = mysqli_query($connection, $query);

    confirmQuery($create_user_query);

    redirect("users.php");
    // echo "User Created: " . " " . "<a href='users.php'>View Users</a>";
  }
}

?>

<form action="" method="post" enctype="multipart/form-data">
  <div class="form-group">
    <label for="user_firstname">FirstName</label>
    <input type="text" required class="form-control" name="user_firstname">
  </div>

  <div class="form-group">
    <label for="user_lastname">LastName</label>
    <input type="text" required class="form-control" name="user_lastname">
  </div>

  <div class="form-group">
    <select name="user_role" required id="">
      <option value="">Select Options</option>
      <option value="subscriber">Subscriber</option>
      <option value="admin">Admin</option>
    </select>
  </div>

  <!-- <div class="form-group">
    <label for="user_image">User Image</label>
    <input type="file" name="image">
  </div> -->

  <div class="form-group">
    <label for="username">Username</label>
    <input type="text" required class="form-control" name="username">
  </div>

  <div class="form-group">
    <label for="user_email">Email</label>
    <input type="email" required class="form-control" name="user_email">
  </div>

  <div class="form-group">
    <label for="user_password">Password</label>
    <input type="password" required class="form-control" name="user_password">
  </div>

  <div class="form-group">
    <input type="submit" class="btn btn-primary" name="create_user" value="Add User">
  </div>
</form>