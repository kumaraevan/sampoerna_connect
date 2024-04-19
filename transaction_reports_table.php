<?php
session_start();
require_once 'config.php';

// Redirect non-logged in users or non-admins
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("Location: login.php");
    exit;
}

$feedback_message = "";
$search = $_GET["search"] ?? ''; // Simplified fetching of search term
$search = trim($search);
$whereClause = "";
$params = [];
$queryTypes = '';

if (!empty($search)) {
    $whereClause = "WHERE ReportID LIKE CONCAT('%', ?, '%') 
                    OR CustomerID LIKE CONCAT('%', ?, '%')
                    OR OrderID LIKE CONCAT('%', ?, '%')
                    OR ProductID LIKE CONCAT('%', ?, '%')
                    OR PaymentID LIKE CONCAT('%', ?, '%')";
    $params = array_fill(0, 5, $search);
    $queryTypes = str_repeat('s', count($params)); // 's' denotes string type for all params
}

$sql = "SELECT * FROM transaction_reports $whereClause ORDER BY ReportID ASC";
$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($queryTypes, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $feedback_message = "Error preparing query: " . htmlspecialchars($conn->error);
}

if (!$result) {
    $feedback_message = "Error retrieving transaction reports: " . htmlspecialchars($conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Reports Table</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <?php include 'sidebar.php'; ?>

    <div class="container mx-auto px-4 pt-5">
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">Manage Transaction Reports</h2>

        <!-- Search bar -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="mb-4">
            <div class="flex items-center">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search reports" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button type="submit" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Search</button>
            </div>
        </form>

        <!-- Display feedback message -->
        <?php echo $feedback_message; ?>

        <div class="bg-white shadow overflow-hidden rounded-lg">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Report ID</th>
                        <th class="py-3 px-6 text-left">Customer ID</th>
                        <th class="py-3 px-6 text-left">Order ID</th>
                        <th class="py-3 px-6 text-left">Product ID</th>
                        <th class="py-3 px-6 text-left">Payment ID</th>
                        <th class="py-3 px-6 text-center">Detail Report</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row["ReportID"]); ?></td>
                        <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row["CustomerID"]); ?></td>
                        <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row["OrderID"]); ?></td>
                        <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row["ProductID"]); ?></td>
                        <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row["PaymentID"]); ?></td>
                        <td class="py-3 px-6 text-center"><a href="transaction_detail_report.php?id=<?php echo $row["ReportID"]; ?>" class="text-blue-500 hover:text-blue-800"><i class="fas fa-file-alt"></i></a></td>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php $conn-> close(); ?>
</body>
</html>