<?php
session_start();
require_once 'db_config.php';

// Jika sudah login, redirect ke home
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}

// Proses login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verifikasi password (gunakan password_verify jika passwordnya di-hash)
        if ($password === $user['password']) { // Untuk produksi, gunakan password_verify()
            // Login berhasil
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            header("Location: home.php");
            exit;
        } else {
            $error = "Password salah";
        }
    } else {
        $error = "Username tidak ditemukan";
    }
    
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Monitoring Tanaman Cabai</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f7fa;
        }
        
        .login-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        
        .login-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        
        .login-form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .login-button {
            width: 100%;
            padding: 12px;
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .login-button:hover {
            background-color: #16a085;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-title">SISTEM MONITORING TANAMAN CABAI</div>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form class="login-form" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="login-button">LOGIN</button>
        </form>
    </div>
</body>
</html>