<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $page_title ?></title>

    <!-- minified Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />

    <!-- nas custom CSS -->
    <link rel="stylesheet" href="assets/custom.css">

</head>
<style>
    body{
        display: flex;
        justify-content: center;
    }
    #container{
        width: 500px;
    }
</style>
<body>
    <div id="container">
        <div class="page-header">
            <h1><?php echo $page_title ?></h1>
        </div>