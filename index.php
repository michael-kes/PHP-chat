<?php include "includes/db.php"; ?>
<?php include "includes/functions.php"; ?>

<?php ob_start(); ?>
<?php session_start(); ?>

<?php
    if (isset($_SESSION['user_id'])) 
    {
        header("Location: groepen.php");          
    }
?>

<?php

    $errors = '';
    $error_color = 'green';

    if (isset($_POST['login'])) 
    {
        $username = escapeString($_POST['username']);
        $password = escapeString($_POST['password']);

        $query = "SELECT * FROM sw_user WHERE user_name = '{$username}'";
        $result = mysqli_query($connection, $query);

        confirmQuery($result);

        if(escapeString(mysqli_num_rows($result)) <= 0)
        {
            $errors .= 'Gebruikersnaam en wachtwoord komen niet overeen';
            $error_color = 'red';
        }
        else
        {
            while ($row = mysqli_fetch_array($result))
            {
                $db_id          = escapeString($row['user_id']);
                $db_username    = escapeString($row['user_name']);
                $db_password    = escapeString($row['user_password']);
                $db_firstname   = escapeString($row['user_firstname']);
                $db_lastname    = escapeString($row['user_lastname']);
                //$db_role        = escapeString($row['user_role']);
                $db_email       = escapeString($row['user_email']);
            }

            $verifyHashpassword = verifyPassword($password, $db_password);

            if($verifyHashpassword == true)
            {   
                setSession($db_id, $db_username, $db_firstname, $db_lastname, $db_email);
                header("Location: groepen.php");
            }
            else
            {
                $errors .= 'Gebruikersnaam en wachtwoord komen niet overeen';
                $error_color = 'red';
            } 
        }
    }
?>
    
<?php include "includes/header.php"; ?>

    <div class="container">
        <div class="row">
            <div class="col s4 offset-s4">
                <!-- Blog Login Well -->
                <div class="card-panel white" style="margin-top: 50%;">
                    <h4 class="center-align">Login</h4>
                    <form action="" method="post">
                        <?php if($errors != ""): ?>
                            <div style='color: <?php echo $error_color; ?>' class="center-align">
                                <?php echo $errors; ?>
                            </div><br>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="username" class="sr-only">Gebruikersnaam</label>
                            <input type="text" class="form-control" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="username" class="sr-only">Wachtwoord</label>
                            <input type="password" class="form-control" name="password">
                        </div>

                        <div class="form-group">
                            <input name="login" type="submit" class="btn btn-primary btn-block" value="Log in" style="margin: 0 auto;">
                        </div>

                        <p class="center-align"><a href="register.php">Registreren</a></p> 
                    </form>
                    <!-- /.input-group -->
                </div>
            </div>
        </div>
    </div>

</body>

</html>