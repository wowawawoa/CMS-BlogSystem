<?php include "includes/db.php"; ?>
<?php include "includes/header.php"; ?>

<!-- Navigation -->
<?php include "includes/navigation.php"; ?>

<?php

if (!isset($_GET['category'])) {
    redirect("index.php");
}

?>

<!-- Page Content -->
<div class="container">

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">
            <h1 class="page-header">
                <?php
                $post_category_id = escape($_GET['category']);
                $category_query = query("SELECT cat_title FROM categories WHERE cat_id = $post_category_id");
                $row = mysqli_fetch_assoc($category_query);
                $category_title = $row['cat_title'];
                ?>
                All posts for <?php echo $category_title; ?>
            </h1>

            <?php

            if (isset($_GET['category'])) {
                $post_category_id = escape($_GET['category']);
            }

            $per_page = 5;

            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                $find_category_posts = query("SELECT * FROM posts WHERE post_category_id = $post_category_id");
            } else {
                $find_category_posts = query("SELECT * FROM posts WHERE post_category_id = $post_category_id AND post_status = 'published'");
            }

            $count = mysqli_num_rows($find_category_posts);
            $total_page_count = ceil($count / $per_page);

            if ($count === 0 || $count === null) {
                echo "<h1 class='text-center'>No posts available</h1>";
            } else {
                if (isset($_GET['page'])) {
                    $page = intval(escape($_GET['page']));
                    if ($page > $total_page_count || $page < 1) {
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
                    $query = "SELECT * FROM posts WHERE post_category_id = $post_category_id LIMIT $page_1, $per_page";
                } else {
                    $query = "SELECT * FROM posts WHERE post_category_id = $post_category_id AND post_status = 'published' LIMIT $page_1, $per_page";
                }

                $select_all_posts_query = mysqli_query($connection, $query);

                while ($row = mysqli_fetch_assoc($select_all_posts_query)) {
                    $post_id = $row['post_id'];
                    $post_title = $row['post_title'];
                    $post_author = $row['post_author'];
                    $post_date = $row['post_date'];
                    $post_image = $row['post_image'];
                    $post_content = substr(strip_tags($row['post_content']), 0, 500);

            ?>

                    <!-- Blog Post -->
                    <h2>
                        <a href="post.php?p_id=<?php echo $post_id; ?>"><?php echo $post_title; ?></a>
                    </h2>
                    <p class="lead">
                        by <a href="author_posts.php?author=<?php echo $post_author; ?>&page=1"><?php echo $post_author; ?></a>
                    </p>
                    <p>
                        <span class="glyphicon glyphicon-time"></span> <?php echo $post_date; ?>
                    </p>
                    <hr>
                    <a href="post.php?p_id=<?php echo $post_id; ?>">
                        <img class="img-responsive" src="images/<?php echo imagePlaceholder($post_image); ?>" alt="post image">
                    </a>
                    <hr>
                    <p><?php echo $post_content; ?>...</p>
                    <a class="btn btn-primary" href="post.php?p_id=<?php echo $post_id; ?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>

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

        for ($i = 1; $i <= $total_page_count; $i++) {
            if ($i === $page) {
                echo "<li><a class='active_link' href='category.php?category={$post_category_id}&page={$i}'>{$i}</a></li>";
            } else {
                echo "<li><a href='category.php?category={$post_category_id}&page={$i}'>{$i}</a></li>";
            }
        }

        ?>

    </ul>

    <!-- Footer -->
    <?php include "includes/footer.php"; ?>