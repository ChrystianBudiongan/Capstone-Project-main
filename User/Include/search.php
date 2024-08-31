<?php
session_start();
require 'db.handler.inc.php';

// Retrieve and sanitize search query from POST
$searchQuery = isset($_POST['query']) ? trim($_POST['query']) : '';

if (!empty($searchQuery)) {
    // Prepare the search query to prevent SQL injection
    $searchQuery = "%$searchQuery%";

    // Pagination logic
    $perPage = 5; // Number of items per page
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $start = ($page - 1) * $perPage;

    // Prepare the SQL query
    $stmt = $pdo->prepare("SELECT ProductID, TypeID FROM tbl_product WHERE Name LIKE ? LIMIT $start, $perPage");

    // Execute the statement
    $stmt->execute([$searchQuery]);

    // Fetch the results
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($result)) {
        // Determine if the products are dental or medical based on TypeID
        $firstProductType = $result[0]['TypeID'];

        // Store the search query in the session
        $_SESSION['search_query'] = $searchQuery;

        // Redirect based on the first product's TypeID
        if ($firstProductType == 1) {
            header("Location: ../medicalproduct.php");
        } elseif ($firstProductType == 2) {
            header("Location: ../dentalproduct.php");
        }
        exit();
    } else {
        // If no products are found, display a message
        echo "No results found for the provided search query.";
    }
} else {
    // Handle empty query or invalid access
    echo "Invalid search request.";
}
?>

