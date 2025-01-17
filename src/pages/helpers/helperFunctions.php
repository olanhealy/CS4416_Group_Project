<?php


function accessCheck()
{
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id']) || getUserRole($_SESSION['user_id']) != "standard") {
        header("Location: /src/pages/errors/unauthorisedaccess.html");
        exit();
    }
}

// Process #4 checks if the Account already exists in the account table of db
function isAccountFound($email, $password)
{
    global $conn;
    $count = 0;
    $sql_is_account_found = "SELECT COUNT(*) FROM account WHERE email = ? AND password_hash = ?";
    $account = $conn->prepare($sql_is_account_found);
    $account->bind_param('ss', $email, $password);
    $account->execute();
    $account->bind_result($count);
    $account->fetch();
    $account->close();

    return $count > 0;
}

function setVerified($user_id, $verified){
    global $conn;

    $sql_set_verified = "UPDATE `profile` SET verified = ? WHERE user_id = ?";
    $set_verified = $conn->prepare($sql_set_verified);
    $set_verified->bind_param("ii", $verified, $user_id);
    $set_verified->execute();

    if ($set_verified->affected_rows > 0) {
        echo "Verified set successfully";
    } else {
        echo "Error setting Verified";
    }

    $set_verified->close();
}

function getVerified($user_id){
    global $conn;
    $verified = 0;

    $sql_get_verified = "SELECT verified FROM profile WHERE user_id = ?";
    $get_verified = $conn->prepare($sql_get_verified);
    $get_verified->bind_param("i", $user_id);
    $get_verified->execute();
    $get_verified->store_result();

    if ($get_verified->num_rows > 0) {
        $get_verified->bind_result($verified);
        $get_verified->fetch();
    }

    $get_verified->close();
    return $verified;
}

function setPassword($password, $user_id)
{
    global $conn;

    // Hash the password for security 
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update the password in the Accounts table
    $sql_set_password = "UPDATE account SET password_hash = ? WHERE user_id = ?";
    $set_password = $conn->prepare($sql_set_password);
    $set_password->bind_param('si', $hashed_password, $user_id);
    $set_password->execute();

    if ($set_password->affected_rows > 0) {
        echo "Password set successfully";
    } else {
        echo "Error setting password";
    }

    $set_password->close();
}

function setPasswordForChange($password, $user_id)
{
    global $conn;

    // Hash the password for security 
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update the password in the Accounts table
    $sql_set_password = "UPDATE account SET password_hash = ? WHERE user_id = ?";
    $set_password = $conn->prepare($sql_set_password);
    $set_password->bind_param('si', $hashed_password, $user_id);
    $set_password->execute();

    if ($set_password->affected_rows > 0) {
        echo "Password set successfully";
    } else {
        echo "Error setting password";
    }

    $set_password->close();
}

function setFirstName($user_id, $first_name){
    global $conn;
    $conn->set_charset("utf8mb4");

    $sql_set_name = "UPDATE account SET first_name = ? WHERE user_id = ?";

    $set_name = $conn->prepare($sql_set_name);
    $set_name->bind_param("si", $first_name, $user_id);
    $set_name->execute();

    if ($set_name->affected_rows > 0) {
        echo '<a style="font-size:20px; text-align:center;"> First Name set successfully in Account. </a>';
    } else {
        //TODO: backend: fix messaging for error when jsut editted name
        //echo "Error setting First and Last Name";
    }
}

function setLastName($user_id, $last_Name){
    global $conn;
    $conn->set_charset("utf8mb4");

    $sql_set_name = "UPDATE account SET last_name = ? WHERE user_id = ?";

    $set_name = $conn->prepare($sql_set_name);
    $set_name->bind_param("si", $last_Name, $user_id);
    $set_name->execute();

    if ($set_name->affected_rows > 0) {
        echo '<a style="font-size:20px; text-align:center;"> Last Name set successfully in Account. </a>';
    } else {
        //TODO: backend: fix messaging for error when jsut editted name
        //echo "Error setting First and Last Name";
    }
}

// Process #11 to set the First name and Last name in the account table in db
function setName($first_name, $last_name, $user_id)
{
    global $conn;
    $conn->set_charset("utf8mb4");

    setFirstName($user_id, $first_name);
    setLastName($user_id, $last_name);

    if (getName($user_id) === "") {
        // Insert the user id  into profile table, also inserting the full name of user
        $sql_insert_profile = "INSERT INTO `profile` (user_id, `name` ) VALUES (?, ?)";
        $insert_new_profile = $conn->prepare($sql_insert_profile);
        $full_name = $first_name . " " . $last_name;
        $insert_new_profile->bind_param('is', $user_id, $full_name);
        $insert_new_profile->execute();


        $insert_new_profile->close();
    }else{
        $full_name = $first_name . " " . $last_name;
        $sql_update_profile = "UPDATE `profile` SET `name` = ? WHERE user_id = ?";
        $update_profile = $conn->prepare($sql_update_profile);
        $update_profile->bind_param("si", $full_name, $user_id);
        $update_profile->execute();

    }
}

function setBio($user_id, $bio) {
    global $conn;
    $conn->set_charset("utf8mb4");

    $sql_set_bio = "UPDATE profile SET bio = ? WHERE user_id = ?";
    $set_bio = $conn->prepare($sql_set_bio);
    $set_bio->bind_param("si", $bio, $user_id);
    $set_bio->execute();

}

// Process #14 to get the user's bio if they already exist in the profiile table of db
function getBio($user_id) {
    global $conn; 
    $conn->set_charset("utf8mb4");
    $bio = "";

    $sql_get_bio = "SELECT bio FROM profile WHERE user_id = ?";
    $get_bio = $conn->prepare($sql_get_bio);
    $get_bio->bind_param("i", $user_id);
    $get_bio->execute();
    $get_bio->store_result();

    if ($get_bio->num_rows > 0) {
        $get_bio->bind_result($bio);
        $get_bio->fetch();
        $bio = htmlspecialchars_decode($bio, ENT_QUOTES); 
    }

    $get_bio->close();
    return $bio;

}

// Process #17 to set the user's gender in the profile table of the db
function setGender($user_id, $gender) {
    global $conn;

    $sql_set_gender = "UPDATE profile SET gender = ? WHERE user_id = ?";
    $set_gender = $conn->prepare($sql_set_gender);
    $set_gender->bind_param("si", $gender, $user_id);
    $set_gender->execute();

    $set_gender->close();
}

// Process #38 to get the user's gender if they already exist in the profiile table of db
function getGender($user_id) {
    global $conn;
    $gender = "";

    $sql_get_gender = "SELECT gender FROM profile WHERE user_id = ?";
    $get_gender = $conn->prepare($sql_get_gender);
    $get_gender->bind_param("i", $user_id);
    $get_gender->execute();
    $get_gender->store_result();

    if ($get_gender->num_rows > 0) {
        $get_gender->bind_result($gender);
        $get_gender->fetch();
    }

    $get_gender->close();
    return $gender;

}

// Process #19 to set the user's age in the profile table of the db
function setAge($userId, $age) {
    global $conn;

    $sql_set_age = "UPDATE profile SET age = ? WHERE user_id = ?";
    $set_age = $conn->prepare($sql_set_age);
    $set_age->bind_param("ii", $age, $userId);
    $set_age->execute();

    $set_age->close();
}

// Process #20 to get the user's age if they already exist in the profiile table of db
function getAge($user_id) {
    global $conn;
    $age = "";

    $sql_get_age = "SELECT age FROM profile WHERE user_id = ?";
    $get_age = $conn->prepare($sql_get_age);
    $get_age->bind_param("i", $user_id);
    $get_age->execute();
    $get_age->store_result();

    if ( $get_age->num_rows > 0) {
        $get_age->bind_result($age);
        $get_age->fetch();
    }

    $get_age->close();
    return $age;

}

// Process #21 to set the user's college year in the profile table of the db
function setCollegeYear($user_id, $college_year) {
    global $conn;

    $sql_set_college_year = "UPDATE profile SET college_year = ? WHERE user_id = ?";
    $set_college_year = $conn->prepare($sql_set_college_year);
    $set_college_year->bind_param("si", $college_year, $user_id);
    $set_college_year->execute();

    $set_college_year->close();
}

// Process #22 to get the user's college year if they already exist in the profile table of the database
function getCollegeYear($user_id) {
    global $conn;
    $college_year = "";

    $sql_get_college_year = "SELECT college_year FROM profile WHERE user_id = ?";
    $get_college_year = $conn->prepare($sql_get_college_year);
    $get_college_year->bind_param("i", $user_id);
    $get_college_year->execute();
    $get_college_year->store_result();

    if ($get_college_year->num_rows > 0) {
        $get_college_year->bind_result($college_year);
        $get_college_year->fetch();
    }

    $get_college_year->close();
    return $college_year;
}

// Process #23 to set the user's pursuing status in the profile table of the db
function setPursuing($user_id, $pursuing) {
    global $conn;

    $sql_set_pursuing = "UPDATE profile SET pursuing = ? WHERE user_id = ?";
    $set_pursuing = $conn->prepare($sql_set_pursuing);
    $set_pursuing->bind_param("si", $pursuing, $user_id);
    $set_pursuing->execute();

    $set_pursuing->close();
}

// Process #22 to get the user's pursuing status if they already exist in the profile table of the database
function getPursuing($user_id) {
    global $conn;
    $pursuing = "";

    $sql_get_pursuing = "SELECT pursuing FROM profile WHERE user_id = ?";
    $get_pursuing = $conn->prepare($sql_get_pursuing);
    $get_pursuing->bind_param("i", $user_id);
    $get_pursuing->execute();
    $get_pursuing->store_result();

    if ($get_pursuing->num_rows > 0) {
        $get_pursuing->bind_result($pursuing);
        $get_pursuing->fetch();
    }

    $get_pursuing->close();
    return $pursuing;
}

// Process #25 to set the user's profile picture in the profile table of the db
function setProfilePic($user_id, $profile_pic_filename) {
    global $conn;

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        // gets the file extension (i.e png etc ) and converts to lowercase for consistency in comparision
        $file_extension = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        //the only allowed type of files to be upload
        $allowed_extensions = array('png', 'jpeg', 'jpg');

        //checks if the file extension is in the allowed extensions
        if (in_array($file_extension, $allowed_extensions)) {
           
            /*
            overwrite the uplaoded file name to be the users id, followed by "_profile_pic" and the file extension so we can ensure no 2 users
            have the same profile pic name
            */

            $new_filename = $user_id . "_profile_pic." . $file_extension;
            // path for saving is in uploads folder, we get the servers document root and then look for uploads
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
            $upload_file = $upload_dir . $new_filename;

            // Attempt to move the uploaded file
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_file)) {
                // update the profile table in db 
                $sql_set_profile_pic = "UPDATE profile SET profile_pic = ? WHERE user_id = ?";
                $set_profile_pic = $conn->prepare($sql_set_profile_pic);
                $relative_path = "uploads/" . $new_filename;
                $set_profile_pic->bind_param("si", $relative_path, $user_id);
                $set_profile_pic->execute();
                if ($set_profile_pic->affected_rows > 0) {
                    // Return the new path for immediate use
                    return $relative_path;
                } else {
                    $_SESSION['profile_pic_error'] = "Error setting profile picture.";
                    return false;
                }
            } else {
                $_SESSION['profile_pic_error'] = "Error uploading file.";
                return false;
            }
        } else {
            $_SESSION['profile_pic_error'] = "Invalid file type. Only PNG, JPEG, and JPG are allowed.";
            return false;
        }
    }

    // return excisting photo if no new photo is uploade || invalid file type uploaded (no way to display this error without javascript)
    return $profile_pic_filename;
}

// Process #26 to get the user's profile picture from the profile table of the db
function getProfilePicture($user_id) {
    global $conn;
    $profile_pic_filename = "";

    $sql_get_profile_pic = "SELECT profile_pic FROM profile WHERE user_id = ?";
    $get_profile_pic = $conn->prepare($sql_get_profile_pic);
    $get_profile_pic->bind_param("i", $user_id);
    $get_profile_pic->execute();
    $get_profile_pic->store_result();

    if ($get_profile_pic->num_rows > 0) {
        $get_profile_pic->bind_result($profile_pic_filename);
        $get_profile_pic->fetch();
    }

    $get_profile_pic->close();
    return $profile_pic_filename;
}

// Process #29 to set the user's course of study in the profile table of the db
function setCourse($user_id, $course) {
    global $conn;

    $sql_set_course = "UPDATE profile SET course = ? WHERE user_id = ?";
    $set_course = $conn->prepare($sql_set_course);
    $set_course->bind_param("si", $course, $user_id); 
    $set_course->execute();

}

// Process #30 to  get the user's course of study from the profile table of the db
function getCourse($user_id) {
    global $conn;
    $course= "";

    $sql_get_course = "SELECT course  FROM profile WHERE user_id = ?";
    $get_course  = $conn->prepare($sql_get_course );
    $get_course ->bind_param("i", $user_id);
    $get_course ->execute();
    $get_course ->store_result();

    if ($get_course ->num_rows > 0) {
        $get_course ->bind_result($course );
        $get_course ->fetch();
    }

    $get_course ->close();
    return $course;
}

// Process #31 to set the user's hobbies in the profile table of the db
function setHobbies($user_id, $hobbies) {
    global $conn;

    $sql_set_hobbies = "UPDATE profile SET hobbies = ? WHERE user_id = ?";
    $set_hobbies = $conn->prepare($sql_set_hobbies);
    $set_hobbies->bind_param("si", $hobbies, $user_id);
    $set_hobbies->execute();

}

// Process #32 to get the user's hobbies if they already exist in the profiile table of db (incomplete for html side)
function getHobbies($user_id) {
    global $conn;
    $hobbies = "";

    $sql_get_hobbies = "SELECT hobbies FROM profile WHERE user_id = ?";
    $get_hobbies = $conn->prepare($sql_get_hobbies);
    $get_hobbies->bind_param("i", $user_id);
    $get_hobbies->execute();
    $get_hobbies->store_result();

    if ($get_hobbies->num_rows > 0) {
        $get_hobbies->bind_result($hobbies_str);
        $get_hobbies->fetch();
        $hobbies = explode(' ', $hobbies_str);
    }

    $get_hobbies->close();
    return $hobbies;
}

// Process #33 to set the user's looking for status in the profile table of the db
function setLookingFor($user_id, $looking_for) {
    global $conn;

    $sql_set_looking_for = "UPDATE profile SET looking_for = ? WHERE user_id = ?";
    $set_looking_for = $conn->prepare($sql_set_looking_for);
    $set_looking_for->bind_param("si", $looking_for, $user_id);
    $set_looking_for->execute();

}

// Process #34 to get the user's looking for status if they already exist in the profiile table of db
function getLookingFor($user_id) {
    global $conn;
    $looking_for = "";
    
    $sql_get_looking_for = "SELECT looking_for FROM profile WHERE user_id = ?";
    $get_looking_for = $conn->prepare($sql_get_looking_for);
    $get_looking_for->bind_param("i", $user_id);
    $get_looking_for->execute();
    $get_looking_for->store_result();

    if ($get_looking_for->num_rows > 0) {
        $get_looking_for->bind_result($looking_for);
        $get_looking_for->fetch();
    }
    
    $get_looking_for->close();
    return $looking_for;
}

// function to get name used in explore.php
function getName($user_id) {
    global $conn;
    $conn->set_charset("utf8mb4");
    $name = "";

    $sql_get_name = "SELECT name FROM profile WHERE user_id = ?";
    $get_name = $conn->prepare($sql_get_name);
    $get_name->bind_param("i", $user_id);
    $get_name->execute();
    $get_name->store_result();

    if ($get_name->num_rows > 0) {
        $get_name->bind_result($name);
        $get_name->fetch();
    }

    $get_name->close();
    return $name;
}

function getMatchId($userId, $targetId)
{
    global $conn;

    $query = "SELECT match_id FROM matches WHERE initiator_id = ? AND target_id = ? OR initiator_id = ? AND target_id = ?";
    $sqlGetmatchId = $conn->prepare($query);
    if ($sqlGetmatchId !== false) {
        $sqlGetmatchId->bind_param("iiii", $userId, $targetId, $targetId, $userId);
        $sqlGetmatchId->execute();
        $matchId = $sqlGetmatchId->get_result();
        $sqlGetmatchId->close();

        if ($matchId->num_rows > 0) {
            $row = $matchId->fetch_assoc();
            return $row['match_id'];
        }
    }
    return false;
}

function getMatch($userId, $targetId)
{
    global $conn;

    $query = "SELECT * FROM matches WHERE initiator_id = ? AND target_id = ? OR initiator_id = ? AND target_id = ?";
    $sqlGetMatch = $conn->prepare($query);
    if ($sqlGetMatch !== false) {
        $sqlGetMatch->bind_param("iiii", $userId, $targetId, $targetId, $userId);
        $sqlGetMatch->execute();
        $match = $sqlGetMatch->get_result();
        $sqlGetMatch->close();

        if ($match->num_rows > 0) {
            return true;
        }
    }
    return false;
}

function addMatch($initiatorId, $targetId)
{

    global $conn;

    $query = "INSERT INTO matches (initiator_id, target_id) VALUES (?, ?)";
    $sqlAddMatch = $conn->prepare($query);
    if ($sqlAddMatch !== false) {
        $sqlAddMatch->bind_param("ii", $initiatorId, $targetId);
        $sqlAddMatch->execute();
    } else {
        die("Error in SQL query: " . $conn->error . "<br>");
    }
}

function isItAMatch($initiatorId, $targetId)
{
    global $conn;

    $query = "SELECT * FROM adore WHERE user_id = ? AND adored_user_id = ?";

    if ($sqlIsItMatch = $conn->prepare($query)) {
        $sqlIsItMatch->bind_param("ii", $targetId , $initiatorId);
        $sqlIsItMatch->execute();
        $isItMatch = $sqlIsItMatch->get_result();
        $sqlIsItMatch->close();

        if ($isItMatch->num_rows > 0) {
            // The current user has previously adored the logged in user
            return true;
        }
    }
    // The current user has not previously adored the logged in user
    return false;
}

function removeMatch($userId, $targetId)
{

    global $conn;

    $matchId = getMatchId($userId, $targetId);

    $query = "DELETE FROM messages WHERE match_id = ?";
    $sqlRemoveMatch = $conn->prepare($query);

    if ($sqlRemoveMatch !== false) {
        $sqlRemoveMatch->bind_param("i", $matchId);
        $sqlRemoveMatch->execute();
    } else {
        die("Error in SQL query: " . $conn->error . "<br>");
    }

    $query = "DELETE FROM matches WHERE initiator_id = ? AND target_id = ? OR initiator_id = ? AND target_id = ?";
    $sqlRemoveMatch = $conn->prepare($query);

    if ($sqlRemoveMatch !== false) {
        $sqlRemoveMatch->bind_param("iiii", $userId, $targetId, $targetId, $userId);
        $sqlRemoveMatch->execute();
    } else {
        die("Error in SQL query: " . $conn->error . "<br>");
    }
}

// Gets all of the users matches
function getAllMatches($userId)
{
    global $conn;
    //get all account information
    $query = "SELECT 
        CASE 
            WHEN initiator_id = $userId THEN target_id 
            ELSE initiator_id 
        END AS other_user_id 
    FROM matches 
    WHERE initiator_id = $userId OR target_id = $userId 
    ORDER BY response_date DESC";
    ;

    //check the result is not empty
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        // Output data of each row with a form to ban/unban
        while ($row = $result->fetch_assoc()) {

            $targetId = $row['other_user_id'];
            
            $name = getName($targetId);
            $age = getAge($targetId);
            $profilePicture = getProfilePicture($targetId);

            //include the user list html for each row
            include "match.html";
        }
    } else {
        //error
        echo "0 results found";
    }
}

// Gets the users next match
function getNextMatches($userId)
{
    global $conn;
    
    // Get next matches for user
    $query = "SELECT 
                CASE 
                    WHEN initiator_id = $userId THEN target_id 
                    ELSE initiator_id 
                END AS other_user_id 
              FROM matches 
              WHERE (initiator_id = $userId OR target_id = $userId)
                AND response_date IS NOT NULL 
              ORDER BY response_date DESC";

    // Check the result is not empty
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        // Fetch each match and add it to the matches array
        while ($row = $result->fetch_assoc()) {
            $targetId = $row['other_user_id'];
            $name = getName($targetId);
            $age = getAge($targetId);
            $matchId = getMatchId($userId, $targetId);
            $profilePicture = getProfilePicture($targetId);
            $matches[] = array('name' => $name, 'age' => $age, 'userId' => $userId, 'targetId' => $targetId, 'matchId' => $matchId,'profilePicture' => $profilePicture);

        }

        return $matches;

    } else {
        // Error
        echo '<div class="col-md-12 col-lg-12 col-lg-12" id="errorContainer">
        <p id="error"> No matches found. <br> Use the Explore page to create new matches!</p></div>';

        return [];
    }

}


function getBanReason($userId){
    global $conn;
    $banReason = "";

    $query = "SELECT reason FROM banned WHERE user_id = ?";

    if ($sqlGetBanReason = $conn->prepare($query)) {
        $sqlGetBanReason->bind_param("i", $userId);
        $sqlGetBanReason->execute();
        $getBanReason = $sqlGetBanReason->get_result();
        $sqlGetBanReason->close();

        if ($getBanReason->num_rows > 0) {
            $row = $getBanReason->fetch_assoc();
            $banReason = $row['reason'];
        }
    }
    return $banReason;

}


function getDateOfUnban($userId){
    global $conn;
    $unbanDate = "";

    $query = "SELECT dateOfUnban FROM banned WHERE user_id = ?";

    if ($sqlGetDateOfUnban = $conn->prepare($query)) {
        $sqlGetDateOfUnban->bind_param("i", $userId);
        $sqlGetDateOfUnban->execute();
        $getDateOfUnban = $sqlGetDateOfUnban->get_result();
        $sqlGetDateOfUnban->close();

        if ($getDateOfUnban->num_rows > 0) {
            $row = $getDateOfUnban->fetch_assoc();
            $unbanDate = $row['dateOfUnban'];
        }
    }
    return $unbanDate;


}
function showProfileCard($user_id){

    $targetUserId = $user_id;
    $name = getName($user_id);
    $profilePicture = getProfilePicture($user_id);
    $bio = getBio($user_id);
    $gender = getGender($user_id);
    $age = getAge($user_id);
    $collegeYear = getCollegeYear($user_id);
    $pursuing = getPursuing($user_id);
    $course = getCourse($user_id);
    $hobbies = getHobbies($user_id);
    $lookingFor = getLookingFor($user_id);

    include "../profilecard/profileCard.html";
}

function setupHeader(){
    include "header.php";
}

function setupFooter(){
    include "footer.php";
}

//process #43, functon to handle adore action 
function adoreUser($userLoggedInId, $currentUserId)
{
    global $conn;
    $sqlAdore = "INSERT INTO adore (user_id, adored_user_id, date) VALUES (?, ?, NOW())";
    if ($adore = $conn->prepare($sqlAdore)) {
        $adore->bind_param("ii", $userLoggedInId, $currentUserId);
        $adore->execute();
        $adore->close();
    }
}

//fucntion to handle ignore action
function ignoreUser($userLoggedInId, $currentUserId)
{
    global $conn;
    $sqlIgnore = "INSERT INTO `ignore` (user_id, ignored_user_id, date) VALUES (?, ?, NOW())";
    if ($ignore = $conn->prepare($sqlIgnore)) {
        $ignore->bind_param("ii", $userLoggedInId, $currentUserId);
        $ignore->execute();
        $ignore->close();
    }
}

function isUserAdored($userId, $targetId){

    global $conn;

    $query = "SELECT * FROM adore WHERE user_id = ? AND adored_user_id = ?";

    if ($sqlIsUserAdored = $conn->prepare($query)) {
        $sqlIsUserAdored->bind_param("ii", $userId, $targetId);
        $sqlIsUserAdored->execute();
        $isUserAdored = $sqlIsUserAdored->get_result();
        $sqlIsUserAdored->close();

        if ($isUserAdored->num_rows > 0) {
            // The current user has previously adored the logged in user
            return true;
        }
    }
    // The current user has not previously adored the logged in user
    return false;
}

function getMessagesByMatchId($matchId, $currentUserId) {
    global $conn;
    $sqlGetMessagesByMatchId = $conn->prepare("SELECT * FROM messages WHERE match_id = ? ORDER BY date ASC");
    $sqlGetMessagesByMatchId->bind_param("i", $matchId);
    $sqlGetMessagesByMatchId->execute();
    $resultSqlGetMessagesByMatchId = $sqlGetMessagesByMatchId->get_result();

    $messages = [];
    while ($row = $resultSqlGetMessagesByMatchId->fetch_assoc()) {
        $row['from_self'] = ($row['sender_id'] == $currentUserId);
        $messages[] = $row;
    }

    $sqlGetMessagesByMatchId->close();

    return $messages;
}


// process #47 to send a message to target user
function sendMessage($userId, $matchId, $messageContent) {
    global $conn;
    // Determine receiver_id based on match_id
    $receiverId = getReceiverIdByMatchId($matchId, $userId);
    //return false if no receiver_id is found (no match id or invalid one)
    if (!$receiverId) {
        
        return false;
    }
    //Insert message content into the db
    $sqlSendMessage = $conn->prepare("INSERT INTO messages (match_id, receiver_id, sender_id, message_content, read_status) VALUES (?, ?, ?, ?, 'delivered')");
    $sqlSendMessage ->bind_param("iiis", $matchId, $receiverId, $userId, $messageContent);
    $executeSuccess = $sqlSendMessage ->execute();
    $sqlSendMessage ->close();

    return $executeSuccess; // Return the success status of the execute (true or false)
}

// Function to get receiver_id by match_id
function getReceiverIdByMatchId($matchId, $senderId) {
    global $conn;
    //Get the initiator_id and target_id from the matches table
    $sqlGetReceiverIdByMatchId = $conn->prepare("SELECT initiator_id, target_id FROM matches WHERE match_id = ?");
    $sqlGetReceiverIdByMatchId->bind_param("i", $matchId);
    $sqlGetReceiverIdByMatchId->execute();
    $sqlGetReceiverIdByMatchId->bind_result($initiatorId, $targetId);

    if ($sqlGetReceiverIdByMatchId->fetch()) {
        // Get the receiver_id based on the match_id
        $receiverId = ($initiatorId === $senderId) ? $targetId : $initiatorId;
    } else {
        $receiverId = null; //if no match excists
    }
    $sqlGetReceiverIdByMatchId->close();
    return $receiverId;
}

// process #46 to get messages for a user between their specific target
function getMessages($userId) {
    global $conn;
    $conversations = [];
    // get all matchIds where the user is either the initiator or the target
    $sqlGetMessages = $conn->prepare("SELECT match_id FROM matches WHERE initiator_id = ? OR target_id = ?");
    $sqlGetMessages->bind_param("ii", $userId, $userId);
    $sqlGetMessages->execute();
    $resultSqlGetMessages = $sqlGetMessages->get_result();
    while($row = $resultSqlGetMessages->fetch_assoc()) {
        $conversations[] = $row['match_id'];
    }
    $sqlGetMessages->close();
    return $conversations;
}

function getNameByMatchId($matchId, $userId) {
    global $conn;
    $conn->set_charset("utf8mb4");
    $sqlGetNameByMatchId = "SELECT p.name FROM profile p INNER JOIN matches m ON p.user_id = CASE 
    WHEN m.initiator_id = ? THEN m.target_id 
    ELSE m.initiator_id END WHERE m.match_id = ?";
    $getNameByMatchId = $conn->prepare($sqlGetNameByMatchId);
    if ($getNameByMatchId !== false) {
        $getNameByMatchId->bind_param("ii", $userId, $matchId);
        $getNameByMatchId->execute();
        $resultGetName = $getNameByMatchId->get_result();
        if ($resultGetName->num_rows > 0) {
            $rowGetName = $resultGetName->fetch_assoc();
            $name = $rowGetName['name'];
        }
        $getNameByMatchId->close();
    }
    return $name;
}

function getProfilePictureByMatchId($matchId, $userId) {
    global $conn;
    $sqlGetProfilePictureByMatchId = "SELECT profile_pic FROM profile WHERE user_id = (SELECT CASE 
        WHEN initiator_id = ? THEN target_id 
        ELSE initiator_id 
    END FROM matches WHERE match_id = ?)";
    $getProfilePicture = $conn->prepare($sqlGetProfilePictureByMatchId);
    if ($getProfilePicture!== false) {
        $getProfilePicture->bind_param("ii", $userId, $matchId);
        $getProfilePicture->execute();
        $resultGetProfilePicture = $getProfilePicture->get_result();
        if ($resultGetProfilePicture->num_rows > 0) {
            $rowGetProfilePicture = $resultGetProfilePicture->fetch_assoc();
            $profilePicture = $rowGetProfilePicture['profile_pic'];
        }
        $getProfilePicture->close();
    }
    return $profilePicture;
}

//Process #48 user can unsed a message they sent to a specific user and it will be deleted from the Messages table
function deleteMessage($userId, $messageId) {
    global $conn;
    
    // Check if the message belongs to the user
    $sqlDeleteMessage = $conn->prepare("SELECT * FROM messages WHERE message_id = ? AND (sender_id = ? OR receiver_id = ?)");
    $sqlDeleteMessage ->bind_param("iii", $messageId, $userId, $userId);
    $sqlDeleteMessage ->execute();
    $resultDeleteMessage  = $sqlDeleteMessage ->get_result();
    if ($resultDeleteMessage ->num_rows === 0) {
        // Message does not belong to user o
        return false;
    }

    // Delete the message from tabke
    $sqlDeleteMessage  = $conn->prepare("DELETE FROM messages WHERE message_id = ?");
    $sqlDeleteMessage ->bind_param("i", $messageId);
    $sqlDeleteMessage ->execute();
    
    return $sqlDeleteMessage ->affected_rows > 0;
}

//Function to get delivery satus (may need)
function getDeliveryStatus($userId, $messageId) {
    global $conn;
    $sqlGetDeliveryStatus = $conn->prepare("SELECT read_status FROM messages WHERE message_id = ? AND (sender_id = ? OR receiver_id = ?)");
    $sqlGetDeliveryStatus->bind_param("iii", $messageId, $userId, $userId);
    $sqlGetDeliveryStatus->execute();
    $resultGetDeliveryStatus = $sqlGetDeliveryStatus->get_result();
    if ($resultGetDeliveryStatus->num_rows > 0) {
        $rowGetDeliveryStatus = $resultGetDeliveryStatus->fetch_assoc();
        $deliveryStatus = $rowGetDeliveryStatus['read_status'];
    }
    $sqlGetDeliveryStatus->close();
    return $deliveryStatus;
}

// Function to count new messages and matches
function fetchNotifications($userId) {
    global $conn;
    // Initialise the count of notifications
    $notifications = [
        'messages' => 0,
        'matches' => 0
    ];
    
    $matchesAsInitiator = 0;
    $matchesAsTarget = 0;

    // Sql statement to get the count of new messages
    $sqlMessages = "SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND read_status = 'delivered'";
    $sqlGetMessages = $conn->prepare($sqlMessages);
    $sqlGetMessages->bind_param("i", $userId);
    $sqlGetMessages->execute();
    $sqlGetMessages->bind_result($notifications['messages']);
    $sqlGetMessages->fetch();
    $sqlGetMessages->close();

    // Sql statement to get the count of new matches for the target
    $sqlMatchesAstarget = "SELECT COUNT(*) FROM matches WHERE target_id = ? AND viewed_by_target = FALSE";
    $sqlGetMatchesAstarget = $conn->prepare($sqlMatchesAstarget);
    $sqlGetMatchesAstarget->bind_param("i", $userId);
    $sqlGetMatchesAstarget->execute();
    $sqlGetMatchesAstarget->bind_result($matchesAsTarget);
    $sqlGetMatchesAstarget->fetch();
    $sqlGetMatchesAstarget->close();

    // Sql statement to get the count of new matches for the initiator
    $sqlMatchesAsInitiator = "SELECT COUNT(*) FROM matches WHERE initiator_id = ? AND viewed_by_initiator = FALSE";
    $sqlGetMatchesAsInitiator = $conn->prepare($sqlMatchesAsInitiator);
    $sqlGetMatchesAsInitiator->bind_param("i", $userId);
    $sqlGetMatchesAsInitiator->execute();
    $sqlGetMatchesAsInitiator->bind_result($matchesAsInitiator);
    $sqlGetMatchesAsInitiator->fetch();
    $sqlGetMatchesAsInitiator->close();

    // Sum up the total matches that have not been viewed by the user
    $notifications['matches'] = $matchesAsTarget + $matchesAsInitiator;

    return $notifications;
}

//Function to clear the messages notifications when they are viewed
function clearMessageNotifications($userId) {
    global $conn;
    $sqlClearMessages = "UPDATE messages SET read_status = 'read' WHERE receiver_id = ? AND read_status = 'delivered'";
    $clearMessages = $conn->prepare($sqlClearMessages);
    $clearMessages->bind_param("i", $userId);
    $clearMessages->execute();
    $clearMessages->close();
}

//function to clear the matches notifications when they are viewed
function clearMatchNotifications($userId) {
    global $conn;
    $sqlClearMatches = "UPDATE matches SET viewed_by_target = TRUE WHERE target_id = ?";
    $clearMatches = $conn->prepare($sqlClearMatches);
    $clearMatches->bind_param("i", $userId);
    $clearMatches->execute();
    $clearMatches->close();

    $sqlClearMatchesForInitiator = "UPDATE matches SET viewed_by_initiator = TRUE WHERE initiator_id = ?";
    $clearMatchesForInitiator = $conn->prepare($sqlClearMatchesForInitiator);
    $clearMatchesForInitiator->bind_param("i", $userId);
    $clearMatchesForInitiator->execute();
    $clearMatchesForInitiator->close();

     // Reset session notification counts
     $_SESSION['notifications'] = fetchNotifications($userId);

}
//function to iniitalise the notifications on login for that user
function initialiseNotificationsOnLogin($userId) {
    $_SESSION['notifications'] = fetchNotifications($userId);
}


function areUserDetailsSet($userId) {
    // get user details from db
    $bio = getBio($userId);
    $hobbies = getHobbies($userId);
    $gender = getGender($userId);
    $age = getAge($userId);
    $collegeYear = getCollegeYear($userId);
    $pursuing = getPursuing($userId);
    $profilePicFilename = getProfilePicture($userId);
    $course = getCourse($userId);
    $lookingFor = getLookingFor($userId);
    $name = getName($userId);

    // If all not null, return true else its false
    if (
        !is_null($bio) && !empty($bio) &&
        !is_null($hobbies) && $hobbies[0] !== "" &&
        !is_null($gender) && !empty($gender) &&
        !is_null($age) && !empty($age) &&
        !is_null($collegeYear) && !empty($collegeYear) &&
        !is_null($pursuing) && !empty($pursuing) &&
        !is_null($profilePicFilename) && !empty($profilePicFilename) &&
        !is_null($course) && !empty($course) &&
        !is_null($lookingFor) && !empty($lookingFor) &&
        !is_null($name) && !empty($name)
    ) {
        return true;
    } else {
        return false;
    }
}
// as hobbies now in array need to export in in comma seperated strings for display

function escapeHtmlForSearch($data) {
    if (is_array($data)) {
        
        $data = implode(', ', $data);
    }
    return htmlspecialchars($data);
}

//get base url so e can use our header.php in different pags
function getBaseUrl() {
    // Check if HTTPS or HTTP is used
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    
    // Get the server name 
    $domainName = $_SERVER['HTTP_HOST'];

    // Construct the base URL
    $baseUrl = $protocol . $domainName;

    //get path where header.css is used
    $extraPath = '/src/assets/';

    //add this url so we can use it to find the css file in header.php for every page
    return rtrim($baseUrl, '/') . $extraPath;
}

function countUnreadMessages($userId, $conversationMatchId, ) {
    global $conn;
    $query = "SELECT COUNT(*) FROM messages 
              WHERE match_id = ? 
              AND read_status = 'delivered' 
              AND receiver_id = ?";

    $sqlUnreadMessages = $conn->prepare($query);
    $sqlUnreadMessages->bind_param("ii", $conversationMatchId, $userId);
    $sqlUnreadMessages->execute();
    $sqlUnreadMessages->bind_result($count);
    $sqlUnreadMessages->fetch();
    $sqlUnreadMessages->close();

    return $count;
}
?>