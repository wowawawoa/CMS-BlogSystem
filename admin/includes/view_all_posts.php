<?php

include("delete_modal.php");
include("reset_modal.php");

if (isset($_POST['checkBoxArray'])) {
  foreach ($_POST['checkBoxArray'] as $postValueId) {
    $bulk_options = escape($_POST['bulk_options']);

    switch ($bulk_options) {
      case 'published':
        $query = "UPDATE posts SET post_status = '{$bulk_options}' WHERE post_id = {$postValueId} ";
        $update_to_published_status = mysqli_query($connection, $query);
        confirmQuery($update_to_published_status);
        break;

      case 'draft':
        $query = "UPDATE posts SET post_status = '{$bulk_options}' WHERE post_id = {$postValueId} ";
        $update_to_draft_status = mysqli_query($connection, $query);
        confirmQuery($update_to_draft_status);
        break;

      case 'delete':
        $query = "DELETE FROM posts WHERE post_id = {$postValueId} ";
        $update_to_delete_status = mysqli_query($connection, $query);
        confirmQuery($update_to_delete_status);
        break;

      case 'clone':
        $query = "SELECT * FROM posts WHERE post_id = '{$postValueId}' ";
        $select_post_query = mysqli_query($connection, $query);

        while ($row = mysqli_fetch_array($select_post_query)) {
          $post_title = $row['post_title'];
          $post_category_id = $row['post_category_id'];
          $post_date = $row['post_date'];
          $post_author = $row['post_author'];
          $post_user = $row['post_user'];
          $post_status = $row['post_status'];
          $post_image = $row['post_image'];
          $post_tags = $row['post_tags'];
          $post_content = $row['post_content'];

          if (empty($post_tags)) {
            $post_tags = "No tags";
          }
        }

        $query = "INSERT INTO posts(post_category_id, post_title, post_author, post_date, post_image, post_content, post_tags, post_status, post_user) ";
        $query .= "VALUES({$post_category_id}, '{$post_title}', '{$post_author}', now(), '{$post_image}', '{$post_content}', '{$post_tags}', '{$post_status}', '{$post_user}') ";

        $copy_query = mysqli_query($connection, $query);

        confirmQuery($copy_query);

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
        <option value="published">Publish</option>
        <option value="draft">Draft</option>
        <option value="delete">Delete</option>
        <option value="clone">Clone</option>
      </select>
    </div>

    <div class="col-xs-4">
      <input type="submit" name="submit" class="btn btn-success" value="Apply">
      <a class="btn btn-primary" href="posts.php?source=add_post">Add New</a>
    </div>

    <thead>
      <tr>
        <th><input type="checkbox" id="selectAllBoxes"></th>
        <th>Id</th>
        <th>User</th>
        <th>Title</th>
        <th>Category</th>
        <th>Status</th>
        <th>Image</th>
        <th>Tags</th>
        <th>Comments</th>
        <th>Date</th>
        <th>View Post</th>
        <th>Edit</th>
        <th>Delete</th>
        <th>Views</th>
      </tr>
    </thead>
    <tbody>

      <?php

      // $current_user = $_SESSION['username'];

      // Joining tables
      $query = "SELECT posts.post_id, posts.post_author, posts.post_user, posts.post_title, posts.post_category_id, posts.post_status, posts.post_image, posts.post_tags, posts.post_date, posts.post_views_count, categories.cat_id, categories.cat_title FROM posts LEFT JOIN categories ON posts.post_category_id = categories.cat_id ORDER BY posts.post_id DESC";
      $select_posts = mysqli_query($connection, $query);

      while ($row = mysqli_fetch_assoc($select_posts)) {
        $post_id = $row['post_id'];
        $post_author = $row['post_author'];
        $post_user = $row['post_user'];
        $post_title = $row['post_title'];
        $post_category_id = $row['post_category_id'];
        $post_status = $row['post_status'];
        $post_image = $row['post_image'];        
        $post_tags = $row['post_tags'];
        $post_date = $row['post_date'];
        $post_views_count = $row['post_views_count'];
        $cat_title = $row['cat_title'];
        $cat_id = $row['cat_id'];

        if (empty($post_tags)) {
          $post_tags = "No tags";
        }

        if (empty($post_image)) {
          $post_image = "img_placeholder.jpg";
        }

        echo "<tr>";

      ?>

        <td><input type='checkbox' class='checkBoxes' name='checkBoxArray[]' value='<?php echo $post_id; ?>'></td>

        <?php

        echo "<td>{$post_id}</td>";

        if (isset($post_user) && !empty($post_user)) {
          echo "<td>{$post_user}</td>";
        } else if (isset($post_author) && !empty($post_author)) {
          echo "<td>{$post_author}</td>";
        } else {
          echo "<td>Unknown</td>";
        }

        echo "<td>{$post_title}</td>";

        // $query = "SELECT * FROM categories WHERE cat_id = $post_category_id ";
        // $select_categories_id = mysqli_query($connection, $query);

        // while ($row = mysqli_fetch_assoc($select_categories_id)) {
        //   $cat_id = $row['cat_id'];
        //   $cat_title = $row['cat_title'];

        //   echo "<td>{$cat_title}</td>";
        // }
        echo "<td>{$cat_title}</td>";
        echo "<td>{$post_status}</td>";
        echo "<td><img width='100' src='../images/{$post_image}' alt='post image'></td>";
        echo "<td>{$post_tags}</td>";

        $comment_query = "SELECT * FROM comments WHERE comment_post_id = $post_id";
        $send_comment_query = mysqli_query($connection, $comment_query);
        $count_comments = mysqli_num_rows($send_comment_query);

        echo "<td><a href='post_comments.php?id=$post_id'>$count_comments</a></td>";

        echo "<td>{$post_date}</td>";
        echo "<td><a class='btn btn-primary' href='../post.php?p_id={$post_id}'>View Post</a></td>";
        echo "<td><a class='btn btn-info' href='posts.php?source=edit_post&p_id={$post_id}'>Edit</a></td>";

        ?>

        <form action="" method="POST">
          <input type="hidden" name="post_id" value="<?php echo $post_id ?>">

          <?php
          echo "<td><input rel='$post_id' class='btn btn-danger delete_link' type='submit' name='delete' value='Delete'></td>"
          ?>

        </form>

      <?php
        echo "<td><a rel='$post_id' href='javascript:void(0)' class='reset_link'>{$post_views_count}</a></td>";
        echo "</tr>";
      }

      ?>

    </tbody>
  </table>
</form>

<?php

if (isset($_POST['delete'])) {
  if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
      $the_post_id = escape($_POST['post_id']);
      $query = "DELETE FROM posts WHERE post_id = {$the_post_id} ";
      $delete_query = mysqli_query($connection, $query);
      redirect("posts.php");
    }
  }
}

if (isset($_GET['reset'])) {
  if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
      $the_post_id = escape($_GET['reset']);
      $query = "UPDATE posts SET post_views_count = 0 WHERE post_id =" . mysqli_real_escape_string($connection, $_GET['reset']);
      $reset_query = mysqli_query($connection, $query);
      redirect("posts.php");
    }
  }
}

?>

<!-- Bootstrap modal -->
<script>
  $(document).ready(function() {
    $(".delete_link").on('click', function(e) {
      e.preventDefault();
      var id = $(this).attr("rel");
      $(".modal_delete_link").val(id);
      $(".modal-body").html("<h3>Are you sure you want to delete post " + id + "</h3>");
      $("#deleteModal").modal('show');
    })

    $(".reset_link").on('click', function() {
      var id = $(this).attr("rel");
      var reset_url = "posts.php?reset=" + id + " ";
      $(".modal_reset_link").attr("href", reset_url);
      $("#resetModal").modal('show');
    })
  })
</script>