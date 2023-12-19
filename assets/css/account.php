<?php
include ('server/connection.php');
if(isset($POST['order_details_btn']) && isset($POST['order_id']) ){
    $order_id = $POST['order_id'];
    $order_status = $POST['order_status'];
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->bind_Param('i', $order_id);
    $stmt->execute();
    $order_details = $stmt->get_result();
}else{
    header('location:account.php');
    exit;
}
function calculatetotalcart(){
    $total = 0;
    foreach($_SESSION['cart'] as $key => $value){
        $product = $_SESSION['cart'][$key];
        $price = $product['product_price'];
        $quantity = $product['product_quantity'];
        $total = $total + ( $price * $quantity);
    }
    $_SESSION['total'] = $total;
}
?>

<?php include('header.php');?>
<section id="orders" class="orders container my-5 py-3">
    <div class="container mt-5">
        <h2 class="font-weight-bold text-center">Order Details</h2>
        <hr class="mx-auto">
    </div>
    <table class="mt-5 pt-5 max-auto">
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
        </tr>
        <?php while($row=$order_details->fetch_assoc()){ ?>
            <tr>
                <td>
                    <div class="product-info">
                        <img src="assets/imgs/<?php echo $row['product_image'];?>"</div>
                    </div>
                    <div>
                        <p class="mt-3"><?php echo $row['product_name'];?></p>
                    </div>
                </td>
                <td>
                    <span>$<?php echo $row['product_price']; ?></span>
                </td>
                <td>
                    <span>$<?php echo $row['product_quantity']; ?></span>
                </td>
            </tr>
        <?php } ?>
    </table>
</section>