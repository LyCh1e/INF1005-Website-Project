<?php
session_start();

$email = $pwd = $errorMsg = "";
$success = true;



if (empty($_POST["pwd"])) {
    $errorMsg .= "Password is required.<br>";
    $success = false;
} else {
    $pwd = sanitize_input($_POST["pwd"]);
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

function authenticateUser()
{
    global $fname, $lname, $email, $hash, $errorMsg, $success;
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
        } else {
            // Prepare the statement:
            $stmt = $conn->prepare("SELECT * FROM world_of_pets_members WHERE email=?");
            // Bind & execute the query statement:
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Note that email field is unique, so should only have one row.
                $row = $result->fetch_assoc();
                $fname = $row["fname"];
                $lname = $row["lname"];
                $hash = $row["password"];
                $ph = $row['phone_number'];
                // Check if the password matches:
                if (!password_verify($_POST["pwd"], $hash)) {
                    // Don’t tell hackers which one was wrong, keep them guessing...
                    $errorMsg = "Email not found or password doesn't match...";
                    $success = false;
                }else {
                    // Password matches, login successful, set session variables
                    $_SESSION['fname'] = $fname;
                    $_SESSION['lname'] = $lname;
                    $_SESSION['email'] = $email;
                    $_SESSION['ph'] = $ph;
                }
            } else {
                $errorMsg = "Email not found or password doesn't match...";
                $success = false;
            }
            $stmt->close();
        }
        $conn->close();
    }
}
if ($success){
    authenticateUser();
    $_SESSION['fname'] = $fname;
}
if(!$success){
    session_unset();
    session_destroy();
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
            echo "<h1>Login successful!</h1>";
            echo "<h3>Welcome back, " . $fname . ".</h3>";
            echo '<p><a href="index.php"><button class="homebutton">Return to Home</button></a></p>';
        } else {
            echo "<h1>Oops!</h1>";
            echo "<h3>The following errors were detected:</h3>";
            echo "<p>Email not found or password doesn't match...</p>";
            // echo "<p>" . $errorMsg . "</p>";
            echo '<p><a href="login.php"><button class="eloginbutton">Return to Login</button></a></p>';
        }
        ?>
    </div>
    </main>
</body>
<?php
include "inc/footer.inc.php";
?>

</html>