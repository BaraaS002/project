<?php
include 'partials/constants.php';
// if there is user logged in already go to home page
if (isset($_SESSION['userID'])) {
    $id = $_SESSION['userID'];
    header("location:manageUser.php?id=$id");
}
// if id in get then go to log in page(same of this page) but without of it
if (isset($_GET['id'])) {
    header("location:logInPage.php");
}
$errors = [];
$noMatch = "";

// check if user press submit button in form or not
if (isset($_POST["submit"])) {
    // ignore hml characters (for more security), and delete white spaces from the vales in inputs in start and end of the value
    $em = htmlspecialchars(trim($_POST["email"]));
    $pw = htmlspecialchars(trim($_POST["password"]));
    // call class user
    include "partials/userClass.php";
    $objUser = new User();
    // do execute in the class
    $arr = $objUser->logIn($em, $pw);
    // no user in db match that u put in log in form inputs
    if (isset($arr["noMatch"])) {
        $noMatch = "show";
    } else {
        // array of errors
        $errors = $arr;
    }
}

$thirdChildOfHeader = '<div class="reg">
            <a href="" class="link">Register</a>
        </div>';
$pageTitle = "Log In";
include "partials/header.php";

?>
<div class="container form">
    <form method="POST">
        <div class="input">
            <label for="email">Enter Email Address :</label>
            <input id="email" name="email" type="text" placeholder="Email Address"
                   value="<?php /* if there value in post print it */ if (isset($_POST["submit"])) echo htmlspecialchars(trim($_POST["email"])) ?>"/>
            <span class="error <?php if (isset($errors["email"])) echo "show" ?>">X
                <?php if (isset($errors["email"])) echo $errors["email"] ?> X</span>
        </div>
        <div class="input">
            <label for="password">Enter Password :</label>
            <div class="passInput">
                <input id="password" name="password" type="password" placeholder="Password"
                       value="<?php /* if there value in post print it */ if (isset($_POST["submit"])) echo htmlspecialchars(trim($_POST["password"])) ?>"/>
                <i class="passwordToggleBTN fa-solid fa-eye-slash"></i>
            </div>
            <span class="error <?php if (isset($errors["password"])) echo "show" ?>">X
                <?php if (isset($errors["password"])) echo $errors["password"] ?> X</span>
        </div>
        <input type="submit" value="Log in" name="submit" class="btnLogIn">
        <div class="noMatch <?php if ($noMatch === "show") echo "show"; ?>">X Your Email or Password is Incorrect !
            X<br>Please Try again ...
        </div>
        <?php
        // if user deleted his account will come here and this massage will display
        if (isset($_GET['deleted'])) {
            echo '
            <div class="noMatch show" style="color: rgb(201, 38, 38)">✔ Deleting Done ✔</div>
     ';
        } ?>
        <div class="togglePage">
            <span> Don't Have account ? </span>
            <a class="link">Register</a>
        </div>
    </form>
</div>
<?php include "partials/footer.php"; ?>
<script>
    window.onload = () => {
        // focus in first input
        let firstInput = document.querySelector(".container .input #email");
        if (firstInput.value !== '')
            firstInput.value = firstInput.value + " ";
        firstInput.focus();
        firstInput.value = firstInput.value.trim();
    }
    // links goes to registers page
    document.querySelectorAll(".link").forEach((e) => {
        e.addEventListener("click", () => {
            window.close();
            window.open("register.php", "_blank")
        });
    });
    // button that show password
    let passwordInput = document.querySelector("#password");
    let passwordToggleBTN = document.querySelector(".passwordToggleBTN");
    passwordToggleBTN.addEventListener("click", () => {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            passwordToggleBTN.classList.replace("fa-eye-slash", "fa-eye");
        } else {
            passwordInput.type = "password";
            passwordToggleBTN.classList.replace("fa-eye", "fa-eye-slash");
        }
    });
</script>
</body>

</html>