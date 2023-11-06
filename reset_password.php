<?php include "includes/db.php"; ?>
<?php include "includes/header.php"; ?>

<?php

if (!isset($_GET['email']) && !isset($_GET['token'])) {
  redirect('index.php');
}

// $token = '1d1d714de9b9273e881c7d89f07249bd7bd5c77f06d0863724ebb147e38c08f3e6a0c8b9c02ddd6d413ec63172762aae1c55';
// $email = 'test1@test.com';

if ($stmt = mysqli_prepare($connection, 'SELECT username, user_email, token FROM users WHERE token= ? ')) {
  mysqli_stmt_bind_param($stmt, 's', $_GET['token']);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $username, $user_email, $token);
  mysqli_stmt_fetch($stmt);
  mysqli_stmt_close($stmt);

  // if ($_GET['token'] !== $token || $_GET['email'] !== $user_email) {
  //   redirect('index.php');
  // }

  if (isset($_POST['password']) && isset($_POST['confirmPassword'])) {
    if ($_POST['password'] === $_POST['confirmPassword']) {
      $password = $_POST['password'];
      $hashed_password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 10));
      if ($stmt = mysqli_prepare($connection, "UPDATE users SET token='', user_password='{$hashed_password}' WHERE user_email = ? ")) {
        mysqli_stmt_bind_param($stmt, "s", $_GET['email']);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) >= 1) {
          redirect('login.php');
        }

        mysqli_stmt_close($stmt);
      }
    }
  }
}

?>

<!-- Navigation -->
<?php include "includes/navigation.php"; ?>

<!-- Page Content -->
<div class="container">
    <div class="form-gap"></div>
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-md-offset-4">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="text-center">
                <h3><i class="fa fa-lock fa-4x"></i></h3>
                <h2 class="text-center">Reset Password</h2>
                <p>You can reset your password here.</p>
                <div class="panel-body">
                  <form id="register-form" role="form" autocomplete="off" class="form" method="post">

                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock color-blue"></i></span>
                        <input id="password" name="password" placeholder="Enter password" class="form-control" type="password">
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-ok color-blue"></i></span>
                        <input id="confirmPassword" name="confirmPassword" placeholder="Confirm password" class="form-control" type="password">
                      </div>
                    </div>

                    <div class="form-group">
                      <input name="resetPassword" class="btn btn-lg btn-primary btn-block" value="Reset Password" type="submit">
                    </div>

                    <input type="hidden" class="hide" name="token" id="token" value="">

                  </form>
                </div><!-- Body-->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  <hr>

  <?php include "includes/footer.php"; ?>

</div> <!-- /.container -->