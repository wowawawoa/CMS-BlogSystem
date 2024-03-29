<?php include "includes/db.php"; ?>
<?php include "includes/header.php"; ?>

<!-- Navigation -->
<?php include "includes/navigation.php"; ?>

<?php

if (isset($_POST['liked'])) {
    $post_id = escape($_POST['post_id']);
    $user_id = escape($_POST['user_id']);

    // fetch post
    $post_query = "SELECT * FROM posts WHERE post_id = $post_id";
    $post_result = mysqli_query($connection, $post_query);
    $post = mysqli_fetch_array($post_result);
    $likes = $post['likes'];

    // update post likes
    $update_likes_query = "UPDATE posts SET likes = $likes + 1 WHERE post_id = $post_id";
    mysqli_query($connection, $update_likes_query);

    // create likes
    $create_likes_query = "INSERT INTO likes(user_id, post_id) VALUES($user_id, $post_id)";
    mysqli_query($connection, $create_likes_query);

    exit();
}

if (isset($_POST['unliked'])) {
    $post_id = escape($_POST['post_id']);
    $user_id = escape($_POST['user_id']);

    // fetch post
    $post_query = "SELECT * FROM posts WHERE post_id = $post_id";
    $post_result = mysqli_query($connection, $post_query);
    $post = mysqli_fetch_array($post_result);
    $likes = $post['likes'];

    // update post unlikes
    $delete_likes_query = "DELETE FROM likes WHERE post_id = $post_id AND user_id = $user_id";
    mysqli_query($connection, $delete_likes_query);

    // update post likes
    $update_likes_query = "UPDATE posts SET likes = $likes - 1 WHERE post_id = $post_id";
    mysqli_query($connection, $update_likes_query);

    exit();
}

?>

<!-- Page Content -->
<div class="container">
    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">

            <?php

            if (isset($_GET['p_id']) && $_GET['p_id']) {
                $the_post_id = escape($_GET['p_id']);
                $message = "";

                $view_query = "UPDATE posts SET post_views_count = post_views_count + 1 ";
                $view_query .= "WHERE post_id = $the_post_id ";
                $update_post_views_count = mysqli_query($connection, $view_query);

                confirmQuery($update_post_views_count);

                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                    $query = "SELECT * FROM posts WHERE post_id = $the_post_id";
                } else {
                    $query = "SELECT * FROM posts WHERE post_id = $the_post_id AND post_status = 'published'";
                }

                $select_all_posts_query = mysqli_query($connection, $query);

                confirmQuery($select_all_posts_query);

                if (mysqli_num_rows($select_all_posts_query) === 0) {
                    redirect("index.php");
                } else {
                    while ($row = mysqli_fetch_assoc($select_all_posts_query)) {
                        $post_title = $row['post_title'];
                        // $post_author = $row['post_author'];
                        $post_author = $row['post_user'];
                        $post_date = $row['post_date'];
                        $post_image = $row['post_image'];
                        $post_content = $row['post_content'];
                    }

            ?>

                    <h1 class="page-header">
                        Posts
                    </h1>

                    <!-- First Blog Post -->
                    <h2>
                        <a href="#"><?php echo $post_title; ?></a>
                    </h2>
                    <p class="lead">
                        by <a href="author_posts.php?author=<?php echo $post_author; ?>&p_id=<?php echo $the_post_id; ?>"><?php echo $post_author; ?></a>
                    </p>
                    <p><span class="glyphicon glyphicon-time"></span> <?php echo $post_date; ?></p>
                    <hr>
                    <img class="img-responsive" src="images/<?php echo imagePlaceholder($post_image); ?>" alt="post image">
                    <hr>
                    <p><?php echo $post_content; ?></p>

                    <hr>

                    <!-- Post Like and Unlike -->
                    <?php

                    if (isLoggedIn()) { ?>
                        <div class="row">
                            <p class="pull-right h4">
                                <a href="" class="<?php echo userLikedThisPost($the_post_id) ? 'unlike' : 'like'; ?>">
                                    <span class="<?php echo userLikedThisPost($the_post_id) ? 'glyphicon glyphicon-thumbs-down' : 'glyphicon glyphicon-thumbs-up'; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo userLikedThisPost($the_post_id) ? 'I liked this before' : 'want to like it?' ?>">
                                        <?php echo userLikedThisPost($the_post_id) ? 'Unlike' : 'Like'; ?>
                                    </span>
                                </a>
                            </p>
                        </div>

                    <?php } else { ?>

                        <div class="row">
                            <p class="pull-right h4">
                                You need to <a href="/cms_blog/login.php">Login</a> to like
                            </p>
                        </div>

                    <?php }

                    ?>

                    <div class="row">
                        <p class="pull-right" id="likesCount">
                            Like: <?php echo getPostLikes($the_post_id); ?>
                        </p>
                    </div>
                    <div class="clearfix"></div>

                <?php
                }

                ?>

                <!-- Blog Comments -->

                <!-- Comments Form -->
                <?php

                if (isset($_POST['create_comment'])) {
                    $the_post_id = $_GET['p_id'];
                    $comment_author = escape($_POST['comment_author']);
                    $comment_email = escape($_POST['comment_email']);
                    $comment_content = escape($_POST['comment_content']);

                    if (isLoggedIn()) {
                        $comment_username = $_SESSION['username'];
                    } else {
                        $comment_username = "";
                    }

                    if (!empty($comment_author) && !empty($comment_email) && !empty($comment_content)) {
                        $query = "INSERT INTO comments (comment_post_id, comment_author, comment_username, comment_email, comment_content, comment_status, comment_date) ";
                        $query .= "VALUES ($the_post_id, '{$comment_author}', '{$comment_username}', '{$comment_email}', '{$comment_content}', 'unapproved', now()) ";
                        $create_comment_query = mysqli_query($connection, $query);

                        confirmQuery($create_comment_query);

                        redirect("post.php?p_id=$the_post_id");
                    } else {
                        // echo "<script>alert('Fields cannot be empty')</script>";
                        $message = "Fields cannot be empty";
                        echo "<script>if ( window.history.replaceState ) { window.history.replaceState( null, null, window.location.href );}</script>";
                    }
                }

                ?>

                <?php if (isLoggedIn()) : ?>
                    <div class="well">
                        <h4>Leave a Comment:</h4>
                        <p class="text-danger"><?php echo $message; ?></p>
                        <form role="form" action="#" method="POST">
                            <div class="form-group">
                                <label for="Author">Author</label>
                                <input type="text" required class="form-control" name="comment_author">
                            </div>

                            <div class="form-group">
                                <label for="Email">Email</label>
                                <input type="email" required class="form-control" name="comment_email">
                            </div>

                            <div class="form-group">
                                <label for="comment">Your Comment</label>
                                <textarea class="form-control" required name="comment_content" rows="3"></textarea>
                            </div>

                            <button type="submit" name="create_comment" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                <?php else : ?>
                    <div class="well">
                        <h4 class="text-center">You need to <a href="/cms_blog/login.php">Login</a> to comment.</h4>
                    </div>
                <?php endif; ?>

                <hr>

                <!-- Posted Comments -->

                <!-- Comment -->
                <?php

                $query = "SELECT * FROM comments WHERE comment_post_id = {$the_post_id} ";
                $query .= "AND comment_status = 'approved' ";
                $query .= "ORDER BY comment_id DESC ";
                $select_comment_query = mysqli_query($connection, $query);

                confirmQuery($select_comment_query);

                while ($row = mysqli_fetch_array($select_comment_query)) {
                    $comment_date = $row['comment_date'];
                    $comment_content = $row['comment_content'];
                    $comment_author = $row['comment_author'];

                ?>

                    <div class="media">
                        <a class="pull-left" href="#">
                            <img class="media-object" src="http://placehold.it/64x64" alt="">
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading"><?php echo $comment_author; ?>
                                <small><?php $comment_date; ?></small>
                            </h4>
                            <?php echo $comment_content; ?>
                        </div>
                    </div>

            <?php }
            } else {
                redirect("index.php");
            }

            ?>

        </div>

        <!-- Blog Sidebar Widgets Column -->
        <?php include "includes/sidebar.php"; ?>

    </div>
    <!-- /.row -->

    <hr>

    <!-- Footer -->
    <?php include "includes/footer.php"; ?>

    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
            var post_id = <?php echo $the_post_id; ?>;
            var user_id = <?php echo loggedInUserId(); ?>

            $('.like').click(function(e) {
                $.ajax({
                    url: "post.php?p_id=<?php echo $the_post_id; ?>",
                    type: 'post',
                    data: {
                        'liked': 1,
                        'post_id': post_id,
                        'user_id': user_id
                    },
                });
            });

            $('.unlike').click(function(e) {
                $.ajax({
                    url: "post.php?p_id=<?php echo $the_post_id; ?>",
                    type: 'post',
                    data: {
                        'unliked': 1,
                        'post_id': post_id,
                        'user_id': user_id
                    },
                });
            });
        })
    </script>