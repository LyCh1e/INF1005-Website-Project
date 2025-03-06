<?php
$email = $fname = $lname = $ph = $pwd = $pwd_confirm = $errorMsg = "";
$success = true;

if (empty($_POST["fname"])) {
    $errorMsg .= "First name is required.<br>";
    $success = false;
} else {
    $fname = sanitize_input($_POST["fname"]);
}

if (empty($_POST["lname"])) {
    $errorMsg .= "Last name is required.<br>";
    $success = false;
} else {
    $lname = sanitize_input($_POST["lname"]);
}

if (empty($_POST["ph"])) {
    $errorMsg .= "Phone Number is required.<br>";
    $success = false;
} else {
    $ph = sanitize_input($_POST["ph"]);
    if (!preg_match("/^\d{8}$/", $ph)) {
        $errorMsg .= "Invalid phone number.";
        $success = false;
    }
}

if (empty($_POST["pwd"])) {
    $errorMsg .= "Password is required.<br>";
    $success = false;
} else {
    $pwd = sanitize_input($_POST["pwd"]);
}

if (empty($_POST["pwd_confirm"])) {
    $errorMsg .= "Please confirm your password.<br>";
    $success = false;
} else {
    $pwd_confirm = sanitize_input($_POST["pwd_confirm"]);
    if ($pwd_confirm != $pwd) {
        $errorMsg .= "Passwords do not match.<br>";
        $success = false;
    }
}

if (empty($_POST["email"])) {
    $errorMsg .= "Email is required.<br>";
    $success = false;
} else {
    $email = sanitize_input($_POST["email"]);
    // Additional check to make sure e-mail address is well-formed.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg .= "Invalid email format.";
        $success = false;
    }
}
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function saveMemberToDB()
{
    global $fname, $lname, $email, $ph, $pwd, $errorMsg, $success;
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    // Create database connection.
    $config = parse_ini_file('/var/www/private/db-config.ini');
    if (!$config) {
        $errorMsg = "Failed to read database config file.";
        $success = false;
    } else {
        $conn = new mysqli(
            $config['servername'],
            $config['username'],
            $config['password'],
            $config['dbname']
        );
        // Check connection
        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            $success = false;
        }
        $stmt = $conn->prepare("SELECT email, phone_number FROM world_of_pets_members WHERE email = ? OR phone_number = ?");
        $stmt->bind_param("ss", $email, $ph);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0){
            $errorMsg = "Phone number or email already exists.";
            $success = false;
        }else {
            // Prepare the statement:
            $stmt = $conn->prepare("INSERT INTO world_of_pets_members
(fname, lname, email, phone_number, password) VALUES (?, ?, ?, ?, ?)");
            // Bind & execute the query statement:
            
            $stmt->bind_param("sssss", $fname, $lname, $email, $ph, $hash);
            if (!$stmt->execute()) {
                $errorMsg = "Execute failed: (" . $stmt->errno . ") " .
                    $stmt->error;
                $success = false;
            }
            $stmt->close();
        }
        $conn->close();
    }
}
if ($success) {
    saveMemberToDB();
}
?>

<!DOCTYPE html>
<html lang="en">

<?php
include "inc/head.inc.php";
?>
<link rel="stylesheet" href="css/main.css">
<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <main>
    <div class="container">
        <?php
        if ($success) {
            $hash = password_hash($pwd, PASSWORD_DEFAULT);
            echo "<h1>Your registration is successful!</h1>";
            echo "<h3>Thank you for signing up, " . $fname . ".</h3>";
            echo '<p><a href="login.php"><button class="loginbutton">Login</button></a></p>';
        } else {
            echo "<h1>Oops!</h1>";
            echo "<h3>The following input errors were detected:</h3>";
            echo "<p>" . $errorMsg . "</p>";
            echo '<p><a href="register.php"><button class="regbutton">Return to Sign Up</button></a></p>';
        }
        ?>
    </div>
    </main>
</body>
<?php
include "inc/footer.inc.php";
?>

</html>