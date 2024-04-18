<?php
session_start();
require_once 'config.php';

// Redirect non-logged in users or non-admins
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Function to sanitize data
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Initialize variables for error messages
$nameErr = $emailErr = $phoneErr = $passwordErr = $success_msg = "";
$name = $email = $phone = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Validate name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);
        // check if name only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
            $nameErr = "Only letters and white space allowed";
        }
    }

    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        // check if e-mail address is well-formed
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // Validate phone
    if (empty($_POST["phone"])) {
        $phoneErr = "Phone is required";
    } else {
        $phone = test_input($_POST["phone"]);
        // check if phone number is valid (this is a simple regex for numbers only)
        if (!preg_match("/^[0-9]*$/", $phone)) {
            $phoneErr = "Only numbers are allowed";
        }
    }

    // Validate password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
    }


    // If no errors, proceed to insert
    if (empty($nameErr) && empty($emailErr) && empty($phoneErr) && empty($passwordErr) ) {
        $password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
        $role = 'admin'; // Set the role

        // Insert into database
        $sql = "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $name, $email, $phone, $password, $role);

            if ($stmt->execute()) {
                // Set success message
                $success_msg = "Admin registered successfully!";
            } else {
                // Set error message
                $success_msg = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    } else {
        // Display error messages
        echo $nameErr;
        echo $emailErr;
        echo $phoneErr;
        echo $passwordErr;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="max-w-lg mx-auto py-10 px-5 mt-20 bg-white shadow-lg rounded">
        <?php if ($success_msg): ?>
            <div class="text-center p-3 mb-2 bg-green-100 text-green-700 rounded">
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Register New Admin</h2>
        <p class="text-gray-600 text-sm text-center mb-6">Please fill this form to create an admin account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                <input type="text" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $name; ?>">
                <p class="text-red-500 text-xs italic"><?php echo $nameErr; ?></p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $email; ?>">
                <p class="text-red-500 text-xs italic"><?php echo $emailErr; ?></p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Phone:</label>
                <input type="text" name="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $phone; ?>">
                <p class="text-red-500 text-xs italic"><?php echo $phoneErr; ?></p>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                <input type="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-red-500 text-xs italic"><?php echo $passwordErr; ?></p>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" name="register" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Register Admin
                </button>
            </div>
        </form>
    </div>
</body>
</html>