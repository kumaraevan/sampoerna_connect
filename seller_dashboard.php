<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'seller') {
    header("Location: login.php");
    exit;
}

$products = array();

$stmt = $conn->prepare("SELECT ProductID, Name, Price, Description, StockQuantity, Category, ImageURLs FROM products WHERE SellerID = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Dashboard</title>
        <style>
            .navbar {
                overflow: hidden;
                background-color: #333;
            }

            .navbar a {
                float: left;
                display: block;
                color: white;
                text-align: center;
                padding: 14px 20px;
                text-decoration: none;
            }

            .navbar::after {
                content: "";
                display: table;
                clear: both;
            }

            .navbar a:hover {
                background-color: #ddd;
                color: black;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table, th, td {
                border: 1px solid #ddd;
            }

            th, td {
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }
        </style>
    </head>
    <body>
    <div class="navbar">
    <a href="seller_dashboard.php">Dashboard</a>
    <a href="seller_add_new_products.php">Add New Products</a>
    <a href="seller_manage_products.php">Manage Products</a>
    <a href="seller_orders.php">View Orders</a>
    <a href="logout.php">Logout</a>
    </div>
    
    <div class="dashboard-content">
    <h1>Welcome To Your Dashboard, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h1>
    
    <div class="dashboard-widget">
        <h2>Product Listings</h2>
        <?php if (count($products) > 0): ?>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Category</th>
                    <!-- Add columns for other details if needed -->
                </tr>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['Name']); ?></td>
                        <td>Rp.<?php echo htmlspecialchars($product['Price']); ?></td>
                        <td><?php echo htmlspecialchars($product['Description']); ?></td>
                        <td><?php echo htmlspecialchars($product['StockQuantity']); ?></td>
                        <td><?php echo htmlspecialchars($product['Category']); ?></td>
                        <!-- Add cells for other details if needed -->
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>
    
    <div class="dashboard-widget">
        <h2>Recent Orders</h2>
    </div>
    
</div>
</body>
</html>