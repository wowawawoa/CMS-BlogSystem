<?php

if (isset($_POST['create_post'])) {
  $post_title = escape($_POST['title']);
  $post_user = escape($_POST['post_user']);
  $post_category_id = escape($_POST['post_category']);
  $post_status = escape($_POST['status']);

  $post_image = $_FILES['post_image']['name'];
  $post_image_temp = $_FILES['post_image']['tmp_name'];

  move_uploaded_file($post_image_temp, "../images/$post_image");

  $post_tags = escape($_POST['tags']);
  $post_content = escape($_POST['content']);
  $post_date = escape(date('d-m-y'));

  $query = "INSERT INTO posts(post_category_id, post_title, post_user, post_date, post_image, post_content, post_tags, post_status) ";
  $query .= "VALUES({$post_category_id}, '{$post_title}', '{$post_user}', now(), '{$post_image}', '{$post_content}', '{$post_tags}', '{$post_status}') ";
  $create_post_query = mysqli_query($connection, $query);

  confirmQuery($create_post_query);

  $the_post_id = mysqli_insert_id($connection);

  echo "<p class='bg-success'>Post Create. <a href='../post.php?p_id={$the_post_id}'>View Post</a> or <a href='posts.php'>Add More Posts</a></p>";
}

?>

<form action="" method="post" enctype="multipart/form-data">
  <div class="form-group">
    <label for="title">Post Title</label>
    <input type="text" class="form-control" name="title">
  </div>

  <div class="form-group">
    <label for="category">Category</label>
    <select name="post_category" id="">

      <?php

      $query = "SELECT * FROM categories";
      $select_categories = mysqli_query($connection, $query);

      confirmQuery($select_categories);

      while ($row = mysqli_fetch_assoc($select_categories)) {
        $cat_id = $row['cat_id'];
        $cat_title = $row['cat_title'];

        echo "<option value='{$cat_id}'>{$cat_title}</option>";
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

          echo "<option value='{$username}'>{$username}</option>";
        }
      } else {
        echo "<option value='{$_SESSION['username']}'>{$_SESSION['username']}</option>";
      }

      ?>

    </select>
  </div>

  <div class="form-group">
    <label for="status">Post Status</label>
    <select name="status" id="">
      <option value="draft">Draft</option>
      <option value="published">Published</option>
    </select>
  </div>

  <div class="form-group">
    <label for="image" style="display: block;">Post Image</label>
    <img width="100" id="add_post_image" src="" alt="post image" style="display: none;">
    <input type="file" name="post_image" onchange="addPostImage(this);">
  </div>

  <div class="form-group">
    <label for="tags">Post Tags</label>
    <input type="text" class="form-control" name="tags">
  </div>

  <div class="form-group">
    <label for="summernote">Post Content</label>
    <textarea class="form-control" name="content" id="summernote" cols="30" rows="10"></textarea>
  </div>

  <div class="form-group">
    <input type="submit" class="btn btn-primary" name="create_post" value="Publish Post">
  </div>
</form>