<?php

class User       
{
    private $conn; 
// constructor to connect to db in object created
    public function __construct()
    {
       
        define("servername", 'localhost');
        define("username", 'root');
        define("password", '');
        define("dbname", 'project');

        $this->conn = mysqli_connect(servername, username, password, dbname,3308);
        if (mysqli_connect_error()) {
            echo "connection error";
        }
    }

    // validate name that pass as parameter
    private function checkName($un)
    {
        if ($un == '') {
            return 'You must Enter a Name!';
        } else if (!is_string($un)) {
            return 'Enter a valid Name!';
        } else if (strlen($un) < 4) {
            return 'Name length must be greater than 4 characters!';
        } else {
            return "AllGood";
        }
    }

    // validate email that pass as parameter
    private function checkEmail($em)
    {
        if ($em == '') {
            return 'You must Enter an Email!';
            //    } else if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\\.[a-zA-Z]{3}/", $em)) {
        } else if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
            return 'Enter a valid email!';
        } else {
            return "AllGood";
        }
    }

    // check if email in sign up in db or not, if yes error will display
    private function checkRepeatingEmail($em)
    {
        $idForNewUser = 0;
        $res = mysqli_query($this->conn, "select * from users");
        while ($row = mysqli_fetch_assoc($res)) {
            if ($em == $row['email'])
                return 'Email is already exist in database!';
                $idForNewUser=$row['id']+1; 
        }
        // the id that after the last id in db
        return $idForNewUser;
    }

    // validate password in log in that pass as parameter
    private function checkOldPassword($pw)
    {
        if ($pw == '') {
            return 'You must Enter a Password!';
        } else if (strlen($pw) < 8) {
            return 'Password length must be greater than or equal 8!';
        } else {
            return "AllGood";
        }
    }

    // validate password in sign up that pass as parameter
    private function checkNewPassword($pw)
    {
        if ($pw == '') {
            return 'You must Enter a Password!';
        } else if (strlen($pw) < 8) {
            return 'Password length must be greater than or equal 8!';
        } else if (!preg_match("/[a-z]/", $pw) || !preg_match("/[A-Z]/", $pw) || !preg_match("/[0-9]/", $pw)) {
            return 'Password must include lower case characters, upper case characters and numbers.';
        } else {
            return "AllGood";
        }
    }

    // validate password in input with the password in db
    private function checkCurrentPassword($id, $pw)
    {
        if ($pw == '') {
            return 'You must Enter a Password!';
        } else if (strlen($pw) < 8) {
            return 'Password length must be greater than or equal 8!';
        } else {
            // hash password to compare hashed password in db with it
            $pwMD5 = md5($pw);
            $stmt = $this->conn->prepare("select password from users where id=?");// prepare stmt to protect from sql injection 
            $stmt->bind_param("i", $id);// bind parameters to stmt to protect from sql injection
            $stmt->execute();// execute stmt
            $res = $stmt->get_result();// get result from stmt
            $row = mysqli_fetch_assoc($res);// fetch a result row as an associative array
            
            if ($row['password'] != $pwMD5) {// if password in db not equal password in input
                return 'Password must equal Current Password.';
            } else {
                return "AllGood";
            }
        }
    }

    // validate new password with configuration password
    private function checkConfirmationPassword($cp, $pw)
    {
        if ($cp == '') {
            return 'You must Enter a Confirmation Password!';
        } else if (strlen($cp) < 8) {
            return 'Confirmation Password length must be greater than or equal 8!';
        } else if ($cp !== $pw) {
            return 'Password and Confirmation Password are not the Same!';
        } else {
            return "AllGood";
        }
    }

    // validate image that pass as parameter
    private function checkImage($image) 
    {
        if (!$image['image']['name'] == "") {
            if ($image['image']['size'] > (1024 * 1024) / 2) {
                return 'Image size must be less than 0.5MB!';
            } else {
                return "AllGood";
            }
        }
    }

    // log in method
    public function logIn($em, $pw)
    {
        $errors = []; 

        // validate inputs

        // validate email 
        $emailError = $this->checkEmail($em);
        if ($emailError !== "AllGood")
            $errors["email"] = $emailError; 

        // validate password
        $passwordError = $this->checkOldPassword($pw);
        if ($passwordError !== "AllGood")
            $errors["password"] = $passwordError;

        // if no errors
        if (count($errors) == 0) {
            // hash a password
            $pwForDB = md5($pw);
            // prepare stmt to protect from sql injection
            $stmt = $this->conn->prepare("select * from users where email = ? and password = ?");
            $stmt->bind_param("ss", $em, $pwForDB);
            $stmt->execute();
            $res = $stmt->get_result();
            // rows more than 0, mean one user in db his email and password equal in inputs and go to home page
            if (mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                $idUser = $row['id'];
                // put id that logged-in session
                $_SESSION['userID'] = $idUser;
                // time session is just 1 hour
                $_SESSION['timeSession'] = time() + (60 * 60);
                // go to manage user page
                header("location:manageUser.php?id=$idUser");
            } else {
                // no user in db his email and password equal in inputs, will display an error massage
                return ["noMatch" => "show"];
            }
        } else {
            // if there an errors will return it to user
            return $errors;
        }
    }

    // sign up method
    public function register($un, $em, $pw, $cp, $image, $image_name)
    {
        $errors = [];
        $idForNewUser = 0;

        // validate inputs //

        // validate user name
        $nameError = $this->checkName($un);
        if ($nameError !== "AllGood")
            $errors["name"] = $nameError;

        // validate email
        $emailError = $this->checkEmail($em);
        if ($emailError !== "AllGood")
            $errors["email"] = $emailError;

        // validate repeating email
        $emailError = $this->checkRepeatingEmail($em);
        if (!is_int($emailError))
            $errors["email"] = $emailError;
        else
            $idForNewUser = $emailError;

        // validate password
        $passwordError = $this->checkNewPassword($pw);
        if ($passwordError !== "AllGood")
            $errors["password"] = $passwordError;

        // validate confirmation password
        $confirmationPasswordError = $this->checkConfirmationPassword($cp, $pw);
        if ($confirmationPasswordError !== "AllGood")
            $errors["conPass"] = $confirmationPasswordError;

        // check if the image is the default image
        if (!strstr($image_name, "avatar")) {
            // validate image
            $imageError = $this->checkImage($image);
            if ($imageError !== "AllGood")
                $errors["image"] = $imageError;
        }
        // if no errors will insert the new user in db
        if (count($errors) == 0) {
            $pwForDB = md5($pw);
            // check if the image is the default image
            if (!strstr($image_name, "avatar")) {
                // move image from tmp to image folder in project
                if (!move_uploaded_file($_FILES['image']['tmp_name'], getcwd() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . pathinfo($_FILES['image']['name'], PATHINFO_FILENAME) . "_" . $un . "." . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION))) {// if there an error in moving image to image folder in project
                    echo 'Error while uploading the Image.';
                }
            }
            // make the new user role 2, mean it will be a user
            if ($idForNewUser==0){
                $role = 0;
                $idForNewUser++;
            }
            else 
                $role = 2;
            // time signed up
            $createdAt = date("y/m/d h:i:s", time() + (60 * 60));

            $stmt = $this->conn->prepare("insert into users set id = ?, name = ?, email = ?, password = ?, role = ?, image_name = ?, created_at = ?");
            $stmt->bind_param("isssiss", $idForNewUser, $un, $em, $pwForDB, $role, $image_name, $createdAt);
            $stmt->execute();
            // check if there is an error in query or not
            if ($stmt) {
                $_SESSION['userID'] = $idForNewUser;
                $_SESSION['timeSession'] = time() + 60 * 60;
                // if no error go to home page
                header("location:manageUser.php?id=$idForNewUser&registered=true");
            } else {
                // if error display it
                return ["errorDB" => "show"];
            }
        } else {
            return $errors;
        }
    }

    // update method
    public function updateData($id, $un, $em, $ro, $image, $new_image_name)
    {
        $errors = [];

        // validate inputs

        // validate user name
        $nameError = $this->checkName($un);
        if ($nameError !== "AllGood")
            $errors["name"] = $nameError;

        // validate email
        $emailError = $this->checkEmail($em);
        if ($emailError !== "AllGood")
            $errors["email"] = $emailError;

        $old_image_name = $this->getImageName($id);
        // if the image is the same don't check it and don't move it to the image folder
        if ($old_image_name != $new_image_name) {
            // validate image
            $imageError = $this->checkImage($image);
            if ($imageError !== "AllGood") {
                $errors["image"] = $imageError;
            } else {
                if (!move_uploaded_file($_FILES['image']['tmp_name'], getcwd() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . pathinfo($_FILES['image']['name'], PATHINFO_FILENAME) . "_" . $un . "." . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION))) {
                    echo 'Error while uploading the Image.';
                }
            }
        }

        if (count($errors) == 0) {
            // if role is as string and equal admin put role to the db 1, else put role 2
            if (is_int($ro))
                $role = $ro;
            else
                $role = ($ro == "admin") ? 1 : 2;

            $stmt = $this->conn->prepare("update users set name = ?, email = ?, role = ?, image_name = ? where id = ?");
            $stmt->bind_param("ssisi", $un, $em, $role, $new_image_name, $id);
            $stmt->execute();
            if ($stmt) {
                // if no error back and say done
                return ["Done" => "done"];
            } else {
                return ["errorDB" => "show"];
            }
        } else {
            return $errors;
        }
    }

    // change password method
    public function changePassword($id, $oldPw, $newPw, $newConPw)
    {
        $errors = [];

        // validate inputs

        // validate old password
        $passwordError = $this->checkCurrentPassword($id, $oldPw);
        if ($passwordError !== "AllGood")
            $errors["oldPw"] = $passwordError;

        // validate new password
        $passwordError = $this->checkNewPassword($newPw);
        if ($passwordError !== "AllGood")
            $errors["newPw"] = $passwordError;

        // validate confirmation password
        $confirmationPasswordError = $this->checkConfirmationPassword($newPw, $newConPw);
        if ($confirmationPasswordError !== "AllGood")
            $errors["newConPw"] = $confirmationPasswordError;

        if (count($errors) == 0) {
            // if current pass equal the new, mean no meaning to execute the query
            if ($oldPw == $newPw)
                return ["errorDB" => "showSame"];
            else {
                // hash new password
                $newPwForDB = md5($newPw);
                $stmt = $this->conn->prepare("update users set password = ? where id = ?");
                $stmt->bind_param("si", $newPwForDB, $id);
                $stmt->execute();
                if ($stmt) {
                    return ["Done" => "done"];
                } else {
                    return ["errorDB" => "showQueryError"];
                }
            }
        } else {
            return $errors;
        }
    }


    // execute delete method (deleting method)
    public function executeDeleting($id, $loggedInUserId)
    {
        $stmt = $this->conn->prepare("delete from users where id= ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt) {
            // if the logged in admin deleted an account that not to him will return to home page
            if ($id != $loggedInUserId && $id != $_SESSION["userID"])
                header("location:manageUser.php?id=$loggedInUserId&deleted=true");
            else {
                // if he deleted his account will go to log in page with done massage and no user in session
                session_start();
                session_destroy();
                header("location:logInPage.php?deleted=true");
            }
        } else {
            // if error in executing query go home page
            header("location:manageUser.php");
        }
    }

    // get name from db to specified user given as parameter
    public function getName($id)
    {
        $stmt = $this->conn->prepare("select name from users where id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            return $row['name'];
        } else {
            return "";
        }
    }

    // get email from db to specified user given as parameter
    public function getEmail($id)
    {
        $stmt = $this->conn->prepare("select email from users where id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            return $row['email'];
        } else {
            return "";
        }
    }

    // get image name from db to specified user given as parameter
    public function getImageName($id)
    {
        $stmt = $this->conn->prepare("select image_name from users where id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            return $row['image_name'];
        } else {
            return "";
        }
    }

    // get role from db to specified user given as parameter and return -1 if the id not in db
    public function getRole($id)
    {
        $stmt = $this->conn->prepare("select role from users where id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            return $row['role'];
        } else {
            return -1;
        }
    }

    // get all users from db and return just values that need to display in home page
    public function getAllUsers()
    {
        $users = [];
        $res = mysqli_query($this->conn, "select * from users");
        while ($row = mysqli_fetch_assoc($res))
            $users[] = ["id" => $row['id'], "name" => $row['name'], "role" => $row['role'], "image_name" => $row['image_name']];
        return $users;
    }

    /*
    - validate the roles,
    - I have three roles :
        1- Administrator with value 0
            - this one can do any think to any user or admin, but he cannot delete his account
        2- Admin with value 1
            - this one can do any think to any user or admin, but he cannot do think to another admins
        3- User with value 2
            - this one can do any think his account only, he cannot do think to another users
    */
    public function compareRolesForUpdateDataANDChangePassword($idLoggedIn, $roleLoggedIn, $idInGet, $roleInGet)
    {
       
        // if logged in is an administrator then he can update and change password to any user or admin
        if ($roleLoggedIn == 0) {
            return "can";
        } // if logged in is an admin then he can update and change password to any user or admin, but not the administrator
        else if ($roleLoggedIn == 1) {
            if ($roleInGet == 0)
                return 'cannot';
            else
                return 'can';
        } // if logged in is a user then he can update and change password to his account
        else if ($roleLoggedIn == 2) {
            // must id that come to validate equal logged in
            if ($idLoggedIn == $idInGet)// if equal then he can update and change password to his account
                return 'can';
            else
                return 'cannot';
        } else {
            return "cannot";
        }
    }

    // if role equal 0 then this account un deletable
    public function compareRolesForDeleting($roleInGet)
    {
        if ($roleInGet == 0) {
            return "cannot";
        } else {
            return "can";
        }
    }
}