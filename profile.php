<?php
include 'partials/constants.php';

$id = 0;
$loggedInUserId = 0;
$role = -2;

// call user class
include 'partials/userClass.php';
// object of it
$objUser = new User();

// profile of user u want must be in get
if (isset($_GET['id'])) {
    // if no user logged in then go log in page
    if (!isset($_SESSION['userID'])) {
        unset($_SESSION['timeSession']);
        session_destroy();
        header("location:logInPage.php");
    } else {
        // put id of this profile in id variable, and his role in role variable
        $id = htmlspecialchars($_GET['id']);
        $role = $objUser->getRole($id);
    }
} else if (isset($_SESSION['userID'])) {
    // if no id in get and there is user logged in, go to profile of user logged-in with his id in get
    $loggedInUserId = $_SESSION['userID'];
    header("location:profile.php?id=$loggedInUserId");
} else {
    // else go to log in page (no user logged in)
    header("location:logInPage.php");
}
$loggedInUserId = $_SESSION['userID'];
// id of this profile not in db, go to profile of user logged-in with his id in get
if ($role == -1) {
    header("location:profile.php?id=$loggedInUserId");
}
// if u update data show alert
if (isset($_GET['updated'])) {
    echo "<script>setTimeout(() => {alert('✔ Updated Done ✔')}, 500);</script>";
}
$name = $objUser->getName($id);
$email = $objUser->getEmail($id);
$image_name_for_header = $objUser->getImageName($loggedInUserId);
$image_name = $objUser->getImageName($id);
// get first name of username(if his name have a full name)
$fName = explode(" ", $name)[0];

// role described in user class
if ($role == 0)
    $roleName = "Administrator";
else if ($role == 1)
    $roleName = "Admin";
else
    $roleName = "User";

$roleLoggedInUser = $objUser->getRole($loggedInUserId);
$doActionForUpdateANDChange = $objUser->compareRolesForUpdateDataANDChangePassword($loggedInUserId, $roleLoggedInUser, $id, $role);
$doActionForDeleting = $objUser->compareRolesForDeleting($role);
$doActionDelete = "";
if ($doActionForUpdateANDChange == "can" && $doActionForDeleting == "can") {
    $doActionDelete = "can";
} else {
    $doActionDelete = "cannot";
}

$thirdChildOfHeader = '<div class="user con ch">
            <span class="ch"><img src="images/' . $image_name_for_header . '" alt="" class="ch"><i
                    class="fa-solid fa-angle-down ch"></i></span>
            <ul>
                <li class=""><a href="manageUser.php">Home</a></li>
                <li class=""><a href="logOutPage.php">LogOut</a></li>
            </ul>
        </div>';
$pageTitle = "Profile | $name";
include "partials/header.php";
?>
<div class="container profile">
    <h3>Hi <?php echo $fName; ?> (●'◡'●)</h3>
    <div class="data-con">
        <div class="imgAndBtns">
            <div class="img">
                <img src="images/<?php echo $image_name; ?>" alt="">
            </div>
            <div class="btns">
                <?= /* if roles good then display this button */
                $doActionForUpdateANDChange == "can" ? "<a class='can' style='--number-bc-color:rgb(17, 167, 167);' href='updateData.php?id=$id'>Update Data</a>" : "" ?>
                <?= /* if roles good then display this button */
                $doActionForUpdateANDChange == "can" ? "<a class='can' style='--number-bc-color:rgb(115, 77, 253);' href='changePassword.php?id=$id'>Change Password</a>" : "" ?>
                <?= /* if roles good then display this button */
                $doActionDelete == "can" ? "<a class='delete can' style='--number-bc-color:rgb(201, 38, 38);' href='deletingPage.php?id=$id&idLoggedIn=$loggedInUserId' target='_blank'>Delete</a>" : "" ?>
            </div>
        </div>
        <div class="data">
            <h4><?php echo $name; ?></h4>
            <span>Email : <?php echo $email; ?></span>
            <div class="role">Role : <?php echo $roleName; ?></div>
            <div class="roles">
                <ul>- Some Roles <?php echo $name; ?> can use :
                    <li class="">Change his password.</li>
                    <li class="">Update his data.</li>
                    <li class="<?php /* roles show when is an admin oe user and hided when administrator */
                    $roleName == 'Administrator' ? print 'hide' : ''; ?>">Delete his account.
                    </li>
                    <li class="<?php /* roles show when is an admin and hided when user */
                    $roleName == 'User' ? print 'hide' : ''; ?>">Change any user or admin password.
                    </li>
                    <li class="<?php /* roles show when is an admin and hided when user */
                    $roleName == 'User' ? print 'hide' : ''; ?>">Update any user or admin data.
                    </li>
                    <li class="<?php /* roles show when is an admin and hided when user */
                    $roleName == 'User' ? print 'hide' : ''; ?>">Delete any user or admin account.
                    </li>
                    <li class="<?php /* roles show when is an admin and hided when user */
                    $roleName == 'User' ? print 'hide' : ''; ?>">Set any user as admin.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php include "partials/footer.php"; ?>
<script>
    // ul code subnet
    let links = document.querySelector(".con");

    document.addEventListener("click", (e) => {
        if (!e.target.classList.contains("ch")) {
            links.classList.remove("active");
            // console.log(e.target);
        }
    });

    links.addEventListener("click", () => {
        links.classList.toggle("active");
    });
</script>
</body>

</html>