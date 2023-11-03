<?php include "includes/db.php"; ?>
<?php include "includes/header.php"; ?>

<?php

if (isset($_POST['submit'])) {
  $to = "tonglin0608@gmail.com";
  $subject = escape(wordwrap($_POST['subject']), 70);
  $body = escape(wordwrap($_POST['body']), 70);
  $header = "From:" .escape($_POST['email']);

  if (!empty($subject) && !empty($body) && !empty($header)) {
    mail("tonglin0608@gmail.com", $subject, $body, $header);
    $message = "Your message has been sent";
  } else {
    $message = "Fields cannot be empty";
  }
} else {
  $message = "";
}

?>

<!-- Navigation -->
<?php include "includes/navigation.php"; ?>

<!-- Page Content -->
<div class="container">
  <section id="login">
    <div class="container">
      <div class="row">
        <div class="col-xs-6 col-xs-offset-3">
          <div class="form-wrap">
            <h1>Contact</h1>
            <form role="form" action="" method="post" id="contact-form" autocomplete="off">
              <h6 class="text-center"><?php echo $message; ?></h6>
              <div class="form-group">
                <label for="email" class="sr-only">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter you Email" required>
              </div>
              <div class="form-group">
                <label for="subject" class="sr-only">Subject</label>
                <input type="text" name="subject" id="subject" class="form-control" placeholder="Enter your subject" required>
              </div>
              <div class="form-group">
                <label for="body" class="sr-only">Body</label>
                <textarea class="form-control" name="body" id="body" cols="30" rows="10" required></textarea>
              </div>
              <input type="submit" name="submit" id="btn-login" class="btn btn-custom btn-lg btn-block" value="Submit">
            </form>
          </div>
        </div> <!-- /.col-xs-12 -->
      </div> <!-- /.row -->
    </div> <!-- /.container -->
  </section>

  <hr>

  <?php include "includes/footer.php"; ?>