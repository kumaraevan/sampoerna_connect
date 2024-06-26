<?php
session_start();
require_once 'config.php';

// Redirect non-logged in users or non-admins
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("Location: login.php");
    exit;
}

$feedback_message = "";  // Initialize success or error feedback message
$search = "";
$whereClause = "";

// Check if search query is provided
if (isset($_GET["search"]) && !empty(trim($_GET["search"]))) {
    $search = trim($_GET["search"]);
    // Construct the WHERE clause to search by product name or product ID
    $whereClause = "WHERE Name LIKE '%$search%' OR ProductID LIKE '%$search%' OR SellerID LIKE '%$search%'";
}

// Query to retrieve existing products with search filter
$sql = "SELECT * FROM products $whereClause ORDER BY ProductID DESC";
$result = $conn->query($sql);

// Check for query errors
if (!$result) {
    $feedback_message = "Error retrieving products: " . htmlspecialchars($conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Table</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <?php include 'sidebar.php'; ?>

    <div class="container mx-auto px-4 pt-5">
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">Manage Products</h2>

        <!-- Search bar -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="mb-4 flex">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search products" required class="px-4 py-2 rounded-l-md focus:outline-none focus:ring focus:border-blue-300 w-full">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-r-md">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <!-- Display feedback message -->
        <?php echo $feedback_message; ?>

        <div class="bg-white shadow overflow-hidden rounded-lg">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Product ID</th>
                        <th class="py-3 px-6 text-left">Seller ID</th>
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Description</th>
                        <th class="py-3 px-6 text-left">Price</th>
                        <th class="py-3 px-6 text-left">Edit</th>
                        <th class="py-3 px-6 text-left">Delete</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap"><?= htmlspecialchars($row["ProductID"]) ?></td>
                        <td class="py-3 px-6 text-left"><?= htmlspecialchars($row["SellerID"]) ?></td>
                        <td class="py-3 px-6 text-left"><?= htmlspecialchars($row["Name"]) ?></td>
                        <td class="py-3 px-6 text-left"><?= htmlspecialchars($row["Description"]) ?></td>
                        <td class="py-3 px-6 text-left"><?= htmlspecialchars($row["Price"]) ?></td>
                        <td class="py-3 px-6 text-left"><a href="admin_edit_product.php?id=<?= $row["ProductID"]; ?>" class="text-blue-500 hover:text-blue-800"><i class="fas fa-edit"></i></a></td>
                        <td class="py-3 px-6 text-left"><a href="admin_delete_product.php?id=<?= $row["ProductID"]; ?>" onclick="return confirm('Are you sure you want to delete this product?')" class="text-red-500 hover:text-red-800"><i class="fas fa-trash-alt"></i></a></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php $conn->close(); ?>
</body>
</html>