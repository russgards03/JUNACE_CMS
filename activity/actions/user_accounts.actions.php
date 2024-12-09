<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../models/user_accounts.php';
include_once '../connDb.php';

$user_accounts = new User_accounts($connDb);

if (isset($_POST["register"])) {
    echo "Registration process started"; // Debugging line
    if (
        empty($_POST["username"]) ||
        empty($_POST["user_display_name"]) ||
        empty($_POST["user_email_address"]) ||
        empty($_POST["user_password"])
    ) {
        header("location: ../register.php?msg=required-fields");
        exit();
    } else {
        $count = $user_accounts->checkEmail($_POST["user_email_address"]);
        if ($count > 0) {
            header("location: ../register.php?msg=duplicate-email");
            exit();
        }

        $salt = uniqid();

        $user_accounts->username = $_POST["username"];
        $user_accounts->user_display_name = $_POST["user_display_name"];
        $user_accounts->user_email_address = $_POST["user_email_address"];
        $user_accounts->user_salt = $salt; // Optional field
        $user_accounts->user_password = password_hash($_POST["user_password"], PASSWORD_DEFAULT);
        $user_accounts->save();

        header("location: ../register.php?msg=register-successful");
        exit();
    }
} elseif (isset($_POST["login"])) {
    echo "Login process started"; // Debugging line
    $validate = $user_accounts->validateUser($_POST["username"], $_POST["password"]);
    if ($validate) {
        header("location: ../dashboard.php");
        exit();
    } else {
        header("location: ../index.php?msg=error-login");
        exit();
    }
} else {
    echo "No action specified"; // Debugging line
}
?>