<?php

include "../helpers/db_connection.php";
include "../helpers/helperFunctions.php";
include "../admin/adminHelperFunctions.php";

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Trim and sanitize user input
        $email = trim($_POST['email']);
        $pass = trim($_POST['password']);

        $banBlock = true;

        //used for remembering the email in the form if the user fails to login (incorrect credentials)
        $_SESSION['form_email'] = $email;

        // Error check email and password
        if (empty($pass)) {
            $error = "Please provide a UL student email and password";
        } else if (!str_ends_with($email, '@studentmail.ul.ie')) {
            $error = "Email must end with '@studentmail.ul.ie'";
        } else {
            // SQL query to retrieve user information based on email
            $sql = "SELECT * FROM account WHERE email='$email'";
            $result = mysqli_query($conn, $sql);

            // Check if user exists
            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);

                // Check if user is banned
                if ($row['banned'] == '1') {

                    $reason = getBanReason($row['user_id']);
                    $dateOfUnban = getDateOfUnban($row['user_id']);

                    if ($dateOfUnban == "0000-00-00") {
                        $dateOfUnban = "Permanent Ban";
                    } else {
                        // Convert dateOfUnban to a timestamp
                        $unbanTimestamp = strtotime($dateOfUnban);
                        $currentTimestamp = time();

                        // Check if the unban date has passed
                        if ($unbanTimestamp < $currentTimestamp) {
                            $banBlock = false;
                            setBanned($row['user_id'], 0);
                        }
                    }
                } else {
                    $banBlock = false;
                }

                if (password_verify($pass, $row['password_hash']) && $banBlock == false) {
                    // User authenticated, set session variables
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['user_id'] = $row['user_id'];
                    //set notifications 
                    initialiseNotificationsOnLogin($_SESSION['user_id']);

                    // Redirect to admin page if user is admin  
                    if ($row['user_role'] == 'admin') {
                        header("Location: ../admin/admin.html");
                        exit();
                    }

                    // Redirect to home page
                    header("Location: ../home/home.php");
                    exit();
                } else {
                    if ($banBlock == true) {
                        $error = "User banned. Unbanned on: $dateOfUnban.  Reason: $reason. Please contact support.";
                    } else {
                        $error = "Incorrect UL student email or password";
                    }
                }
            } else {
                $error = "Incorrect UL student email or password";
            }
        }
    } else {
        $error = "Invalid form submission";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/ulSinglesSymbolTransparent.ico" type="image/x-icon">

    <!-- Bootstrap Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>



    <!-- Bootstrap Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" type="text/css" href="../../assets/css/login.css">

</head>

<body>

    <nav class="navbar navbar-fixed-top">
        <div class="images">
            <img class="header-img d-none d-md-block" src="../../assets/images/ul_logo.png" alt="ul_logo">
            <div class="line img-divider d-none d-md-block"></div>
            <img class="header-img" src="../../assets/images/ulSinglesTrasparent.png" alt="ulSingles_logo">
        </div>

        <div class="btn-group d-none d-lg-block" role="group">
            <button type="button" class="btn header-button" onclick="location.href='login.php'">Log In</button>
            <button type="button" class="btn header-button" onclick="location.href='../registration/registration.php'">Sign Up</button>
        </div>

    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 login-container">
                <!-- login form  -->
                <form action="" method="post">
                    <img class="mt-4 mb-4 img-fluid logoSymbol" src="../../assets/images/ulSinglesSymbolTransparent.png" height="200" alt="ulSingles_symbol">
                    <h3 class="mb-3 font-weight-normal">Log In</h3>

                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-container">
                        <input type="text" name="email" class="form-control" placeholder="Email" required autofocus value="<?php echo isset($_SESSION['form_email']) ? htmlspecialchars($_SESSION['form_email']) : ''; ?>"><br>
                        <input type="password" name="password" placeholder="Password" class="form-control"><br>
                        <button type="submit" class="btn btn-secondary mb-4 login-btn">Log in</button>
                    </div>

                    <!-- Link to Registration page via link -->
                    <p>Don't have an account? <a href="../registration/registration.php">Register here</a></p>
                </form>

            </div>
        </div>

    </div>

    <footer class="p-2">
        © 2024 Copyright UL Singles. All Rights Reserved
    </footer>

</body>

</html>