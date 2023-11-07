<?php

include("delete_modal.php");
include("reset_modal.php");

if (isset($_POST['checkBoxArray'])) {
  foreach ($_POST['checkBoxArray'] as $commentValueId) {
    $bulk_options = escape($_POST['bulk_options']);

    switch ($bulk_options) {
      case 'approved':
        $update_to_approved_status = query("UPDATE comments SET comment_status = '{$bulk_options}' WHERE comment_id = {$commentValueId} ");
        break;

      case 'unapproved':
        $update_to_unapproved_status = query("UPDATE comments SET comment_status = '{$bulk_options}' WHERE comment_id = {$commentValueId} ");
        break;

      case 'delete':
        $update_to_delete_status = query("DELETE FROM comments WHERE comment_id = {$commentValueId} ");
        break;

      default:
        break;
    }
  }
}

?>

<form action="#" method="POST">
  <table class="table table-bordered table-hover">
    <div id="bulkOptionsContainer" class="col-xs-4">
      <select class="form-control" name="bulk_options" id="">
        <option value="">Select Options</option>
        <option value="approved">Approve</option>
        <option value="unapproved">Unapprove</option>
        <option value="delete">Delete</option>
      </select>
    </div>

    <div class="col-xs-4">
      <input type="submit" name="submit" class="btn btn-success" value="Apply">
      <a class="btn btn-primary" href="posts.php?source=add_post">Add New</a>
    </div>

    <thead>
      <tr>
        <th><input type="checkbox" id="selectAllComments"></th>
        <th>Id</th>
        <th>Author</th>
        <th>User</th>
        <th>Comment</th>
        <th>Email</th>
        <th>Status</th>
        <th>In Response to</th>
        <th>Date</th>

        <?php if (is_admin()) : ?>
          <th>Approve</th>
          <th>Unapprove</th>
          <th>Delete</th>
        <?php endif; ?>

      </tr>
    </thead>
    <tbody>

      <?php
      if (is_admin()) {
        $select_comments = query("SELECT * FROM comments ORDER BY comment_id DESC");
      } else {
        $select_comments = query("SELECT * FROM comments WHERE comment_username = '" . get_user_name() . "'" . " ORDER BY comment_id DESC");
      }

      while ($row = mysqli_fetch_assoc($select_comments)) {
        $comment_id = $row['comment_id'];
        $comment_post_id = $row['comment_post_id'];
        $comment_author = $row['comment_author'];
        $comment_username = $row['comment_username'];
        $comment_content = $row['comment_content'];
        $comment_email = $row['comment_email'];
        $comment_status = $row['comment_status'];
        $comment_date = $row['comment_date'];

        echo "<tr>";

      ?>

        <td><input type='checkbox' class='checkBoxes' name='checkBoxArray[]' value='<?php echo $comment_id; ?>'></td>

        <?php

        echo "<td>{$comment_id}</td>";
        echo "<td>{$comment_author}</td>";
        echo "<td>{$comment_username}</td>";
        echo "<td>{$comment_content}</td>";
        echo "<td>{$comment_email}</td>";
        echo "<td>{$comment_status}</td>";

        $select_post_id_query = query("SELECT * FROM posts WHERE post_id = $comment_post_id ");

        if (mysqli_num_rows($select_post_id_query) === 0) {
          echo "<td>Post Deleted</td>";
        } else {
          while ($row = mysqli_fetch_assoc($select_post_id_query)) {
            $post_id = $row['post_id'];
            $post_title = $row['post_title'];

            echo "<td><a href='../post.php?p_id=$post_id'>{$post_title}</a></td>";
          }
        }

        echo "<td>{$comment_date}</td>";

        if (is_admin()) {
          echo "<td><a href='comments.php?approve={$comment_id}'>Approve</a></td>";
          echo "<td><a href='comments.php?unapprove={$comment_id}'>Unapprove</a></td>";

        ?>

          <form action="" method="POST">
            <input type="hidden" name="comment_id" value="<?php echo $comment_id ?>">

            <?php
            echo "<td><input rel='$comment_id' id='comment_delete_btn' class='btn btn-danger' type='submit' name='delete' value='Delete'></td>"
            ?>

          </form>

      <?php

        }

        echo "</tr>";
      }

      ?>

    </tbody>
  </table>
</form>

<?php

if (isset($_GET['approve'])) {
  if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
      $the_comment_id = escape($_GET['approve']);
      $query = "UPDATE comments SET comment_status = 'approved' WHERE comment_id = {$the_comment_id} ";
      $approve_comment_query = mysqli_query($connection, $query);
      redirect("comments.php");
    }
  }
}

if (isset($_GET['unapprove'])) {
  if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
      $the_comment_id = escape($_GET['unapprove']);
      $query = "UPDATE comments SET comment_status = 'unapproved' WHERE comment_id = {$the_comment_id} ";
      $unapprove_comment_query = mysqli_query($connection, $query);
      redirect("comments.php");
    }
  }
}

if (isset($_POST['delete'])) {
  if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
      $the_comment_id = escape($_POST['comment_id']);
      $query = "DELETE FROM comments WHERE comment_id = {$the_comment_id} ";
      $delete_query = mysqli_query($connection, $query);
      redirect("comments.php");
    }
  }
}

?>

<!-- Bootstrap modal -->
<script>
  $(document).ready(function() {
    $("#comment_delete_btn").on('click', function(e) {
      e.preventDefault();
      var id = $(this).attr("rel");
      $(".modal_delete_link").val(id);
      $(".modal-body").html("<h3>Are you sure you want to delete comment " + id + "</h3>");
      $("#deleteModal").modal('show');
    })
  })
</script>