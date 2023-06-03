<?php
include 'partials/constants.php';
if (isset($_GET['id'])) {
    header("location:register.php");
}
if (isset($_SESSION['userID'])) {
    header("location:manageUser.php");
}

$errors = [];
$errorDB = "";

if (isset($_POST["submit"])) {
    $un = htmlspecialchars(trim($_POST["name"]));
    $em = htmlspecialchars(trim($_POST["email"]));
    $pw = htmlspecialchars(trim($_POST["password"]));
    $cp = htmlspecialchars(trim($_POST["conPass"]));
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $image_name = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME) . "_" . $un . "." . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    } else {
        $image_name = "avatar.png";
    }
    $image = $_FILES;
    include "partials/userClass.php";
    $objUser = new User();
    $arr = $objUser->register($un, $em, $pw, $cp, $image, $image_name);
    if (isset($arr["errorDB"])) {
        $errorDB = "show";
    } else {
        $errors = $arr;
    }
}

$thirdChildOfHeader = '<div class="reg">
            <a href="logInPage.php" class="link">Log In</a>
        </div>';
$pageTitle = "Sign Up";
include "partials/header.php";

?>
<div class="container form">
    <form method="post" enctype="multipart/form-data">
        <div class="input">
            <label for="name">Your Name :</label>
            <input id="name" name="name" type="text" placeholder="Name ..."
                value="<?php if (isset($_POST["submit"])) echo htmlspecialchars(trim($_POST["name"])) ?>" required />
            <span class="error <?php if (isset($errors["name"])) echo "show" ?>">X
                <?php if (isset($errors["name"])) echo $errors["name"] ?> X</span>
        </div>
        <div class="input">
            <label for="email">Your Email :</label>
            <input id="email" name="email" type="email" placeholder="Email ..."
                value="<?php if (isset($_POST["submit"])) echo htmlspecialchars(trim($_POST["email"])) ?>" required />
            <span class="error <?php if (isset($errors["email"])) echo "show" ?>">X
                <?php if (isset($errors["email"])) echo $errors["email"] ?> X</span>
        </div>
        <div class="input">
            <label title="More than 8 letters. Include lower, upper letters and numbers. " for="password">
                Your Password :
                <i class="fa-solid fa-circle-info" style="margin-left: 10px;height: 100%;"></i>
            </label>
            <div class="passInput">
                <input id="password" name="password" type="password" placeholder="Password ... "
                    value="<?php if (isset($_POST["submit"])) echo htmlspecialchars(trim($_POST["password"])) ?>"
                    required />
                <i class="passwordToggleBTN fa-solid fa-eye-slash"></i>
            </div>
            <span class="error <?php if (isset($errors["password"])) echo "show" ?>">X
                <?php if (isset($errors["password"])) echo $errors["password"] ?> X</span>
        </div>
        <div class="input">
            <label for="conPass">Confirm Password :</label>
            <div class="passInput">
                <input id="conPass" name="conPass" type="password" placeholder="Confirm Password ... "
                    value="<?php if (isset($_POST["submit"])) echo htmlspecialchars(trim($_POST["conPass"])) ?>"
                    required />
                <i class="passwordToggleBTN fa-solid fa-eye-slash"></i>
            </div>
            <span class="error <?php if (isset($errors["conPass"])) echo "show" ?>">X
                <?php if (isset($errors["conPass"])) echo $errors["conPass"] ?> X</span>
        </div>
        <div class="input">
            <label title="If you don't upload image, a defualt image will be set !" for="image">Enter Image :
                <i class="fa-solid fa-circle-info" style="margin-left: 10px;height: 100%;"></i>
            </label>
            <label for="image" class="upIm">Upload Image</label>
            <input id="image" name="image" value="Choose Image" type="file" accept="image/*" />
            <span class="error  <?php if (isset($errors["image"])) echo "show" ?>">X
                <?php if (isset($errors["image"])) echo $errors["image"] ?> X</span>
        </div>
        <input type="submit" value="Sign Up" name="submit" class="btnLogIn">
        <div class="noMatch <?php if ($errorDB === "show") echo "show"; ?>">X Error while compiling query ! X<br>Please
            Try again ...
        </div>
        <div class="togglePage">
            <span> Have account ? </span>
            <a class="link" href="logInPage.php">Log In</a>
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
document.querySelector(".link").addEventListener("click", () => {
    window.close();
    window.open("logInPage.php", "_blank")
});
// let passwordInput = document.querySelector("#password");
let passwordToggleBTN = document.querySelectorAll(".passwordToggleBTN");
passwordToggleBTN.forEach((e) => {
    let passwordInput = e.parentElement.firstElementChild;
    e.addEventListener("click", () => {
        // console.log(e.parentElement.firstElementChild);
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            e.classList.replace("fa-eye-slash", "fa-eye");
        } else {
            passwordInput.type = "password";
            e.classList.replace("fa-eye", "fa-eye-slash");
        }
    });
});
</script>
</body>

</html>