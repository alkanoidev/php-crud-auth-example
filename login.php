<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: index.php");
    exit;
}
require_once "config/database.php";
$username = $password = "";
$username_err = $password_err = "";
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (empty(trim($_POST['username']))) {
        $username_err = "Please enter the username.";
    } else {
        $username = trim(htmlspecialchars($_POST['username']));
    }
    if (empty(trim($_POST['password']))) {
        $password_err = "Please enter the password.";
    } else {
        $password = trim(htmlspecialchars($_POST['password']));
    }

    if (empty($username_err) && empty($password_err)) {
        $pdo = Database::getConnection();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM users WHERE username = :username";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = trim($_POST['username']);
            $password = trim($_POST['password']);

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $username = $row['username'];
                        $password1 = $row['password'];
                        if (password_verify($password, $password1)) {
                            session_start();
                            $_SESSION['loggedin'] = true;
                            $_SESSION['username'] = $username;
                            $_SESSION['id'] = $id;
                            Database::disconnect();
                            header("location: index.php");
                        } else {
                            $password_err = "Password that you entered is incorrect.";
                        }
                    }
                } else {
                    $username_err = "No account was found by that username.";
                }
            } else {
                echo "Oops.. Something went wrong...";
            }
        }
        unset($stmt);
    }
    unset($pdo);
}
?>

<?php
$page_title = "Login";
include_once("templates/header.php");
?>
<div id="loginContainer">
    <div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : '' ?>">
                <input type="text" name="username" class="form-control" placeholder="Username">
                <span class="help-block"><?php echo $username_err ?></span>
            </div>
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : '' ?>">
                <input type="password" name="password" class="form-control" placeholder="Password">
                <span class="help-block"><?php echo $password_err ?></span>
            </div>
            <div class="form-group">
                <input type="submit" value="Log in" class="btn btn-primary">
            </div>
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </form>
    </div>
</div>

<?php include_once "templates/footer.php" ?>