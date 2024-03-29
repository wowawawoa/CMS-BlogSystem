<?php

if (isset($_GET['edit_user'])) {
  $the_user_id = escape($_GET['edit_user']);

  if (!$the_user_id) {
    redirect("users.php");
  }

  $query = "SELECT * FROM users WHERE user_id = $the_user_id ";
  $select_users_query = mysqli_query($connection, $query);

  confirmQuery($select_users_query);

  while ($row = mysqli_fetch_assoc($select_users_query)) {
    $user_id = $row['user_id'];
    $username = $row['username'];
    $user_password = $row['user_password'];
    $user_firstname = $row['user_firstname'];
    $user_lastname = $row['user_lastname'];
    $user_email = $row['user_email'];
    $user_image = $row['user_image'];
    $user_role = $row['user_role'];
  }

  if (mysqli_num_rows($select_users_query) === 0) {
    redirect("users.php");
  }
}

if (isset($_POST['edit_user'])) {
  $user_firstname = escape($_POST['user_firstname']);
  $user_lastname = escape($_POST['user_lastname']);
  $user_role = escape($_POST['user_role']);

  // $user_image = $_FILES['image']['name'];
  // $user_image_temp = $_FILES['image']['tmp_name'];

  $username = escape($_POST['username']);
  $user_email = escape($_POST['user_email']);
  $user_password = escape($_POST['user_password']);

  $hashed_password = password_hash($user_password, PASSWORD_BCRYPT, array('cost' => 10));

  // move_uploaded_file($user_image_temp, "../images/$user_image");

  $query = "UPDATE users SET ";
  $query .= "user_firstname = '{$user_firstname}', ";
  $query .= "user_lastname = '{$user_lastname}', ";
  $query .= "user_role = '{$user_role}', ";
  $query .= "username = '{$username}', ";
  $query .= "user_email = '{$user_email}', ";
  $query .= "user_password = '{$hashed_password}' ";
  $query .= "WHERE user_id = {$the_user_id} ";

  $edit_user_query = mysqli_query($connection, $query);

  confirmQuery($edit_user_query);

  echo "User Updated" . " <a href='users.php'>View Users?</a>";
}

?>

<form action="" method="post" enctype="multipart/form-data">
  <div class="form-group">
    <label for="user_firstname">FirstName</label>
    <input type="text" value="<?php echo $user_firstname ?>" class="form-control" name="user_firstname">
  </div>

  <div class="form-group">
    <label for="user_lastname">LastName</label>
    <input type="text" value="<?php echo $user_lastname ?>" class="form-control" name="user_lastname">
  </div>

  <div class="form-group">
    <select name="user_role" id="">
      <option value="<?php echo $user_role; ?>"><?php echo $user_role; ?></option>

      <?php

      if ($user_role === 'admin') {
        echo "<option value='subscriber'>subscriber</option>";
      } else {
        echo "<option value='admin'>admin</option>";
      }

      ?>

    </select>
  </div>

  <!-- <div class="form-group">
    <label for="user_image">User Image</label>
    <input type="file" name="image">
  </div> -->

  <div class="form-group">
    <label for="username">Username</label>
    <input type="text" value="<?php echo $username ?>" class="form-control" name="username">
  </div>

  <div class="form-group">
    <label for="user_email">Email</label>
    <input type="email" value="<?php echo $user_email ?>" class="form-control" name="user_email">
  </div>

  <div class="form-group">
    <label for="user_password">Password</label>
    <input type="password" autocomplete="off" required class="form-control" name="user_password">
  </div>

  <div class="form-group">
    <input type="submit" class="btn btn-primary" name="edit_user" value="Update User">
  </div>
</form>