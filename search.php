<?php include "includes/db.php"; ?>
<?php include "includes/header.php"; ?>

<!-- Navigation -->
<?php include "includes/navigation.php"; ?>

<?php

if (!isset($_GET['search'])) {
    redirect("index.php");
}

?>

<!-- Page Content -->
<div class="container">

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">
            <h1 class="page-header">
                Search Result for <?php echo escape($_GET['search']); ?>
            </h1>

            <?php

            if (isset($_GET['search'])) {

                $per_page = 5;

                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                    $stmt = mysqli_prepare($connection, "SELECT * FROM posts WHERE post_tags LIKE ?");
                } else {
                    $stmt = mysqli_prepare($connection, "SELECT * FROM posts WHERE post_status = 'published' AND post_tags LIKE ?");
                }

                $search = escape($_GET['search']);
                if ($stmt) {
                    $searchParam = "%" . $search . "%";
                    mysqli_stmt_bind_param($stmt, "s", $searchParam);
                    mysqli_stmt_execute($stmt);
                    $search_query = mysqli_stmt_get_result($stmt);
                    confirmQuery($search_query);
                    mysqli_stmt_close($stmt);
                }

                $count = mysqli_num_rows($search_query);
                $total_page_count = ceil($count / $per_page);

                if ($count === 0 || $count === null) {
                    echo "<h1>NO RESULT</h1>";
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
                        $limited_stmt = mysqli_prepare($connection, "SELECT * FROM posts WHERE post_tags LIKE ? LIMIT $page_1, $per_page");
                    } else {
                        $limited_stmt = mysqli_prepare($connection, "SELECT * FROM posts WHERE post_status = 'published' AND post_tags LIKE ? LIMIT $page_1, $per_page");
                    }

                    if ($limited_stmt) {
                        $search = escape($_GET['search']);
                        $searchParam = "%" . $search . "%";
                        mysqli_stmt_bind_param($limited_stmt, "s", $searchParam);
                        mysqli_stmt_execute($limited_stmt);
                        $limited_search_query = mysqli_stmt_get_result($limited_stmt);
                        confirmQuery($limited_search_query);
                        mysqli_stmt_close($limited_stmt);
                    }

                    while ($row = mysqli_fetch_assoc($limited_search_query)) {
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
                echo "<li><a class='active_link' href='search.php?search={$search}&page={$i}'>{$i}</a></li>";
            } else {
                echo "<li><a href='search.php?search={$search}&page={$i}'>{$i}</a></li>";
            }
        }

        ?>

    </ul>

    <!-- Footer -->
    <?php include "includes/footer.php"; ?>