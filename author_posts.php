<?php include "includes/db.php"; ?>
<?php include "includes/header.php"; ?>

<!-- Navigation -->
<?php include "includes/navigation.php"; ?>

<!-- Page Content -->
<div class="container">

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">

            <h1 class="page-header">
                All posts by <?php echo escape($_GET['author']); ?>
            </h1>

            <?php

            $per_page = 5;

            if (isset($_GET['author'])) {
                $the_post_user = escape($_GET['author']);
            }

            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                $select_all_posts_query = query("SELECT * FROM posts WHERE post_user = '{$the_post_user}'");
            } else {
                $select_all_posts_query = query("SELECT * FROM posts WHERE post_user = '{$the_post_user}' AND post_status = 'published'");
            }

            $count = mysqli_num_rows($select_all_posts_query);

            $count = ceil($count / $per_page);

            if (isset($_GET['page'])) {
                $page = intval(escape($_GET['page']));
                if ($page > $count || $page < 1) {
                    redirect("index.php?page=1");
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

            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                $limited_all_posts_query = query("SELECT * FROM posts WHERE post_user = '{$the_post_user}' LIMIT $page_1, $per_page");
            } else {
                $limited_all_posts_query = query("SELECT * FROM posts WHERE post_user = '{$the_post_user}' AND post_status = 'published' LIMIT $page_1, $per_page");
            }

            if (mysqli_num_rows($limited_all_posts_query) === 0) {
                redirect("index.php");
            } else {
                while ($row = mysqli_fetch_assoc($limited_all_posts_query)) {
                    $post_title = $row['post_title'];
                    $post_user = $row['post_user'];
                    $post_date = $row['post_date'];
                    $post_image = $row['post_image'];
                    $post_content = $row['post_content'];
                    $post_id = $row['post_id'];

            ?>

                    <!-- First Blog Post -->
                    <h2>
                        <a href="post.php?p_id=<?php echo $post_id; ?>"><?php echo $post_title; ?></a>
                    </h2>
                    <p class="lead">
                        All posts by <?php echo $post_user; ?>
                    </p>
                    <p><span class="glyphicon glyphicon-time"></span> <?php echo $post_date; ?></p>
                    <hr>
                    <img class="img-responsive" src="images/<?php echo $post_image; ?>" alt="post image">
                    <hr>
                    <p><?php echo $post_content; ?></p>

                    <hr>

            <?php
                }
            }

            ?>

        </div>

        <!-- Blog Sidebar Widgets Column -->
        <?php include "includes/sidebar.php"; ?>

    </div>
    <!-- /.row -->

    <hr>

    <!-- Pagination -->
    <ul class="pager">

        <?php

        for ($i = 1; $i <= $count; $i++) {
            if ($i === $page) {
                echo "<li><a class='active_link' href='author_posts.php?author={$post_user}&page={$i}'>{$i}</a></li>";
            } else {
                echo "<li><a href='author_posts.php?author={$post_user}&page={$i}'>{$i}</a></li>";
            }
        }

        ?>

    </ul>

    <!-- Footer -->
    <?php include "includes/footer.php"; ?>