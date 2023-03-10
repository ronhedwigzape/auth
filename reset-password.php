<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
//if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
//    header("location: login.php");
//    exit;
//}

//echo "<pre>";
//echo var_dump($_COOKIE);
//echo "</pre>";
//
//echo "<pre>";
//echo var_dump($_SESSION);
//echo "</pre>";


// Check if user is logged in
if(!isset($_COOKIE['username'])){
    header("location: login.php");
    exit;
}
 
// Include config file
require_once "partials/conn.php";
 
// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("si", $param_password, $param_id);
            
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_COOKIE["id"];
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Password updated successfully.
//                session_destroy();
                $expiration = time() + 5;
                setcookie('reset_pass_status','success', $expiration, '/');
                header("location: welcome.php");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
//            $stmt->close();
        }
    }
    
    // Close connection
//    $conn->close();
}
require_once 'partials/header.php';
?>
<body style="background: #e7e7e7;">
<div id="preloader"></div>
    <div class="container px-4 py-3 mt-5 h-100"> <!--container-->
        <div class="row d-flex justify-content-center h-100"> <!--grid-->
            <div class="col-12 col-sm-10 col-md-7 col-lg-6 col-xl-5 "> <!--column-->
                <div class="bg-white text-black rounded-4"> <!--background-->
                    <div class="p-1 px-5 py-3 text-center rounded-4" style="box-shadow: 1px 3px 12px #888888;"> <!--padding-->
                        <div class="mb-md-2 mt-md-2"> <!--margin-->
                            <form class="reset" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <h2 class="fw-bold my-4 text-uppercase"><i class="fa-solid fa-repeat me-3"></i>Reset Password</h2>
                                <p>Please fill out this form to reset your password.</p>
                                <div class="form-floating form-outline form-white mb-3">
                                    <input type="password"
                                           name="new_password"
                                           id="password1"
                                           class="form-control form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>"
                                           value="<?php echo $new_password; ?>"
                                           placeholder="Username" autofocus>
                                    <label for="username" class="text-muted">New Password</label>
                                    <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
                                </div>
                                <div class="form-floating form-outline form-white mb-3">
                                    <input type="password"
                                           name="confirm_password"
                                           id="password2"
                                           class="form-control form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>"
                                           placeholder="Password">
                                    <label for="password" class="text-muted">Password</label>
                                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                                </div>
                                <div class="form-check my-4 text-center">
                                    <input type="checkbox" id="show-password" class="">
                                    <label for="show-password" class=" p-0">Show Password</label>
                                </div>
                                <div class="mb-3">
                                <button class="btn btn-md btn-outline-success px-4 rounded-5" value="Login" type="submit"><i class="fa-solid fa-check me-2"></i>Submit</button>
                                <a class="btn btn-md btn-outline-danger px-4 rounded-5" href="welcome.php"><i class="fa-solid fa-xmark me-2"></i>Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    $(document).ready(function(){
        var password1 = $("#password1");
        var password2 = $("#password2");
        var checkbox = $("#show-password");

        checkbox.change(function() {
            if (checkbox.is(":checked")) {
                password1.attr("type", "text");
                password2.attr("type", "text");
            } else {
                password1.attr("type", "password");
                password2.attr("type", "password");
            }
        });
    });
</script>
<script src="assets/js/main.js"></script>
</body>
</html>