<?php
        ob_start();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? ''; // mendapatkan username dari POST request
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? ''; // mendapatkan password dari POST request
            $confirmPassword = $_POST['confirm-password'] ?? '';

            $errors = [];

            if (empty($username)) {
                $errors[] = "Username is required.";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "A valid email is required.";
            }
            if (empty($password)) {
                $errors[] = "Password is required.";
            }
            if ($password !== $confirmPassword) {
                $errors[] = "Passwords do not match.";
            }

            if (headers_sent($file, $line)) { //mengecek apakah header sudah dikirim sebelumnya
                die("Header sudah dikirim di $file pada baris $line");
            }

            if (empty($errors)) { //mengantar user ke halaman selanjutnya
                header("Location: main.php");
                exit;
            } else {
                foreach ($errors as $error) {
                    echo "<p class='error'>$error</p>";
                }
            }
            ob_end_flush();
        }
        ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
            background-color: #F4EFEA; /* Earthtone base */
            color: #4A403A; /* Earthtone text */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #EDE5DA;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 350px;
            border: 1px solid #D6CDBA;
        }
        .login-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #8C6A5A;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #D6CDBA;
            border-radius: 4px;
            background-color: #F4EFEA;
            margin: 10;
        }
        .form-group input:focus {
            border-color: #8C6A5A;
            outline: none;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background: #8C6A5A;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .form-group button:hover {
            background: #6E5044;
        }
        .error {
            color: red;
            font-size: 0.9em;
            margin-top: -10px;
            margin-bottom: 10px;
        }
        #password-strength {
            font-size: 0.9em;
            margin-left: 5px;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");

            // Validasi form saat submit (event handling)
            form.addEventListener("submit", function(event) {
                const username = document.getElementById("username").value;
                const email = document.getElementById("email").value;
                const password = document.getElementById("password").value;
                const confirmPassword = document.getElementById("confirm-password").value;

                let errors = [];

                if (!username.trim()) {
                    errors.push("Username is required.");
                }

                if (!email.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    errors.push("A valid email is required.");
                }

                if (!password.trim() || password.length < 6) {
                    errors.push("Password must be at least 6 characters long.");
                }

                if (password !== confirmPassword) {
                    errors.push("Passwords do not match.");
                }

                if (errors.length > 0) {
                    event.preventDefault();
                    alert(errors.join("\n"));
                }
            });

            // Validasi Real Time
            const emailInput = document.getElementById("email");
            emailInput.addEventListener("input", function() {
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
                    emailInput.style.borderColor = "red";
                } else {
                    emailInput.style.borderColor = "green";
                }
            });

            // Mengecek password
            const passwordInput = document.getElementById("password");
            passwordInput.addEventListener("input", function() {
                const strength = document.getElementById("password-strength");
                if (passwordInput.value.length < 6) {
                    strength.textContent = "Weak";
                    strength.style.color = "red";
                } else if (passwordInput.value.length < 10) {
                    strength.textContent = "Moderate";
                    strength.style.color = "orange";
                } else {
                    strength.textContent = "Strong";
                    strength.style.color = "green";
                }
            });

            // Validasi password secara real time
            const confirmPasswordInput = document.getElementById("confirm-password");
            confirmPasswordInput.addEventListener("input", function() {
                if (confirmPasswordInput.value !== passwordInput.value) {
                    confirmPasswordInput.style.borderColor = "red";
                } else {
                    confirmPasswordInput.style.borderColor = "green";
                }
            });
        });
    </script>
</head>
<body>
    <div class="login-container">
        <h1>login</h1>
       
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <span id="password-strength" style="font-size: 0.9em; margin-left: 5px;"></span>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>
    </div>
    
</body>
</html>

