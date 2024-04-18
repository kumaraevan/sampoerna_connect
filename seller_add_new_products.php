<?php
session_start();
require_once 'config.php';

// Redirect if not logged in or if the user is not a seller or admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["role"] !== 'seller' && $_SESSION["role"] !== 'admin')) {
    header("Location: login.php");
    exit;
}

// Initialize variables
$message = "";
$categoryOptions = "";

// Fetch categories for the dropdown
$sql = "SELECT CategoryID, CategoryName FROM categories";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categoryOptions .= "<option value='" . $row["CategoryID"] . "'>" . $row["CategoryName"] . "</option>";
    }
} else {
    $message = "No categories found. Please add categories before adding products.";
}

// Process the form when it is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Sanitize input
    $name = $conn->real_escape_string(trim($_POST['name']));
    $price = $conn->real_escape_string(trim($_POST['price']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $stockQuantity = $conn->real_escape_string(trim($_POST['stockQuantity']));
    $categoryID = intval($_POST['category']);

    // Handle file upload
    $target_dir = "img/uploads/";
    $target_file = $target_dir . basename($_FILES["productImage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["productImage"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $message = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size - for example, limit to 5MB
    if ($_FILES["productImage"]["size"] > 5000000) {
        $message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $message = "Sorry, your file was not uploaded.";
    // If everything is ok, try to upload file
    } else {
        if (!move_uploaded_file($_FILES["productImage"]["tmp_name"], $target_file)) {
            $message = "Sorry, there was an error uploading your file.";
        }
    }

    // Insert product into the database if the file was successfully uploaded
    if ($uploadOk == 1) {
        $sql = "INSERT INTO products (SellerID, Name, Price, Description, StockQuantity, CategoryID, ImageURLs) VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("isssiss", $_SESSION['user_id'], $name, $price, $description, $stockQuantity, $categoryID, $target_file);
            
            if ($stmt->execute()) {
                $message = "Product added successfully!";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-gray-900 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="seller_dashboard.php" class="hover:bg-gray-700 px-3 py-2 rounded">Dashboard</a>
            <a href="seller_add_new_products.php" class="hover:bg-gray-700 px-3 py-2 rounded">Add New Products</a>
            <a href="seller_manage_products.php" class="hover:bg-gray-700 px-3 py-2 rounded">Manage Products</a>
            <a href="seller_orders.php" class="hover:bg-gray-700 px-3 py-2 rounded">View Orders</a>
            <div class="flex space-x-4">
                <a href="logout.php" class="hover:bg-gray-700 px-3 py-2 rounded">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container mx-auto mt-10">
        <h2 class="text-2xl font-bold mb-5 text-center">Add New Product</h2>
        <?php if ($message): ?>
            <div class="mx-auto max-w-md py-4 px-8 bg-white shadow-lg rounded-lg my-20">
                <div>
                    <h2 class="text-gray-800 text-3xl font-semibold"><?php echo $message; ?></h2>
                </div>
            </div>
        <?php endif; ?>
        <form action="seller_add_new_products.php" method="post" enctype="multipart/form-data" class="w-full max-w-lg mx-auto mt-6">
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="name">
                        Product Name
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" type="text" name="name" required>
                </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="price">
                        Price
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" type="text" name="price" required>
                </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="description">
                        Description
                    </label>
                    <textarea class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="description" required></textarea>
                </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="stockQuantity">
                        Stock Quantity
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" type="number" name="stockQuantity" required>
                </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="category">
                        Category
                    </label>
                    <div class="relative">
                        <select class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="category" required>
                            <?php echo $categoryOptions; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="productImage">
                        Product Image
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" type="file" name="productImage" required>
                </div>
            </div>
            <div class="md:flex md:items-center">
                <div class="md:w-1/3">
                    <button class="shadow bg-blue-500 hover:bg-blue-700 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded" type="submit" name="submit">
                        Add Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>