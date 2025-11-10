<?php

class Forms{
    public function signup(){
        ?>
        <form  action ="Forms/signup_action.php" method="POST">
        <h1 style="text-align:center;">Sign up</h1>
            <div class="form-group">
                    <label  for="username">Full Name</label>
                    <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Email</label>
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"  name="email">

            </div>
            <div class="form-group">
                <label  for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" id="signupPassword" onkeyup="checkPasswordStrength(this.value, 'signup-strength')" name="password">
                <div id="signup-strength" style="margin-top: 5px; font-size: 0.9em;"></div>
            </div>
            <div style="text-align:center;">
            <button type="submit" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big-icon lucide-circle-check-big"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg> Sign up</button>
            <div id="login-redirect-container">
                    <a href="?form=login">Already have an account? Login</a>
            </div>
    </div>
        </form>
        <?php

    }

    public function login() {
        ?>
        <form method="POST" action="Forms/login_action.php">
            <h1 style="text-align:center;">Login</h1>
            <div class="form-group">
                <label for="loginEmail">Email address</label>
                <input type="email" class="form-control" id="loginEmail" name="email"  required>
            </div>

            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input type="password" class="form-control" id="loginPassword" name="password"  required>
            </div>
            <div style="text-align:center;">
                <button type="submit" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big-icon lucide-circle-check-big"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg> Login</button>
                <div id="create-account-container">
                    <a href="?form=signup">Don't have an account? Create one</a><br>
                    <a href="?form=forgot_password">Forgot Password?</a><br><br>
                </div>
            </div>
        </form>
        <?php
    }

    public function twofa(){
        ?>
        <div>
            <form style="padding:40px;text-align:center;" method="POST" action="Forms/twofactor.php">
                <h1>Two-Factor authentication</h1>
                <p>Enter the code below</p>
                    <input style="height:80px;color:white;text-align:center;background-color:#23262b;border:none;font-size:30px;width:100%;" type="text" id="verification_code" name="verification_code" maxlength="6" placeholder="000 - 000" required>
                <button class="btn btn-primary" type="submit">Verify</button>
            </form>
            <?php if (!empty($error)) : ?>
                <p style="color:red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            </div>
        <?php
    }

    
    public function forgotPassword(){
        ?>
        <form method="POST" action="Forms/forgot_password_action.php">
            <h1 style="text-align:center;">Reset Password</h1>
            <p style="text-align:center;">Enter your email to receive a 6-digit verification code.</p>
            <div class="form-group">
                <label for="forgotEmail">Email address</label>
                <input type="email" class="form-control" id="forgotEmail" name="email" required>
            </div>
            <div style="text-align:center;">
                <button type="submit" class="btn btn-primary">Send Code</button>
                <div id="login-redirect-container">
                    <a href="?form=login">Back to Login</a>
                </div>
            </div>
        </form>
        <?php
    }

 

public function resetCodeForm(){
   
    $error_message = '';
    if (isset($_GET['error'])) {
        if ($_GET['error'] === 'InvalidOrExpiredCode') {
            $error_message = "Error: Invalid or expired verification code. Please try again.";
        } elseif ($_GET['error'] === 'InvalidCodeFormat') {
            $error_message = "Error: Invalid code format. Please enter a 6-digit number.";
        }
       
    }

    ?>
    <div class="twofa-container">
        <form style="padding:40px;text-align:center;" method="POST" action="Forms/reset_code_action.php">
            <h1>Password Reset Code</h1>
            <p>Enter the 6-digit code sent to your email.</p>
            
            <?php if (!empty($error_message)) : ?>
                <p style="color:red; text-align:center;"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>

            <input style="height:80px;color:white;text-align:center;background-color:#23262b;border:none;font-size:30px;width:100%;" type="text" id="reset_code" name="reset_code" maxlength="6" placeholder="000 - 000" required>
            <button class="btn btn-primary" type="submit">Verify Code</button>
        </form>
    </div>
    <?php
}

   

public function newPasswordForm(){
   
    $error_message = '';
    if (isset($_GET['error']) && $_GET['error'] === 'PasswordsDoNotMatch') {
        $error_message = "Error: The passwords you entered do not match. Please try again.";
    }

    ?>
    <form method="POST" action="Forms/new_password_action.php">
        <h1 style="text-align:center;">Set New Password</h1>
        <p style="text-align:center;">Enter and confirm your new password.</p>
        
        <?php if (!empty($error_message)) : ?>
            <p style="color:red; text-align:center;"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <div class="form-group">
            <label for="newPassword">New Password</label>
            <input type="password" class="form-control" id="newPassword" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
        </div>
        <div style="text-align:center;">
            <button type="submit" class="btn btn-primary">Change Password</button>
        </div>
    </form>
    <?php
}

}
?>

<script>
    function checkPasswordStrength(password, indicatorId) {
        const indicator = document.getElementById(indicatorId);
        let strength = 0;
        let feedback = "Weak";
        let color = "red";

       
        if (password.length >= 8) {
            strength += 1;
        }
        
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) {
            strength += 1;
        }

        if (password.match(/\d/)) {
            strength += 1;
        }

    
        if (password.match(/[^a-zA-Z\d\s]/)) {
            strength += 1;
        }

        
        if (password.length === 0) {
            feedback = "";
            color = "transparent";
        } else if (strength < 2) {
            feedback = "Weak (Minimum 8 chars and mixed case recommended)";
            color = "red";
        } else if (strength === 2 || strength === 3) {
            feedback = "Medium";
            color = "orange";
        } else if (strength === 4) {
            feedback = "Strong";
            color = "green";
        }

        indicator.innerHTML = 'Strength: ' + feedback;
        indicator.style.color = color;
    }
</script>