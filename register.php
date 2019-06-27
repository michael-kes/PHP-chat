<?php include "includes/db.php"; ?>
<?php include "includes/functions.php"; ?>

<?php

    $errors = '';
    $error_color = 'green';
    $error_background = 'bg-success';

    if (isset($_POST['register']))
    {
        $username           = escapeString($_POST['username']);
        $user_password      = escapeString($_POST['password']);
        $confirm_password   = escapeString($_POST['passwordCheck']);
        $user_firstname     = escapeString($_POST['firstname']);
        $user_lastname      = escapeString($_POST['lastname']);
        $user_email         = escapeString($_POST['email']);

        $hashpassword = generateHash($user_password);

        if (!empty($user_email) && !empty($user_lastname) && !empty($user_firstname) && !empty($username) && !empty($user_password) && !empty($confirm_password))
        {
            if (usernameExists($username))
            {
                $errors .= 'Gebruikersnaam bestaat al';
                $error_color = 'red';
                $error_background = 'bg-danger';
            }
            elseif (emailExists($user_email))
            {
                $errors .= 'Email bestaat al';
                $error_color = 'red';
                $error_background = 'bg-danger';
            }
            else
            {
                if ($user_password == $confirm_password)
                {
                    createUser($username, $user_firstname, $user_lastname, $user_email, $hashpassword);

                    $errors.= 'Regristratie is compleet. U kunt nu inloggen';

                    header("Refresh: 2; URL=index.php");
                }
                else
                {
                    $errors .= 'Wachtwoorden komen niet overeen';
                    $error_color = 'red';
                    $error_background = 'bg-danger';
                }
            }
        }
        else
        {
            $errors .= 'Alle velden zijn verplicht';
            $error_color = 'red';
            $error_background = 'bg-danger';
        }
    }

?>

    <?php  include "includes/header.php"; ?>

    <!-- Page Content -->
    <div class="container">
        <div class="row">
            <div class="col s4 offset-s4">
                <!-- Blog Login Well -->
                <div class="card-panel white" style="margin-top: 40px;">
                <h4 class="center-align">Registreren</h4>
                    <form role="form" action="register.php" method="post" id="loginForm" autocomplete="off" onsubmit="return registerFormCheck(this);">
                        <?php if($errors != ""): ?>
                            <div class='<?php echo $error_background; ?> center-align' style='color: <?php echo $error_color; ?>'>
                                <?php echo $errors; ?>
                            </div><br>
                        <?php endif; ?>

                        <div class="form-group">
                            <i class="tiny material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Alleen letters en cijfers zijn toegestaan" style="float:left; margin-top: 4px;">info</i>
                            &nbsp;<label for="username" class="sr-only">Gebruikersnaam</label>
                            <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($_POST['username']) ? $_POST['username'] : '' ?>">
                        </div>

                        <div class="form-group">
                            <i class="tiny material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Alleen letters zijn toegestaan" style="float:left; margin-top: 4px;">info</i>
                            &nbsp;<label for="firstname" class="sr-only">Naam</label>
                            <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($_POST['firstname']) ? $_POST['firstname'] : '' ?>">
                        </div>

                        <div class="form-group"><i class="tiny material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Alleen letters zijn toegestaan" style="float:left; margin-top: 4px;">info</i>
                            &nbsp;<label for="lastname" class="sr-only">Achternaam</label>
                            <input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($_POST['lastname']) ? $_POST['lastname'] : '' ?>">
                        </div>

                         <div class="form-group">
                            <i class="tiny material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Voer een correcte e-mailadres in" style="float:left; margin-top: 4px;">info</i>
                            &nbsp;<label for="email" class="sr-only">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>">
                        </div>

                         <div class="form-group">
                            <i class="tiny material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Min. 8 karakters lang met 1 speciale teken, nummer, hoofdletter en kleine letter" style="float:left; margin-top: 4px;">info</i>
                            &nbsp;<label for="password" class="sr-only">Wachwtoord</label>
                            <input type="password" name="password" id="key" class="form-control">
                        </div>

                        <div class="form-group">
                            <i class="tiny material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Min. 8 karakters lang met 1 speciale teken, nummer, hoofdletter en kleine letter" style="float:left; margin-top: 4px;">info</i>
                            &nbsp;<label for="passwordCheck" class="sr-only">Wachwtoord herhalen</label>
                            <input type="password" name="passwordCheck" id="keyCheck" class="form-control">
                        </div>

                        <div class="form-group">
                            <input name="register" type="submit" class="btn btn-primary btn-block" value="Registreren" style="margin: 0 auto;">
                        </div>

                        <p class="center-align"><a href="index.php">Al een account?</a></p>
                    </form>
                </div>
            </div>
        </div> <!-- /.row -->
    </div> <!-- /.container -->

    <script>
        document.getElementById("username").oninput     = function() {userNameValidate()};
        document.getElementById("firstname").oninput    = function() {userFirstNameValidate()};
        document.getElementById("lastname").oninput     = function() {userLastNameValidate()};
        document.getElementById("email").oninput        = function() {emailValidate()};
        document.getElementById("key").oninput          = function() {passwordValidate()};
        document.getElementById("keyCheck").oninput     = function() {passwordCheckValidate()};
    </script>

</body>

</html>
