<?php
include 'partials/constants.php';

$name = "";
// this page to confirm or cancel delete request
// call class user
include "partials/userClass.php";
$objUser = new User();

$roleForIdInGet = 0;
$id = 0;
$loggedInUserId = 0;
$roleForIdLoggedIn = 0;

// if no user log in do log in page
if (!isset($_SESSION['userID'])) {
    header("location:logInPage.php");
}
// must id for deleting and logged-in user in get
else if (isset($_GET['id']) && isset($_GET['idLoggedIn'])) {
    $id = htmlspecialchars($_GET['id']);
    $loggedInUserId = htmlspecialchars($_GET['idLoggedIn']);
    // if logged-in user not equal in the session then back (validate url)
    if ($loggedInUserId != $_SESSION["userID"])
        header("location:manageUser.php");
    $roleForIdInGet = $objUser->getRole($id);
    $name = $objUser->getName($id);
    $roleForIdLoggedIn = $objUser->getRole($loggedInUserId);
    // mean that there is no user with this id
    if ($roleForIdInGet == -1) {
        header("location:manageUser.php");
    }
}
// if no id to the user want to delete and user logged in in get, then go home
else {
    header("location:manageUser.php");
}
$thirdChildOfHeader = '<div class="reg">
            <a href="" class="link">Home</a>
        </div>';
$pageTitle = "Deleting page | $name";
include "partials/header.php";

?>
<div class="container form">
    <div class="deleting-dialog">
        <div class="deleting-dialog-cont sec">
            <span>Are you sure you want to delete this account ?</span>
            <div class="btns">
                <button class="delete"
                        onclick="<?php echo "window.open('redirectForDelete.php?id=$id&loggedId=$loggedInUserId','_self')"; ?>">
                    Delete
                </button>
                <button class="close" onclick="<?php /* go to the delete page when confirm */ echo "window.close()"; ?>">Close</button>
            </div>
        </div>
    </div>
</div>
<?php include "partials/footer.php"; ?>
<script>
    // back to home page when cancel
    document.querySelectorAll(".link").forEach((e) => {
        e.addEventListener("click", () => {
            window.close();
            window.open("manageUser.php")
        });
    });
</script>
</body>

</html>