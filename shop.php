<?php
include 'server/connection.php';
include 'header.php';

// Initialize $page_no to 1
$page_no = 1;

if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    $category = $_POST['category'];

    // Modify your SQL query to search for products based on the search query and category
    if ($category === 'All Categories') {
        $stmt1 = $conn->prepare("SELECT * FROM `products` WHERE product_name LIKE ? OR product_description LIKE ?");
        $stmt1->bind_param("ss", $search_term, $search_term);
    } else {
        $stmt1 = $conn->prepare("SELECT * FROM `products` WHERE (product_name LIKE ? OR product_description LIKE ?) AND (product_category = ?)");
        $stmt1->bind_param("sss", $search_term, $search_term, $category);
    }

    $search_term = "%" . $search_query . "%";
    $stmt1->execute();
    $products = $stmt1->get_result();

    // Calculate the total number of pages based on search results
    $total_records = $products->num_rows;
    $total_records_per_page = 8;
    $total_no_of_pages = ceil($total_records / $total_records_per_page);
} else {
    // 1. determine page number
    // $stmt = $conn->prepare("SELECT * FROM `products`");
    // $stmt->execute();
    // $products = $stmt->get_result();

    // 2. return the number of products
    $stmt2 = $conn->prepare("SELECT COUNT(*) as total_records FROM products");
    $stmt2->execute();
    $stmt2->bind_result($total_records);
    $stmt2->store_result();
    $stmt2->fetch();

    // 3. products per page
    $total_records_per_page = 8;
    $offset = ($page_no - 1) * $total_records_per_page;
    $previous_page = $page_no - 1;
    $next_page = $page_no + 1;
    $adjacents = "2";
    $total_no_of_pages = ceil($total_records / $total_records_per_page);

    // Fetch all products initially
    $stmt3 = $conn->prepare("SELECT * FROM products ORDER BY RAND() LIMIT $offset,$total_records_per_page");
    $stmt3->execute();
    $products = $stmt3->get_result();
}
?>

<div class="shop-page">
    <!-- Shop -->

    <section id="featured" class="my-5 container">
        <div class="container text-center mt-5 py-5">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Search Section -->
                <form class="d-flex" action="shop.php" method="POST">
                    <div class="input-group mb-3 w-100">
                        <input type="text" class="form-control" placeholder="Enter Search" name="search_query"
                            aria-label="Recipient's username" aria-describedby="button-addon2">
                        <select class="form-select" name="category">
                            <option value="All Categories">All Categories</option>
                            <option value="Anklet">Anklet</option>
                            <option value="Bangles">Bangles</option>
                            <option value="Bracelet">Bracelet</option>
                            <option value="Necklace">Necklace</option>
                            <option value="Nosepin">Nosepin</option>
                            <option value="Ring">Ring</option>
                            <option value="Jewellery Set">Jewellery Set</option>
                            <option value="Broaches">Broaches</option>
                        </select>
                        <button class="btn btn-outline-primary" type="submit" name="search">Search</button>
                    </div>
                </form>
            </div>

            <h3>Latest Products</h3>
            <hr class="mx-auto">
            <p>Here You Can Check Out Our Latest products</p>
        </div>
        <div class="row mx-auto container-fluid">

            <?php foreach ($products as $row) { ?>

                <div class="product text-center col-lg-3 col-md-4 col-sm-12">
                    <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image']; ?>" alt="">
                    <div class=" star">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <h5 class="p-name">
                        <?php echo $row['product_name']; ?>
                    </h5>
                    <h4 class="p-price">$
                        <?php echo $row['product_price']; ?>
                    </h4>
                    <a href=<?php echo "single_product.php?product_id=" . $row['product_id']; ?>><button
                            class="button button2">Buy Now</button></a>
                </div>
            <?php } ?>

            <nav aria-label="page navigation example">
                <ul class="pagination mt-5">

                    <li class="page-item <?php if ($page_no <= 1) {
                        echo 'disabled';
                    } ?>">
                        <a class="page-link" href="<?php if ($page_no <= 1) {
                            echo '#';
                        } else {
                            echo "?page_no=" . ($page_no - 1);
                        } ?>">Previous</a>
                    </li>

                    <?php for ($i = 1; $i <= $total_no_of_pages; $i++) { ?>
                        <li class="page-item <?php if ($page_no == $i)
                            echo 'active'; ?>">
                            <a class="page-link" href="?page_no=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php } ?>

                    <li class="page-item <?php if ($page_no >= $total_no_of_pages) {
                        echo 'disabled';
                    } ?>">
                        <a class="page-link" href="<?php if ($page_no >= $total_no_of_pages) {
                            echo '#';
                        } else {
                            echo "?page_no=" . ($page_no + 1);
                        } ?>">Next</a>
                    </li>
                </ul>
            </nav>

        </div>
    </section>
</div>

<?php include 'footer.php' ?>