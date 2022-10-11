<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
}
require_once "config/database.php";

$email = $firstname = $lastname = $username = $password = $confirmPassword = "";
$emailErr = $firstnameErr = $lastnameErr = $usernameErr = $passwordErr = $confirmPasswordErr = "";
$regExOnlyLetters = "/^[a-zA-Z]+$/";
$regExLettersAndNumbers = "/^[a-zA-Z0-9\s]+$/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //validate username and check if exists
    if (empty(trim($_POST["username"]))) {
        $usernameErr = "Pls enter username.";
    } else {
        $username = trim(htmlspecialchars($_POST["username"]));
        if (!preg_match($regExLettersAndNumbers, $username)) {
            $usernameErr = "Username can contain letters, numbers and white spaces.";
        } else {
            $pdo = Database::getConnection();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "SELECT id FROM users WHERE username = :username";
            if ($stmt = $pdo->prepare($sql)) { // TODO: zasto
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $username; // TODO: zasto inicijalizujemo posle 
                if ($stmt->execute()) { // zasto
                    if ($stmt->rowCount() == 1) {
                        $usernameErr = "Username exists.";
                    } else {
                        $username = trim($_POST["username"]);
                    }
                } else {
                    echo "Oops! Something went wront.. Try again later..";
                }
            }
        }
        Database::disconnect();
        unset($stmt);
    }
    function validate($str)
    {
        return trim(htmlspecialchars($str));
    }
    //validate firstname
    if (empty($_POST["firstname"])) {
        $firstnameErr = "Firstname must be filled";
    } else {
        $firstname = validate($_POST["firstname"]);
        if (!preg_match($regExOnlyLetters, $firstname)) {
            $firstnameErr = "Firstname must contain only letters.";
        }
    }

    //validate lastname
    if (empty($_POST['lastname'])) {
        $lastnameErr = "Lastname must be filled";
    } else {
        $lastname = validate($_POST['lastname']);
        if (!preg_match($regExOnlyLetters, $lastname)) {
            $firstnameErr = "Lastname must contain only letters.";
        }
    }

    // validate email
    if (empty($_POST['email'])) {
        $emailErr = "Email must be filled.";
    } else {
        $email = validate($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Enter a valid email.";
        }
    }

    // validate password
    if (empty(validate($_POST["password"]))) {
        $passwordErr = "Please enter a password.";
    } else if (strlen(trim($_POST['password']) < 6)) {
        $passwordErr = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST['password']);
    }

    // validate confirm password
    if (empty(validate($_POST["confirm_password"]))) {
        $confirmPasswordErr = "Please enter password.";
    } else {
        $confirmPassword = validate($_POST['confirm_password']);
        if (empty($passwordErr) && ($password != $confirmPassword)) {
            $confirmPasswordErr = "Passwords don't match.";
        }
    }

    if (empty($usernameErr) && empty($passwordErr) && empty($confirmPasswordErr) && empty($emailErr) && empty($firstnameErr) && empty($lastnameErr)) {
        $pdo = Database::getConnection();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO users (username, firstname, lastname, password, email) VALUES (:username, :firstname, :lastname, :password, :email)";

        if ($stmt = $pdo->prepare($sql)) {
            // TODO: zasto koristimo $param promenljive
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":firstname", $param_firstname, PDO::PARAM_STR);
            $stmt->bindParam(":lastname", $param_lastname, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);

            $param_username = $username;
            $param_firstname = $firstname;
            $param_lastname = $lastname;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_email = $email;
            if ($stmt->execute()) {
                Database::disconnect();
                header("location: login.php");
            } else {
                echo "Oops! Something went wront.. Try again later..";
            }
        }
        unset($stmt);
    }
    unset($pdo);
}
?>

<?php
$page_title = "Register";
include_once "templates/header.php";
?>
<div id="loginContainer">
    <div>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
            <div class="form-group <?php echo (!empty($usernameErr)) ? 'has-error' : ''; ?>">
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" placeholder="Username">
                <span class="help-block"><?php echo $usernameErr ?></span>
            </div>
            <div class="form-group <?php echo (!empty($emailErr)) ? 'has-error' : ''; ?>">
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>" placeholder="Email">
                <span class="help-block"><?php echo $emailErr ?></span>
            </div>
            <div class="form-group <?php echo (!empty($firstnameErr)) ? 'has-error' : ''; ?>">
                <input type="text" name="firstname" class="form-control" value="<?php echo $firstname; ?>" placeholder="Firstname">
                <span class="help-block"><?php echo $firstnameErr ?></span>
            </div>
            <div class="form-group <?php echo (!empty($lastnameErr)) ? 'has-error' : ''; ?>">
                <input type="text" name="lastname" class="form-control" value="<?php echo $lastname; ?>" placeholder="Lastname">
                <span class="help-block"><?php echo $lastnameErr ?></span>
            </div>
            <div class="form-group <?php echo (!empty($passwordErr)) ? 'has-error' : ''; ?>">
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>" placeholder="Password">
                <span class="help-block"><?php echo $passwordErr ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirmPasswordErr)) ? 'has-error' : ''; ?>">
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirmPassword; ?>" placeholder="Confirm Password">
                <span class="help-block"><?php echo $confirmPasswordErr ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" id="regBtn" value="Register">
            </div>
            <p>Already have an account? <a href="login.php">Log in here</a></p>
        </form>
    </div>
</div>