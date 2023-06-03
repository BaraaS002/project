<?php
// page for deleting only and display nothing
include 'partials/constants.php';
// call class user
include "partials/userClass.php";
$objUser = new User();
// must id for deleting and logged-in user in get, else go home page
if (isset($_GET["id"]) && isset($_GET["loggedId"])) {
    $id = htmlspecialchars($_GET["id"]);
    $loggedId = htmlspecialchars($_GET["loggedId"]);
    // if logged-in user not equal in the session then back (validate url)
    if ($loggedId != $_SESSION["userID"]) {
        header("location:manageUser.php");
    } else {
        $role = $objUser->getRole($id);
        // mean that there is no user with this id
        if ($role == -1) {
            header("location:manageUser.php");
        }
        $roleLoggedIn = $objUser->getRole($loggedId);
        // mean that there is no user with this id
        if ($roleLoggedIn == -1) {
            header("location:manageUser.php");
        }
        // role described in line 468 in userClass
        $doActionForUpdateANDChange = $objUser->compareRolesForUpdateDataANDChangePassword($loggedId, $roleLoggedIn, $id, $role);
        $doActionForDeleting = $objUser->compareRolesForDeleting($role);
        // must be can from two methods to delete(first compare roles, second that validate id not for administrator)
        if ($doActionForUpdateANDChange == "can" && $doActionForDeleting == "can") {
            $objUser->executeDeleting($id, $loggedId);
        } else {
            header("location:manageUser.php");
        }
    }
} else
    header("location:manageUser.php");
?>