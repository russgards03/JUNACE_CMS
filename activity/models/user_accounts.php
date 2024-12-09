<?php


class User_accounts {
    public $id;
    public $username;
    public $user_email_address;
    public $user_display_name;
    public $user_password;
    public $user_salt;
    public $user_date_registered;

    private $connDb;

    function __construct($connDb) {
        $this->connDb = $connDb;
    }

    function save() {
        try {
            if (empty($this->id)) {
                $sql = "INSERT INTO user_accounts (username, user_email_address, user_display_name, user_password, user_salt) 
                        VALUES ('".$this->username."', '".$this->user_email_address."', '".$this->user_display_name."', '".$this->user_password."', '".$this->user_salt."')";
            } else {
                $sql = "UPDATE user_accounts SET username='".$this->username."', user_email_address='".$this->user_email_address."', 
                        user_display_name='".$this->user_display_name."', user_password='".$this->user_password."', user_salt='".$this->user_salt."' 
                        WHERE id='".$this->id."'";
            }
            mysqli_query($this->connDb, $sql) or die (mysqli_error($this->connDb));
        } catch(Exception $ex) {
            echo $ex->getMessage();
        }
    }

    function checkEmail($email) {
        try {
            $sql = "SELECT COUNT(id) FROM user_accounts WHERE user_email_address = '".$email."'";
            $result = mysqli_query($this->connDb, $sql) or die (mysqli_error($this->connDb));
            $row = mysqli_fetch_row($result);

            return $row[0];
        } catch(Exception $ex) {
            echo $ex->getMessage();
        }
    }

    function getAll() {
        try {
            $sql = "SELECT * FROM user_accounts";
            $result = mysqli_query($this->connDb, $sql) or die (mysqli_error($this->connDb));
            $users = [];
            while ($row = mysqli_fetch_object($result)) {
                $users[] = $row;
            }
            return $users;
        } catch(Exception $ex) {
            echo $ex->getMessage();
        }
    }

    function getSingle($id) {
        try {
            $sql = "SELECT * FROM user_accounts WHERE id = '".$id."'";
            $result = mysqli_query($this->connDb, $sql) or die (mysqli_error($this->connDb));
            return mysqli_fetch_object($result);
        } catch(Exception $ex) {
            echo $ex->getMessage();
        }
    }

    function delete($id) {
        try {
            $sql = "DELETE FROM user_accounts WHERE id = '".$id."'";
            mysqli_query($this->connDb, $sql) or die (mysqli_error($this->connDb));
        } catch(Exception $ex) {
            echo $ex->getMessage();
        }
    }

    function validateUser($username, $password) {
        try {
            $return = false;

            // Query to check if the user exists
            $sql_set = "SELECT * FROM user_accounts WHERE username = '".$username."'";
            $qry_set = mysqli_query($this->connDb, $sql_set) or die (mysqli_error($this->connDb));
            $row_set = mysqli_fetch_object($qry_set);

            // Check if the user was found
            if (!$row_set) {
                echo "User not found.";
                return false;
            }

            // Use password_verify to check the password
            if (password_verify($password, $row_set->user_password)) {
                session_start();
                $_SESSION['id'] = $row_set->id;
                $_SESSION['username'] = $row_set->username;
                $_SESSION['user_display_name'] = $row_set->user_display_name;

                $return = true; // Successful login
            } else {
                echo "Password does not match.";
            }

            return $return;
        } catch(Exception $ex) {
            echo $ex->getMessage();
        }
    }
}
?>