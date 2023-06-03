<?php
include 'partials/constants.php';
//
$id = 0;
$loggedInUserId = 0;
$role = -2;
$errorDB = "";

// call class User
include 'partials/userClass.php';
$objUser = new User();

// check if there is user in get
if (isset($_GET['id'])) {
    // if no user logged in well go to log in page
    if (!isset($_SESSION['userID'])) {
        unset($_SESSION['timeSession']);
        session_destroy();
        header("location:logInPage.php");
    } else {
        // get role to the id in get
        $id = htmlspecialchars($_GET['id']);
        $role = $objUser->getRole($id);
    }
    // no user in get
} else if (isset($_SESSION['userID'])) {
    header("location:manageUser.php");
}   // no user in logged in
else {
    header("location:logInPage.php");
}
// the id not in database
if ($role == -1)
    header("location:manageUser.php");

$loggedInUserId = $_SESSION['userID'];
// role described in line 468 in userClass
$roleLoggedInUser = $objUser->getRole($loggedInUserId);
$doActionForChangePassword = $objUser->compareRolesForUpdateDataANDChangePassword($loggedInUserId, $roleLoggedInUser, $id, $role);
// if u cannot change then go home page
if ($doActionForChangePassword == "cannot") {
    header("location:manageUser.php");
}

if (isset($_POST["submit"])) {
    $oldPw = htmlspecialchars(trim($_POST["oldPw"]));
    $newPw = htmlspecialchars(trim($_POST["newPw"]));
    $newConPw = htmlspecialchars(trim($_POST["newConPw"]));
    // do change in user class
    $arr = $objUser->changePassword($id, $oldPw, $newPw, $newConPw);
    // if back with done thin go home page, else print errors
    if (isset($arr["Done"])) {
        header("location:manageUser.php?id=$loggedInUserId&changed=true");
    } else if (isset($arr["errorDB"])) {
        $errorDB = $arr["errorDB"];
    } else {
        $errors = $arr;
    }
}
$thirdChildOfHeader = '<div class="reg">
            <a href="manageUser.php" class="link">Back</a> <!-- on php will close this page -->
        </div>';
$pageTitle = "Change Password | name";
include "partials/header.php";
?>
<div class="container form">
    <form method="post">
        <div class="input">
            <label for="oldPw">Current Password : </label>
            <div class="passInput">
                <input id="oldPw" name="oldPw" type="password" placeholder="Current Password ... "
                    value="<?php /* if there value in post print it */if (isset($_POST["submit"])) echo htmlspecialchars(trim($_POST["oldPw"])) ?>" />
                <i class="passwordToggleBTN fa-solid fa-eye-slash"></i>
            </div>
            <span class="error <?php /* if there an error print it */ if (isset($errors["oldPw"])) echo "show" ?>">X
                <?php if (isset($errors["oldPw"])) echo $errors["oldPw"] ?> X</span>
        </div>
        <div class="input">
            <label title="More than 8 letters. Include lower, upper letters and numbers. " for="newPw">
                New Password :
                <i class="fa-solid fa-circle-info" style="margin-left: 10px;height: 100%;"></i>
            </label>
            <div class="passInput">
                <input id="newPw" name="newPw" type="password" placeholder="New Password ... "
                    value="<?php /* if there value in post print it */ if (isset($_POST["submit"])) echo htmlspecialchars(trim($_POST["newPw"])) ?>" />
                <i class="passwordToggleBTN fa-solid fa-eye-slash"></i>
            </div>
            <span class="error <?php if (isset($errors["newPw"])) echo "show" ?>">X
                <?php if (isset($errors["newPw"])) echo $errors["newPw"] ?> X</span>
        </div>
        <div class="input">
            <label for="newConPw">Confirm Password : </label>
            <div class="passInput">
                <input id="newConPw" name="newConPw" type="password" placeholder="Confirm Password ... "
                    value="<?php /* if there value in post print it */ if (isset($_POST["submit"])) echo htmlspecialchars(trim($_POST["newConPw"])) ?>" />
                <i class="passwordToggleBTN fa-solid fa-eye-slash"></i>
            </div>
            <span class="error <?php if (isset($errors["newConPw"])) echo "show" ?>">X
                <?php if (isset($errors["newConPw"])) echo $errors["newConPw"] ?> X</span>
        </div>
        <input type="submit" value="Change" name="submit" class="btnChange">
        <div class="noMatch <?php if ($errorDB === "showSame" || $errorDB === "showQueryError") echo "show"; ?>">
            <?php
            /* if there an error in query print it */
            if ($errorDB === "showSame") echo "X Current Password equal New Password ! X<br>Please Try again ...";
            else if ($errorDB === "showQueryError") echo "X Error while compiling query ! X<br>Please Try again ...";
            ?>
        </div>
    </form>
</div>
<?php include "partials/footer.php"; ?>
<script>
window.onload = () => {
    // in load auto focus to first input
    let firstInput = document.querySelector("#oldPw");
    if (firstInput.value !== '')
        firstInput.value = firstInput.value + " ";
    firstInput.focus();
    firstInput.value = firstInput.value.trim();
}
// toggle button to show password
let passwordToggleBTN = document.querySelectorAll(".passwordToggleBTN");
passwordToggleBTN.forEach((e) => {
    let passwordInput = e.parentElement.firstElementChild;
    e.addEventListener("click", () => {
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