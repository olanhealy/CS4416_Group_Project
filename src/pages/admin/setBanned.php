<?php

include_once "adminHelperFunctions.php";
include_once "../helpers/db_connection.php";

adminAccessCheck();

//if the request is a post request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset ($_SESSION['targetId'])) {

        //assigns the targetId to the value of the session
        $targetId = $_SESSION['targetId'];

        //if ban, sets ban. If unban sets unban
        if ($_POST['action'] == 'unban') {
            setBanned($targetId, 0);
        } elseif ($_POST["action"] == "ban") {
            setBanned($targetId, 1);
        }

        // Redirect to userListAdmin.php
        header("Location: usersListAdmin.php");
        exit();
    } else {
        echo "Target ID is not set.";
        exit();
    }
}