<?php

include("delete_modal.php");
include("reset_modal.php");

if (isset($_POST['checkBoxArray'])) {
  foreach ($_POST['checkBoxArray'] as $postValueId) {
    $bulk_options = escape($_POST['bulk_options']);

    switch ($bulk_options) {
      case 'published':
        $update_to_published_status = query("UPDATE posts SET post_status = '{$bulk_options}' WHERE post_id = {$postValueId} ");
        break;

      case 'draft':
        $update_to_draft_status = query("UPDATE posts SET post_status = '{$bulk_options}' WHERE post_id = {$postValueId} ");
        break;

      case 'delete':
        $update_to_delete_status = query("DELETE FROM posts WHERE post_id = {$postValueId} ");
        break;

      case 'clone':
        $clone_select_posts_query = query("SELECT * FROM posts WHERE post_id = '{$postValueId}' ");

        while ($row = mysqli_fetch_array($clone_select_posts_query)) {
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
            $post_tags = "";
          }
        }

        $clone_query = query(
          "INSERT INTO posts(post_category_id, post_title, post_author, post_date, post_image, post_content, post_tags, post_status, post_user) " .
            "VALUES({$post_category_id}, '{$post_title}', '{$post_author}', now(), '{$post_image}', '{$post_content}', '{$post_tags}', '{$post_status}', '{$post_user}') "
        );
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
        <th><input type="checkbox" id="selectAllPosts"></th>
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

      $per_page = 10;

      // Joining tables
      if (is_admin()) {
        $select_posts_query = "SELECT posts.post_id, posts.post_author, posts.post_user, posts.post_title, posts.post_category_id, posts.post_status, posts.post_image, posts.post_tags, posts.post_date, posts.post_views_count, categories.cat_id, categories.cat_title FROM posts LEFT JOIN categories ON posts.post_category_id = categories.cat_id ORDER BY posts.post_id DESC";
        $select_posts = mysqli_query($connection, $select_posts_query);
      } else {
        $select_posts_query = "SELECT posts.post_id, posts.post_author, posts.post_user, posts.post_title, posts.post_category_id, posts.post_status, posts.post_image, posts.post_tags, posts.post_date, posts.post_views_count, categories.cat_id, categories.cat_title FROM posts LEFT JOIN categories ON posts.post_category_id = categories.cat_id WHERE posts.post_user = '" . get_user_name() . "'" . " ORDER BY posts.post_id DESC";
        $select_posts = mysqli_query($connection, $select_posts_query);
      }

      $count = mysqli_num_rows($select_posts);
      $total_page_count = ceil($count / $per_page);

      if (isset($_GET['page'])) {
        $page = intval(escape($_GET['page']));
        if ($page > $total_page_count || $page < 1) {
          redirect("posts.php?page=1");
        }
      } else {
        $page = 1;
      }

      // post starting id on page
      if ($page === "" || $page === 1) {
        $page_1 = 0;
      } else {
        $page_1 = ($page * $per_page) - $per_page;
      }

      $limit_select_posts_query = $select_posts_query . " LIMIT $page_1, $per_page";
      $limit_select_posts = mysqli_query($connection, $limit_select_posts_query);

      while ($row = mysqli_fetch_assoc($limit_select_posts)) {
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
          $post_tags = "";
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
          echo "<td><input rel='$post_id' id='post_delete_btn' class='btn btn-danger' type='submit' name='delete' value='Delete'></td>"
          ?>

        </form>

      <?php
        echo "<td><a rel='$post_id' id='post_view_reset_btn' href='javascript:void(0)'>{$post_views_count}</a></td>";
        echo "</tr>";
      }

      ?>

    </tbody>
  </table>
</form>

<hr>

<!-- Pagination -->
<ul class="pager">

  <?php

  for ($i = 1; $i <= $total_page_count; $i++) {
    if ($i === $page) {
      echo "<li><a class='active_link' href='posts.php?page={$i}'>{$i}</a></li>";
    } else {
      echo "<li><a href='posts.php?page={$i}'>{$i}</a></li>";
    }
  }

  ?>

</ul>

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
    $("#post_delete_btn").on('click', function(e) {
      e.preventDefault();
      var id = $(this).attr("rel");
      $(".modal_delete_link").val(id);
      $(".modal-body").html("<h3>Are you sure you want to delete post " + id + "</h3>");
      $("#deleteModal").modal('show');
    })

    $("#post_view_reset_btn").on('click', function() {
      var id = $(this).attr("rel");
      var reset_url = "posts.php?reset=" + id + " ";
      $(".modal_reset_link").attr("href", reset_url);
      $("#resetModal").modal('show');
    })
  })
</script>