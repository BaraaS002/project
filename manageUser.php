<?php
include 'partials/constants.php';

// HOME PAGE

$id = 0;
$role = -1;

// call class user
include 'partials/userClass.php';
// make an object
$objUser = new User();

// logged-in user must be in get
if (isset($_GET['id'])) {
    // if no user logged in then go log in page
    if (!isset($_SESSION['userID'])) {
        unset($_SESSION['timeSession']);
        session_destroy();
        header("location:logInPage.php");
    } else {
        // user id in get must equal logged-in user in session, if not go to home page with id in get (url security <discuss in discussion>)
        if ($_SESSION['userID'] != htmlspecialchars($_GET['id'])) {
            $id = $_SESSION['userID'];
            header("location:manageUser.php?id=$id");
        } else {
            // if all conditions up is good then but id in id variable, and his role in role variable
            $id = htmlspecialchars($_GET['id']);
            $role = $objUser->getRole($id);
        }
    }
} else if (isset($_SESSION['userID'])) {
    // if no id in get and there is user logged-in, go to home page with his id in get
    $id = $_SESSION['userID'];
    header("location:manageUser.php?id=$id");
} else {
    // no logged-in user in get, go log in page
    header("location:logInPage.php");
}
// role = -1 , mean id of user not in db
if ($role == -1) {
    header("location:logInPage.php");
}
// check if time of session end or not, if end log out the user that logged-in and go log in page
if (isset($_SESSION['timeSession'])) {
    if ($_SESSION['timeSession'] <= time()) {
        unset($_SESSION['timeSession']);
        unset($_SESSION['userID']);
        header("location:logInPage.php");
    }
}
// if sign up process is done show alert for it
if (isset($_GET['registered'])) {
    echo "<script>setTimeout(() => {alert('✔ Signed Up Done ✔')}, 500);</script>";
}
// if delete process is done show alert for it
if (isset($_GET['deleted'])) {
    echo "<script>setTimeout(() => {alert('✔ Deleting Done ✔')}, 500);</script>";
}
// if change password process is done show alert for it
if (isset($_GET['changed'])) {
    echo "<script>setTimeout(() => {alert('✔ Password Changed ✔')}, 500);</script>";
}
// log in user id and his id will put them in valuables to use them later
$loggedInUserId = $_SESSION['userID'];
$roleLoggedInUser = $objUser->getRole($loggedInUserId);
// user id in get and his id will put them in valuables to use them later
$userLoggedInName = $objUser->getName($id);
$userLoggedInImagePath = $objUser->getImageName($loggedInUserId);

// search form check
$searchFor = "";
// if user put submit button
if (isset($_POST['submit'])) {
    $searchFor = htmlspecialchars(trim($_POST['name']));
    // if value that searched for is empty then show alert to tell him, else go to search page
    if ($searchFor != '') {
        header("location:searchPage.php?id=$loggedInUserId&search_for=$searchFor");
    } else {
        echo "<script>setTimeout(() => {alert('X You must Enter a Name! X')}, 500);</script>";
    }

}

$thirdChildOfHeader = '<div class="user con ch">
            <span class="ch"><img src="images/' . $userLoggedInImagePath . '" alt="" class="ch"><i
                    class="fa-solid fa-angle-down ch"></i></span>
        <ul>
            <li class=""><a target="_blank" href="profile.php?id=' . $loggedInUserId . '">Profile</a></li>
            <li class=""><a href="logOutPage.php">LogOut</a></li>
        </ul>
    </div>';
$pageTitle = "Manage Users | $userLoggedInName";
include "partials/header.php";

?>
<div class="container">
    <i class="search fa-solid fa-magnifying-glass"></i>
    <i class="search fa-solid fa-x"></i>
    <h3>All Users</h3>
    <div class="searchCon">
        <form action="" method="post" class="hidden">
            <input id="name" name="name" type="text" placeholder="Searching for User ... "/>
            <input type="submit" value="Search" name="submit" class="btnSearch">
        </form>
    </div>
    <div class="table" style="overflow-x: auto;">
        <table class="">
            <thead>
            <tr>
                <td>#</td>
                <td>Profile Image</td>
                <td>Name</td>
                <td>Show</td>
                <td>Update Data</td>
                <td>Update Password</td>
                <td>Delete</td>
            </tr>
            </thead>
            <tbody>
            <?php
            // get all users using user class
            $users = $objUser->getAllUsers();
            // for loop in the result
            for ($i = 0; $i < count($users); $i++) {
                // get data that will display
                $trId = $users[$i]['id'];
                $trName = $users[$i]['name'];
                $trRole = $users[$i]['role'];
                $trImageName = $users[$i]['image_name'];
                echo "<tr>";
                echo "<td>$trId</td>";
                echo "<td><img src='images/$trImageName' alt=''/></td>";
                echo "<td>$trName</td>";
                // compare roles that described in user class
                $doActionForUpdateANDChange = $objUser->compareRolesForUpdateDataANDChangePassword($loggedInUserId, $roleLoggedInUser, $trId, $trRole);

                echo "<td><a href='profile.php?id=$trId' class='can' style='--number-bc-color:rgb(73, 197, 97);'>Profile</a></td>";
                // if value 'can' returned from compare method then preemption is ok
                if ($doActionForUpdateANDChange == "can") {
                    echo "<td><a target='_blank' href='updateData.php?id=$trId' class='can update' style='--number-bc-color:rgb(17, 167, 167);'>Update Data</a></td>";
                } else {
                    echo "<td><a title='No Permeation' style='--number-bc-color:rgb(17, 167, 167); opacity: 0.3; cursor: auto'>Update Data</a></td>";
                }
                // if value 'can' returned from compare method then preemption is ok
                if ($doActionForUpdateANDChange == "can") {
                    echo "<td><a target='_blank' href='changePassword.php?id=$trId' class='can change' style='--number-bc-color:rgb(115, 77, 253);'>Change Password</a></td>";
                } else {
                    echo "<td><a title='No Permeation' style='--number-bc-color:rgb(115, 77, 253); opacity: 0.3; cursor: auto'>Change Password</a></td>";
                }
                // if value 'can' returned from compare method, and delete method returned 'can' then preemption is ok
                $doActionForDeleting = $objUser->compareRolesForDeleting($trRole);
                $doActionDelete = "";
                if ($doActionForUpdateANDChange == "can" && $doActionForDeleting == "can") {
                    echo "<td><a target='_blank' href='deletingPage.php?id=$trId&idLoggedIn=$loggedInUserId' class='can delete' style='--number-bc-color:rgb(201, 38, 38);'>Delete</a></td>";
                } else {
                    echo "<td><a title='No Permeation' style='--number-bc-color:rgb(201, 38, 38); opacity: 0.3; cursor: auto'>Delete</a></td>";
                }

                echo "</tr>";
            }

            ?>
            </tbody>
        </table>
    </div>
</div>
<?php include "partials/footer.php"; ?>
<script>
    // ul open close code subnet
    let links = document.querySelector(".con");

    document.addEventListener("click", (e) => {
        if (!e.target.classList.contains("ch")) {
            links.classList.remove("active");
            // console.log(e.target);
        }
    });

    links.addEventListener("click", () => {
        links.classList.toggle("active");
        // if (e.classList.contains("active")) {
        // links.forEach((e2) => {
        //     e2.classList.remove("active");
        // });
        //    e.classList.add("active");
        // }
    });
    // search and close searching buttons code subnet
    let searchCon = document.querySelector(".searchCon form");
    let search = document.querySelectorAll(".container .search");
    search.forEach((e) => {
        e.addEventListener("click", () => {
            if (e.classList.contains("fa-x")) {
                searchCon.classList.add("hidden");
                document.querySelector(".container .search.fa-x").style.display = "none";
                document.querySelector(".container .search.fa-magnifying-glass").style.display = "block";
            } else if (e.classList.contains("fa-magnifying-glass")) {
                searchCon.classList.remove("hidden");
                document.querySelector("#name").focus();
                document.querySelector(".container .search.fa-x").style.display = "block";
                document.querySelector(".container .search.fa-magnifying-glass").style.display = "none";
            }
        });
    });
</script>
</body>

</html>