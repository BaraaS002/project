<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/Logo.png" type="image/x-icon">
    <title><?php /* every page have different title */echo $pageTitle; ?></title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/forms.css">

</head>

<body class="">
    <div class="header">
        <div class="dark-mode-toggle">
            <i class="fa-solid fa-moon"></i>
        </div>
        <div class="logo">
            <i class="fa-solid fa-b b"></i>
            <i class="fa-solid fa-r r"></i>
        </div>

        <?php
        // html tags that display in header (because every page have different header)
        echo $thirdChildOfHeader;
        ?>
    </div>