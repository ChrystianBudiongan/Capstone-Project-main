<?php
session_start(); // Starting the session

require 'Include/db.handler.inc.php';
$basePath = '../Admin/html/Include/';

if (isset($_SESSION['alert_message'])) {
  echo '<div id="alert-message" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-sm">
                  <div class="modal-content modal-filled bg-success">
                      <div class="modal-body p-4">
                          <div class="text-center">
                              <h4 class="mt-2 text-white">'. $_SESSION['alert'] .'</h4>
                              <p class="mt-3 text-white">' . $_SESSION['alert_message'] . '</p>
                              <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Continue</button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>';
  echo '<script>
          document.addEventListener("DOMContentLoaded", function() {
              var myModal = new bootstrap.Modal(document.getElementById("alert-message"));
              myModal.show();
          setTimeout(function() {
                      myModal.hide();
                  }, 6000); //
              });
          </script>';
  unset($_SESSION['alert_message']); // Clear the message from the session
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Product Details</title>
    <link rel="icon" type="image/png" sizes="16x16" href="assets/H_Logo.png">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" />
    <!-- Bootstrap 5.3.0 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="css/style.css" />
    <!--Fonts Work Sans-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
</head>
<body>
<!--Main Navigation-->
<header>
  <!-- Icon Container -->
  <div class="p-3 text-center bg-white border-bottom">
    <div class="container">
      <div class="row gy-3">
        <!-- Left elements -->
        <div class="col-lg-2 col-sm-4 col-4">
          <a href="index.php" class="float-start">
            <img src="assets/H.png" height="35" />
          </a>
        </div>
        <!-- Left elements -->

        <!-- Center elements -->
            <div class="order-lg-last col-lg-5 col-sm-8 col-8">
              <div class="d-flex justify-content-end">
                <?php
                  // Check if the user is logged in
                    if(isset($_SESSION['user'])) {
                        // User is logged in
                        $loggedIn = true;
                        $username = $_SESSION['user'];
                        $addToCartLink = "shopping cart.php";
                    } else {
                        // User is not logged in
                        $loggedIn = false;
                        $addToCartLink = "SignIn.php";
                    }
                  if ($loggedIn) {
                    // If logged in, display the "Account" option
                    echo '

                          <div class="me-1 py-1 px-3 d-flex align-items-center">
                            <p class="d-none d-md-block mb-0 fw-bold">Welcome, ' . $username . '</p>
                          </div>
                          <a href="account.php" class="me-1 border rounded py-1 px-3 nav-link d-flex align-items-center">
                            <i class="fas fa-user-alt m-1 me-md-2"></i>
                            <p class="d-none d-md-block mb-0 ">Account</p>
                          </a>
                          <a href="shopping cart.php" class="border rounded py-1 px-3 nav-link d-flex align-items-center">
                            <i class="fas fa-shopping-cart m-1 me-md-2"></i>
                            <p class="d-none d-md-block mb-0">My cart</p>
                          </a>';
                  } else {
                    // If not logged in, display the "Sign In" option
                    echo '<a href="SignIn.php" class="me-1 border rounded py-1 px-3 nav-link d-flex align-items-center">
                            <i class="fas fa-user-alt m-1 me-md-2"></i>
                            <p class="d-none d-md-block mb-0 ">Sign in</p>
                          </a>
                          ';
                  }?>
              </div>
            </div>
        <!-- Center elements -->

        <!-- Right elements -->
          <div class="col-lg-5 col-md-12 col-12">
              <div class="input-group justify-content-center">
                  <!-- Form wrapper -->
                  <form action="Include/search.php" method="POST" class="d-flex w-100">
                      <!-- Search input -->
                      <input type="search" name="query" id="form1" class="form-control" placeholder="Search" aria-label="Search" />
                      <!-- Button -->
                      <div class="input-group-append">
                          <button type="submit" class="btn btn-success shadow-none">
                              <i class="fas fa-search"></i>
                          </button>
                      </div>
                  </form>
              </div>
          </div>
        <!-- Right elements -->
      </div>
    </div>
  </div>

<!--Get item name-->
<?php
$product_id = $_GET['id'];

// Retrieve product details from the database
// Note: Needed to edit this to have more functions
$sql_product_details = "SELECT p.productid, p.Name, p.Price, p.Description, pt.TypeName, ps.SizeName, p.product_image, i.quantity
                        FROM tbl_product p
                        INNER JOIN tbl_product_type pt ON pt.TypeID = p.TypeID
                        INNER JOIN tbl_product_size ps ON ps.SizeID = p.SizeID
                        INNER JOIN tbl_inventory i ON i.ProductID = p.ProductID
                        WHERE p.ProductID = :id";
try {
    $stmt_product_details = $pdo->prepare($sql_product_details);
    $stmt_product_details->bindValue(':id', $product_id);
    $stmt_product_details->execute();
    $productDetails = $stmt_product_details->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Catch any SQL errors
    header("location: ../product-list-edit.php?error=sqlerror");
    exit();
}
?>

<!-- Heading -->
  <div class="nav-bg">
    <div class="container py-4">
      <h3 class="text-white mt-2">Data</h3>
      <!-- Breadcrumb -->
      <nav class="d-flex mb-2">
        <h6 class="mb-0">
          <a href="index.php" class="text-white-50">Home</a>
          <span class="text-white-50 mx-2"> > </span>
          <a href="product.php" class="text-white-50">Products</a>
          <span class="text-white-50 mx-2"> > </span>
          <a href="product_detail.php" class="text-white"><u><?php echo $productDetails['Name']; ?></u></a>
        </h6>
      </nav>
      <!-- Breadcrumb -->
    </div>
  </div>
</header>
<!-- Heading -->

<!-- content -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="row gx-5 bg-white p-4 rounded-3 shadow-sm">
      <!-- Product Image -->
      <aside class="col-lg-6 mb-4 mb-lg-0">
        <div class="d-flex justify-content-center">
          <img src="<?php echo $basePath . $productDetails['product_image']; ?>" alt="Product Image" class="img-fluid rounded-4" style="max-width: 100%; height: auto;" />
        </div>
      </aside>
      
      <!-- Product Details -->
      <main class="col-lg-6">
        <div class="ps-lg-4">
          <h4 class="title text-dark mb-3">
            <?php echo $productDetails['Name']; ?>
          </h4>

          <!-- Ratings -->
          <div class="d-flex align-items-center mb-3">  
            <div class="text-warning me-2">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fas fa-star-half-alt"></i>
            </div>
            <span class="text-muted">(4.5)</span>
          </div>

          <!-- Price and Stock -->
          <div class="mb-3">
            <span class="h4 d-block">₱ <?php echo $productDetails['Price']; ?></span>
            <small class="text-muted"><i class="fas fa-shopping-basket fa-sm me-1"></i> <?php echo $productDetails['quantity']; ?> in stock</small>
          </div>

          <!-- Description -->
          <p class="mb-3"><?php echo $productDetails['Description']; ?></p>

          <!-- Product Details -->
          <div class="mb-3">
            <dl class="row">
              <dt class="col-sm-4">Type:</dt>
              <dd class="col-sm-8"><?php echo $productDetails['TypeName']; ?></dd>

              <dt class="col-sm-4">Size:</dt>
              <dd class="col-sm-8"><?php echo $productDetails['SizeName']; ?></dd>
            </dl>
          </div>

          <!-- Quantity -->
          <div class="mb-3 mb-md-0 me-md-3">
              <label class="form-label mb-2">Quantity</label>
              <div class="input-group" style="width: 170px;">
                <button class="btn btn-outline-secondary" type="button" id="decrementButton">
                  <i class="fas fa-minus"></i>
                </button>
                <input type="text" id="quantityInput" class="form-control text-center border border-secondary" value="1" min="1"/>
                <button id="incrementButton" class="btn btn-outline-secondary" type="button">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
          </div>

          <hr />
          

          <!-- Quantity and Actions -->
          <div class="d-flex flex-column flex-md-row align-items-center mb-4">
            
            <!-- Add to Cart and Save Buttons -->
            <div class="d-flex gap-2">
              <form action="./Include/add_cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $productDetails['productid']; ?>">
                <input type="hidden" name="quantity" id="quantityValue" value="1">
                <button type="submit" class="btn btn-primary py-2 px-3 shadow-0">
                  <i class="me-1 fa fa-shopping-basket"></i> Add to cart
                </button>
              </form>

              <a href="#" class="btn btn-light border border-secondary py-2 px-3">
                <i class="me-1 fa fa-heart fa-lg"></i> Save
              </a>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</section>
<!-- content -->


<!--Lower side of the product details i.e comments, specifications-->
<section class="bg-light border-top py-4">
  <div class="container">
    <div class="row gx-4">
      <div class="col-lg-8 mb-4">
        <div class="border rounded-2 px-3 py-2 bg-white">
          <!-- Pills navs -->
          <ul class="nav nav-pills nav-justified mb-3" role="tablist">
            <li class="nav-item d-flex">
              <a class="nav-link d-flex align-items-center justify-content-center w-100 active" id="ex1-tab-1" data-bs-toggle="pill" href="#ex1-pills-1">Specification</a>
            </li>
            <li class="nav-item d-flex">
              <a class="nav-link d-flex align-items-center justify-content-center w-100" id="ex1-tab-2" data-bs-toggle="pill" href="#ex1-pills-2">Comments</a>
            </li>
          </ul>

          <div class="tab-content" id="ex1-content">
            <div class="container tab-pane fade show active" id="ex1-pills-1">
              <p>
                <?php echo $productDetails['Description']; ?>
              </p>
            </div>  
            <div class="container tab-pane fade" id="ex1-pills-2">
              <div class="d-flex mb-2">
                <div class="d-flex align-items-center">
                  <img src="https://via.placeholder.com/40x40" alt="avatar" class="rounded-circle me-2">
                  <div class="text-end">
                    <strong>Author</strong>
                    <span class="small text-muted">January 23, 2021</span>
                  </div>
                </div>
              </div>
              <p>
                Comment text goes here. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
                ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
              </p>
              <div class="d-flex mb-2">
                <div class="d-flex align-items-center">
                  <img src="https://via.placeholder.com/40x40" alt="avatar" class="rounded-circle me-2">
                  <div class="text-end">
                    <strong>Author</strong>
                    <span class="small text-muted">January 24, 2021</span>
                  </div>
                </div>
              </div>
              <p>
                Another comment text goes here. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
                ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
              </p>
            </div>
          </div>
        <!-- Pills content -->
        </div>
      </div>
    </div>
  </div>
</section>
<!--End Lower side of the product details i.e comments, specifications-->

<!-- Footer -->
<footer class="bg-light text-muted">
  <div class="container pt-4">
    <div class="row">
      <div class="col-lg-3 col-md-3">
        <a href="index.php">
          <img src="assets/H.png" height="35" class="mb-3" alt="Logo">
        </a>
        <p class="text-dark" style="font-size: 14px; margin-left: 5px;"> MacArthur Highway, Sto.Domingo 1st Capas, Tarlac</p>
      </div>

      <div class="col-6 col-sm-4 col-lg-2">
        <h6 class="text-uppercase fw-bold mb-3">Store</h6>
        <ul class="list-unstyled mb-4">
          <li><a href="product.php" class="text-muted text-decoration-none text-muted-hover">Products</a></li>
          <li><a href="dentalproduct.php" class="text-muted text-decoration-none text-muted-hover">Dental Product</a></li>
          <li><a href="medicalproduct.php" class="text-muted text-decoration-none text-muted-hover">Medical Product</a></li>
        </ul>
      </div>

      <div class="col-6 col-sm-4 col-lg-2">
        <h6 class="text-uppercase fw-bold mb-3">Information</h6>
        <ul class="list-unstyled mb-4">
          <li><a href="about us.php" class="text-muted text-decoration-none text-muted-hover">About us</a></li>
          <li><a href="contact us.php" class="text-muted text-decoration-none text-muted-hover">Contact us</a></li>
        </ul>
      </div>

      <div class="col-6 col-sm-4 col-lg-2">
        <h6 class="text-uppercase fw-bold mb-3">Credits</h6>
        <ul class="list-unstyled mb-0">
          Images by <a href="https://www.freepik.com" class="text-muted text-decoration-none text-muted-hover">Freepik</a>
        </ul>
      </div>

      <div class="col-6 col-sm-4 col-lg-2">
        <h6 class="text-uppercase fw-bold mb-3">Visit Us On</h6>
        <ul class="list-unstyled mb-4">
          <a href="https://www.facebook.com/HealthPalEssentials" >
            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
              <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
            </svg>
          </a>
        </ul>
      </div>
    </div>    
  </div>
  <!--- copyright --->
   <div class="">
    <div class="container">
      <div class="d-flex justify-content-between py-4 border-top">
        <p class="text-dark">© 2024 Healthpal Medical and Dental Supplies</p>
          <ul style="list-style-type: none; padding: 0; display: flex; margin-left: 20px;">
            <li style="margin-right: 10px;"><a href="product.php" class="text-muted text-decoration-none text-muted-hover">Terms</a> </li>
            <li style="margin-right: 10px;"><a href="product.php" class="text-muted text-decoration-none text-muted-hover">Privacy</a> </li>
            <li><a href="product.php" class="text-muted text-decoration-none text-muted-hover">Security</a></li>
          </ul>
      </div>
    </div>
  </div>
</footer>
<!-- Footer -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantityInput');
    const quantityValue = document.getElementById('quantityValue');
    const incrementButton = document.getElementById('incrementButton');
    const decrementButton = document.getElementById('decrementButton');

    // Reset quantity to 1 on page load
    quantityInput.value = 1;
    quantityValue.value = 1;

    function updateQuantity() {
        const quantity = parseInt(quantityInput.value) || 1;
        quantityValue.value = quantity; // Update hidden input for form submission
    }

    function incrementQuantity() {
        let quantity = parseInt(quantityInput.value) || 1;
        quantity += 1;
        quantityInput.value = quantity;
        updateQuantity();
    }

    function decrementQuantity() {
        let quantity = parseInt(quantityInput.value) || 1;
        if (quantity > 1) {
            quantity -= 1;
            quantityInput.value = quantity;
            updateQuantity();
        }
    }

    incrementButton.addEventListener('click', incrementQuantity);
    decrementButton.addEventListener('click', decrementQuantity);
    quantityInput.addEventListener('input', updateQuantity);

    // Reset quantity to 1 when the user navigates back to the page
    window.addEventListener('pageshow', function() {
        quantityInput.value = 1;
        quantityValue.value = 1;
    });
});

</script>


<!--Jquery-->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
<!--Popper-->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<!--Bootstrapjs-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

</body>
</html>
