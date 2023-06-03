<?php
include 'partials/constants.php';

// comments here the same of home page comments

$id = 0;
$role = -1;

include 'partials/userClass.php';
$objUser = new User();

if (isset($_GET['id'])) {
    if (!isset($_SESSION['userID'])) {
        unset($_SESSION['timeSession']);
        session_destroy();
        header("location:logInPage.php");
    } else {
        if ($_SESSION['userID'] != htmlspecialchars($_GET['id'])) {
            $id = $_SESSION['userID'];
            header("location:manageUser.php?id=$id");
        } else {
            $id = htmlspecialchars($_GET['id']);
            $role = $objUser->getRole($id);
        }
    }
} else if (isset($_SESSION['userID'])) {
    $id = $_SESSION['userID'];
    header("location:manageUser.php?id=$id");
} else {
    header("location:logInPage.php");
}

if ($role == -1) {
    header("location:logInPage.php");
}
if (isset($_SESSION['timeSession'])) {
    if ($_SESSION['timeSession'] <= time()) {
        header("location:logInPage.php");
        unset($_SESSION['timeSession']);
        unset($_SESSION['userID']);
    }
}

$loggedInUserId = $_SESSION['userID'];
$roleLoggedInUser = $objUser->getRole($loggedInUserId);
$userLoggedInName = $objUser->getName($id);
$userLoggedInImagePath = $objUser->getImageName($loggedInUserId);

$searchFor = "";
if (isset($_GET['search_for'])) {
    $searchFor = htmlspecialchars($_GET['search_for']);
} else {
    $id = $_SESSION['userID'];
    header("location:manageUser.php?id=$id");
}
if (isset($_POST['submit'])) {
    $searchFor = htmlspecialchars(trim($_POST['name']));
    if ($searchFor != '') {
        header("location:searchPage.php?id=$loggedInUserId&search_for=$searchFor");
    } else {
        echo "<script>setTimeout(() => {alert('X You must Enter a Name! X')}, 500);</script>";
    }
}

$thirdChildOfHeader = '<div class="reg">
            <a href="manageUser.php" class="link">Home</a>
        </div>';
$pageTitle = "Search Page | $userLoggedInName";
include "partials/header.php";

?>
<div class="container">
    <i class="search fa-solid fa-magnifying-glass"></i>
    <i class="search fa-solid fa-x"></i>
    <h3>You searched for '<?= $searchFor ?>' </h3>
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
            $users = $objUser->getAllUsers();
            for ($i = 0; $i < count($users); $i++) {
                $trId = $users[$i]['id'];
                $trName = $users[$i]['name'];
                // if the searched for world is in (or part of) the name of username then display this user
                if (!strstr(strtolower($trName), strtolower($searchFor)))
                    continue;
                $trRole = $users[$i]['role'];
                $trImageName = $users[$i]['image_name'];
                echo "<tr>";
                echo "<td>$trId</td>";
                echo "<td><img src='images/$trImageName' alt=''/></td>";
                echo "<td>$trName</td>";
                $doActionForUpdateANDChange = $objUser->compareRolesForUpdateDataANDChangePassword($loggedInUserId, $roleLoggedInUser, $trId, $trRole);

                echo "<td><a href='profile.php?id=$trId' class='can' style='--number-bc-color:rgb(73, 197, 97);'>Profile</a></td>";
                if ($doActionForUpdateANDChange == "can") {
                    echo "<td><a target='_blank' href='updateData.php?id=$trId' class='can update' style='--number-bc-color:rgb(17, 167, 167);'>Update Data</a></td>";
                } else {
                    echo "<td><a title='No Permeation' style='--number-bc-color:rgb(17, 167, 167); opacity: 0.3; cursor: auto'>Update Data</a></td>";
                }
                if ($doActionForUpdateANDChange == "can") {
                    echo "<td><a target='_blank' href='changePassword.php?id=$trId' class='can change' style='--number-bc-color:rgb(115, 77, 253);'>Change Password</a></td>";
                } else {
                    echo "<td><a title='No Permeation' style='--number-bc-color:rgb(115, 77, 253); opacity: 0.3; cursor: auto'>Change Password</a></td>";
                }
                $doActionForDeleting = $objUser->compareRolesForDeleting($trRole);
                $doActionDelete = "";
                if ($doActionForUpdateANDChange == "can" && $doActionForDeleting == "can") {
                    echo "<td><a target='_blank' href='deletingPage.php?id=$trId' class='can delete' style='--number-bc-color:rgb(201, 38, 38);'>Delete</a></td>";
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