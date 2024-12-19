<?php
// Include database connection
include '../database/db_connect.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect input
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
        $toastClass = "bg-warning";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match";
        $toastClass = "bg-warning";
    } else {
        // Check if email exists
        $checkEmailStmt = $conn->prepare("SELECT email FROM userdata WHERE email = ?");
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $result = $checkEmailStmt->get_result();

        if ($result->num_rows > 0) {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Update the password
            $stmt = $conn->prepare("UPDATE userdata SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashedPassword, $email);

            if ($stmt->execute()) {
                $message = "Password updated successfully";
                $toastClass = "bg-success";
            } else {
                $message = "Error updating password";
                $toastClass = "bg-danger";
            }
            $stmt->close();
        } else {
            $message = "Email does not exist";
            $toastClass = "bg-warning";
        }
        $checkEmailStmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/295/295128.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Reset Password</title>
</head>

<body>
    <div class="container p-5 d-flex flex-column align-items-center">
        <?php if ($message): ?>
            <div class="toast align-items-center text-white border-0" role="alert"
                 aria-live="assertive" aria-atomic="true" data-bs-autohide="true"
                 style="background-color: <?php echo $toastClass === 'bg-success' ? 
                '#28a745' : ($toastClass === 'bg-danger' ? '#dc3545' : '#ffc107'); ?>;">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="form-control mt-5 p-4"
              style="height:auto; width:380px; box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;">
            <div class="row">
                <i class="fa fa-user-circle-o fa-3x mt-1 mb-2" style="text-align: center; color: green;"></i>
                <h5 class="text-center p-4" style="font-weight: 700;">Change Your Password</h5>
            </div>
            <div class="col-mb-3 position-relative">
                <label for="email"><i class="fa fa-envelope"></i> Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
                <span id="email-check" class="position-absolute"
                      style="right: 10px; top: 50%; transform: translateY(-50%);"></span>
            </div>
            <div class="col mb-3 mt-3">
                <label for="password"><i class="fa fa-lock"></i> Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="col mb-3 mt-3">
                <label for="confirm_password"><i class="fa fa-lock"></i> Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>
            <div class="col mb-3 mt-3">
                <button type="submit" class="btn bg-dark" style="font-weight: 600; color:white;">
                    Reset Password
                </button>
            </div>
            <div class="col mb-2 mt-4">
                <p class="text-center" style="font-weight: 600; color: navy;">
                    <a href="./register.php" style="text-decoration: none;">Create Account</a> OR
                    <a href="./login.php" style="text-decoration: none;">Login</a>
                </p>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            $('#email').on('blur', function () {
                var email = $(this).val();
                if (email) {
                    $.ajax({
                        url: 'check_email.php',
                        type: 'POST',
                        data: { email: email },
                        success: function (response) {
                            if (response === 'exists') {
                                $('#email-check').html('<i class="fa fa-check text-success"></i>');
                            } else {
                                $('#email-check').html('<i class="fa fa-times text-danger"></i>');
                            }
                        }
                    });
                } else {
                    $('#email-check').html('');
                }
            });

            // Show toast notifications
            let toastElList = [].slice.call(document.querySelectorAll('.toast'));
            toastElList.forEach(toastEl => new bootstrap.Toast(toastEl).show());
        });
    </script>
</body>

</html>
