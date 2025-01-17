<?php

include "../helpers/db_connection.php";
include "../helpers/helperFunctions.php";
include "../admin/adminHelperFunctions.php";

accessCheck();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['hobbies'])) {
        $searchHobbies = $_POST['hobbies'];
    } else {
        $searchHobbies = NULL;
    }
    // Get the search parameters from the form

    if (isset($_POST['full_name'])) {
        $searchName = $_POST['full_name'];
    } else {
        $searchName = "";
    }

    $searchAgeMin = $_POST['min_age'];
    $searchAgeMax = $_POST['max_age'];
    $searchGender = $_POST['gender'];
    $searchLookingFor = $_POST['looking_for'];
    $searchCollegeYear = $_POST['college_year'];
    $userGender = getGender($_SESSION['user_id']);

    // Build the SQL query using prepared statements to prevent SQL injection
    $query = "SELECT * FROM profile WHERE age BETWEEN ? AND ?";

    // Bind parameters for the age range
    $params = array();

    if ($searchName !== "") {
        $query .= " AND name LIKE ?";
        $params[] = $searchName . '%';
    }

    if ($searchGender !== "any") {
        $query .= " AND gender = ?";
        $params[] = $searchGender;
    }

    if ($searchLookingFor !== "any") {
        $query .= " AND looking_for = ?";
        $params[] = $searchLookingFor;
    }

    if ($searchCollegeYear !== "any") {
        $query .= " AND college_year = ?";
        $params[] = $searchCollegeYear;
    }

    if ($searchHobbies !== NULL) {

        foreach ($searchHobbies as $hobby) {
            $query .= " AND hobbies LIKE ?";
            $params[] = '%' . $hobby . '%';
        }
    }

    // The prepared minimum entry for the bind_param function
    $types = 'ii';

    // Append type and value pairs for each parameter
    foreach ($params as $param) {
        if (is_string($param)) {
            $types .= 's'; // Add a string type
        }
    }

    $stmt = $conn->prepare($query);
    // Create an array with all parameters including the age range parameters
    $allParams = array_merge(array($types), array($searchAgeMin, $searchAgeMax), $params);

    //... unpacks the array into individual arguments
    $stmt->bind_param(...$allParams);

    $stmt->execute();
    $result = $stmt->get_result();

    //sets up the header and dropdown
    setupHeader();

    if ($result->num_rows > 0) {

        // create a container for the profile cards
        echo '<div class="profile-cards-container">';

        while ($row = $result->fetch_assoc()) {

            // Get the user ID of the each profile
            $targetUserId = $row['user_id'];

            // Skip the current user, admins, and users that the current user has adored
            if ($targetUserId == $_SESSION['user_id'] || getUserRole($targetUserId) == "admin") {
                continue;
            }

            // Check if the current user can adore the target user
            global $showingAdoreButton;
            if ((getPursuing($_SESSION['user_id']) === getGender($targetUserId) && getPursuing($targetUserId) === $userGender) && isUserAdored($_SESSION['user_id'], $targetUserId) !== true) {
                $showingAdoreButton = true;
            }

            // Display the profile card
            showProfileCard($targetUserId);

            // Reset the flag
            $showingAdoreButton = false;
        }

        //close the container
        echo '</div>';

        //set up the footer
        setupFooter();
    } else {
        //error
        echo "0 results found";
    }

}
?>