<?php
include 'partials/constants.php';

$id = 0;
$loggedInUserId = 0;
$role = -2;
$errorDB = "";

include 'partials/userClass.php';
$objUser = new User();

if (isset($_GET['id'])) {
    if (!isset($_SESSION['userID'])) {
        unset($_SESSION['timeSession']);
        session_destroy();
        header("location:logInPage.php");
    } else {
        $id = htmlspecialchars($_GET['id']);
        $role = $objUser->getRole($id);
    }
} else if (isset($_SESSION['userID'])) {
    header("location:manageUser.php");
} else {
    header("location:logInPage.php");
}

if ($role == -1)
    header("location:manageUser.php");
$loggedInUserId = $_SESSION['userID'];
$roleLoggedInUser = $objUser->getRole($loggedInUserId);
if ($roleLoggedInUser == 2 && $id != $loggedInUserId) {
    header("location:manageUser.php");
}
$name = $objUser->getName($id);
$email = $objUser->getEmail($id);
$image_name = $objUser->getImageName($id);

if (isset($_POST["submit"])) {
    $un = htmlspecialchars(trim($_POST["name"]));
    $em = htmlspecialchars(trim($_POST["email"]));
    if (isset($_POST["role"]))
        $ro = htmlspecialchars($_POST["role"]);
    else
        $ro = $role;
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $new_image_name = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME) . "_" . $un . "." . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    } else {
        $new_image_name = $image_name;
    }
    $image = $_FILES;
    $arr = $objUser->updateData($id, $un, $em, $ro, $image, $new_image_name);
    if (isset($arr["Done"])) {
        header("location:profile.php?id=$id&updated=true");
    } else if (isset($arr["errorDB"])) {
        $errorDB = "show";
    } else {
        $errors = $arr;
    }
}

$thirdChildOfHeader = '<div class="reg">
            <a href="" class="link">Back</a>
        </div>';
$pageTitle = "Update Data | $name";
include "partials/header.php";
?>
<div class="container form">
    <form method="post" enctype="multipart/form-data">
        <div class="input">
            <label for="name">Your Name :</label>
            <input id="name" name="name" type="text" placeholder="Name ..." required value="<?php if (isset($_POST["submit"])) echo htmlspecialchars(trim($_POST["name"]));
                                                                                            else echo $name; ?>" />
            <span class="error <?php if (isset($errors["name"])) echo "show" ?>">X
                <?php if (isset($errors["name"])) echo $errors["name"] ?> X</span>
        </div>
        <div class="input">
            <label for="email">Your Email :</label>
            <input id="email" name="email" type="email" placeholder="Email ..." required value="<?php if (isset($_POST["submit"])) echo htmlspecialchars(trim($_POST["email"]));
                                                                                                else echo $email ?>" />
            <span class="error <?php if (isset($errors["email"])) echo "show" ?>">X
                <?php if (isset($errors["email"])) echo $errors["email"] ?> X</span>
        </div>
        <?php
        if ($role == 0 || ($role == 2 && $roleLoggedInUser == 2)) {
            echo '<div class="input">
            <label>You CANNOT change your role !</label>
        </div>';
        } else {
            $roleAdmin = ($role == 1) ? "checked" : "";
            $roleUser = ($role == 2) ? "checked" : "";
            echo '<div class="input">
            <label for="email">Your Role :</label>
            <div class="radio">
                <div class="op">
                    <input type="radio" name="role" id="admin" value="admin" ' . $roleAdmin . '>
                    <label for="admin" class="option admin">
                        <span>Admin</span>
                    </label>
                </div>
                <div class="op">
                    <input type="radio" name="role" id="user" value="user" ' . $roleUser . '>
                    <label for="user" class="option user">
                        <span>User</span>
                    </label>
                </div>
            </div>
        </div>';
        }
        ?>
        <div class="input">
            <label title="If you don't upload image, a defualt image will be set !" for="image">Your Image :
                <i class="fa-solid fa-circle-info" style="margin-left: 10px;height: 100%;"></i>
            </label>
            <div class="img">
                <img src="images/<?php echo $image_name ?>" alt="">
            </div>
            <label for="image" class="upIm">Upload Image</label>
            <input id="image" name="image" value="Choose Image" type="file" accept="image/*" />
            <span class="error  <?php if (isset($errors["image"])) echo "show" ?>">X
                <?php if (isset($errors["image"])) echo $errors["image"] ?> X</span>
        </div>
        <input type="submit" value="Update" name="submit" class="btnUpdate">
        <div class="noMatch <?php if ($errorDB === "show") echo "show"; ?>">X Error while compiling query ! X<br>Please
            Try again ...
        </div>
    </form>
</div>
<?php include "partials/footer.php"; ?>
<script>
    window.onload = () => {
        let firstInput = document.querySelector(".container .input #name");
        if (firstInput.value !== '')
            firstInput.value = firstInput.value + " ";
        firstInput.focus();
        firstInput.value = firstInput.value.trim();
    }
    <?php echo 'document.querySelector(".header .link").addEventListener("click",()=>{close();})'; ?>
</script>
</body>

</html>