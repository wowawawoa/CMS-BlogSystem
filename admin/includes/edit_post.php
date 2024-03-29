<?php

if (isset($_GET['p_id'])) {
  $the_post_id = escape($_GET['p_id']);
}

$query = "SELECT * FROM posts WHERE post_id = $the_post_id ";
$select_posts_by_id = mysqli_query($connection, $query);

while ($row = mysqli_fetch_assoc($select_posts_by_id)) {
  $post_id = $row['post_id'];
  $post_user = $row['post_user'];
  $post_title = $row['post_title'];
  $post_category_id = $row['post_category_id'];
  $post_status = $row['post_status'];

  $post_image = $row['post_image'];
  if (empty($post_image)) {
    $post_image = 'img_placeholder.jpg';
  } else {
    $post_image = $row['post_image'];
  }

  $post_tags = $row['post_tags'];
  $post_date = $row['post_date'];
  $post_content = $row['post_content'];
}

if (isset($_POST['update_post'])) {
  $post_user = escape($_POST['post_user']);
  $post_title = escape($_POST['post_title']);
  $post_category_id = escape($_POST['post_category']);
  $post_status = escape($_POST['status']);
  $post_image = $_FILES['image']['name'];
  $post_image_temp = $_FILES['image']['tmp_name'];
  $post_tags = escape($_POST['tags']);
  $post_content = escape($_POST['content']);

  move_uploaded_file($post_image_temp, "../images/$post_image");

  if (empty($post_image)) {
    $query = "SELECT * FROM posts WHERE post_id = $the_post_id ";
    $select_image = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_array($select_image)) {
      $post_image = $row['post_image'];
    }
  }

  $query = "UPDATE posts SET ";
  $query .= "post_title = '{$post_title}', ";
  $query .= "post_category_id = '{$post_category_id}', ";
  $query .= "post_date = now(), ";
  $query .= "post_user = '{$post_user}', ";
  $query .= "post_status = '{$post_status}', ";
  $query .= "post_tags = '{$post_tags}', ";
  $query .= "post_content = '{$post_content}', ";
  $query .= "post_image = '{$post_image}' ";
  $query .= "WHERE post_id = {$the_post_id} ";

  $update_post = mysqli_query($connection, $query);

  confirmQuery($update_post);

  echo "<p class='bg-success'>Post Updated. <a href='../post.php?p_id={$the_post_id}'>View Post</a> or <a href='posts.php'>Edit More Posts</a></p>";
}

?>

<form action="" method="post" enctype="multipart/form-data">
  <div class="form-group">
    <label for="title">Post Title</label>
    <input type="text" value="<?php echo $post_title; ?>" class="form-control" name="post_title">
  </div>

  <div class="form-group">
    <label for="category">Category</label>
    <select name="post_category" id="">

      <?php

      if (isset($_GET['edit_post'])) {
        $the_post_id = escape($_GET['edit_post']);
      }

      $select_categories = query("SELECT * FROM categories");

      while ($row = mysqli_fetch_assoc($select_categories)) {
        $cat_id = $row['cat_id'];
        $cat_title = $row['cat_title'];

        if ($cat_id === $post_category_id) {
          echo "<option selected value='{$cat_id}'>{$cat_title}</option>";
        } else {
          echo "<option value='{$cat_id}'>{$cat_title}</option>";
        }
      }

      ?>

    </select>
  </div>

  <div class="form-group">
    <label for="users">Users</label>
    <select name="post_user" id="">

      <?php

      if (is_admin()) {
        $select_users = query("SELECT * FROM users");

        while ($row = mysqli_fetch_assoc($select_users)) {
          $user_id = $row['user_id'];
          $username = $row['username'];

          if ($post_user === $username) {
            echo "<option selected value='{$username}'>{$username}</option>";
          } else {
            echo "<option value='{$username}'>{$username}</option>";
          }
        }
      }

      ?>

    </select>
  </div>

  <div class="form-group">
    <select name="status" id="">
      <option value="<?php echo $post_status; ?>"><?php echo $post_status; ?></option>

      <?php

      if ($post_status === 'published') {
        echo "<option value='draft'>draft</option>";
      } else {
        echo "<option value='published'>published</option>";
      }

      ?>

    </select>
  </div>

  <div class="form-group">
    <img width="100" id="edit_post_image" src="../images/<?php echo $post_image; ?>" alt="post image">
    <input type="file" name="image" onchange="editPostImage(this);">
  </div>

  <div class="form-group">
    <label for="tags">Post Tags</label>
    <input type="text" value="<?php echo $post_tags; ?>" class="form-control" name="tags">
  </div>

  <div class="form-group">
    <label for="summernote">Post Content</label>
    <textarea class="form-control" name="content" id="summernote" cols="30" rows="10"><?php echo $post_content; ?></textarea>
  </div>

  <div class="form-group">
    <input type="submit" class="btn btn-primary" name="update_post" value="Update Post">
  </div>
</form>