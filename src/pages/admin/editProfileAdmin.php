<?php

// Include the helperFunctions.php file
include_once 'adminHelperFunctions.php';
include_once '../helpers/helperFunctions.php';
include '../helpers/db_connection.php';

adminAccessCheck();
//turn on output buffering so output only sent when all code is done and enables it to go striaight to the userList page
ob_start(); 

//var_dump($_SESSION);
if (isset($_SESSION['targetId'])) {
    $targetId = $_SESSION['targetId'];

    // Fetch existing profile information
    $_SESSION['existingName'] = getName($targetId);
    $_SESSION['existingGender'] = getGender($targetId);
    $_SESSION['existingAge'] = getAge($targetId);

    $_SESSION['existingCollegeYear'] = getCollegeYear($targetId);
    $_SESSION['existingCourse'] = getCourse($targetId);

    $_SESSION['existingPursuing'] = getPursuing($targetId);
    $_SESSION['existingLookingFor'] = getLookingFor($targetId);

    $_SESSION['existingBio'] = getBio($targetId);

    $_SESSION['existingHobbies'] = getHobbies($targetId);
    

    // added in to get the existing profile picture
    $_SESSION['existingProfilePic'] = getProfilePicture($targetId);


    include "editProfileAdmin.html";
    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Process form data
        
        // Profile Picture
        if (isset($_FILES['profile_pic']['name']) && $_FILES['profile_pic']['name'] !== $_SESSION['existingProfilePic']) {
            $profilePicFilename = $_FILES['profile_pic']['name'];
            setProfilePic($targetId, $profilePicFilename);
        }


        // First & Last Name
        if ((isset($_POST['first_name']) || isset($_POST['last_name'])) && ($_POST['first_name'] !== explode(' ', $_SESSION['existingName'])[0] || $_POST['last_name'] !== explode(' ', $_SESSION['existingName'])[1])) {

            if ($_POST['first_name'] === explode(' ', $_SESSION['existingName'])[0]) {
                $firstName = explode(' ', $_SESSION['existingName'])[0];
            } else {
                $firstName = $_POST['first_name'];
            }

            if ($_POST['last_name'] === explode(' ', $_SESSION['existingName'])[1]) {
                $lastName = explode(' ', $_SESSION['existingName'])[1];
            } else {
                $lastName = $_POST['last_name'];
            }

            setName($firstName, $lastName, $targetId);
        }

         // Gender
         if (isset($_POST['gender']) && $_POST['gender'] !== $_SESSION['existingGender']) {
            $gender = $_POST['gender'];
            setGender($targetId, $gender);
        }

        // Age
        if (isset($_POST['age']) && $_POST['age'] != $_SESSION['existingAge'] ) {
            $age = $_POST['age'];
            setAge($targetId, $age);
        }

        // Year
        if (isset($_POST['college_year']) && $_POST['college_year'] !== $_SESSION['existingCollegeYear'] ) {
            $college_year = $_POST['college_year'];
            setCollegeYear($targetId, $college_year);
        }

        // Course
        if (isset($_POST['course']) && $_POST['course'] !== $_SESSION['existingCourse']) {
            $course = $_POST['course'];
            setCourse($targetId, $course);
        }

        // Pursuing
        if (isset($_POST['pursuing']) && $_POST['pursuing'] !== $_SESSION['existingPursuing']) {
            $pursuing = $_POST['pursuing'];
            setPursuing($targetId, $pursuing);
        }

        // Looking For
        if (isset($_POST['looking_for']) && $_POST['looking_for'] !== $_SESSION['existingLookingFor']) {
            $lookingFor = $_POST['looking_for'];
            setLookingFor($targetId, $lookingFor);
        }

        // Bio
        if (isset($_POST['bio']) && $_POST['bio'] !== $_SESSION['existingBio']) {
            $bio = $_POST['bio'];
            setBio($targetId, $bio);
        }

        // Hobbies
        if (isset($_POST['hobbies']) && !empty($_POST['hobbies']) && $_POST['hobbies'] !== $_SESSION['existingHobbies']) {
            $hobbies = $_POST['hobbies'];
            $hobbies = implode(' ', $hobbies);
            setHobbies($targetId, $hobbies);
        }else{
            $hobbies = getHobbies($targetId);
        }

        // Add Footer
        include "../footer.php";

        header('Location: usersListAdmin.php');
        exit();
    }


} else {
    echo "Target ID is not set.";
    exit();
}
?>