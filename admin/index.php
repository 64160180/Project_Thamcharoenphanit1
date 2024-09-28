<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/login.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="login.css">
    <title>Login Page</title>
    <style>
        /* Add your custom CSS here */
        .textbox {
            position: relative;
            margin-bottom: 20px;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>

<body>
    <form action="login.php" method="post">
        <div class="login-box">
            <h2>ธรรมเจริญพาณิช</h2>

            <div class="textbox">
                <input type="text" placeholder="Username" name="username" value="">
            </div>

            <div class="textbox">
                <input type="password" placeholder="Password" name="password" id="password">
                <span class="toggle-password" onclick="togglePassword('password', 'toggleIcon')">
                    <i class="fa fa-eye" id="toggleIcon"></i>
                </span>
            </div>

            <input class="button" type="submit" name="login" value="Sign In">
        </div>
    </form>

    <script>
        function togglePassword(inputId, iconId) {
            var passwordField = document.getElementById(inputId);
            var toggleIcon = document.getElementById(iconId);
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        }
    </script>
</body>

</html>
