<?php

include_once "db_connection.php";
include "helperFunctions.php";

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the user ID from the URL
$targetUserId = $_GET['target_user_id'];
$showingAdoreButton = $_GET['showingAdoreButton'];

showProfileCard($targetUserId);