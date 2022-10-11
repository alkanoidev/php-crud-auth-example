<?php session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
?>

<?php $page_title = "Index";
include_once("templates/header.php"); ?>
<h1 class="page-title">You are logged in!!!</h1>
<?php include_once("templates/footer.php") ?>