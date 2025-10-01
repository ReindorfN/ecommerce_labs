<?php
require_once 'settings/core.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill-Office Africa | Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        
        .navbar {
            position: fixed;
            top: 20px;
            left: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 150px;
        }
        
        .navbar a {
            text-decoration: none;
        }
        
        .navbar button {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        
        .navbar button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .navbar button:active {
            transform: translateY(0);
        }
        
        .container {
            max-width: 800px;
            margin: 100px auto 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .welcome-text {
            text-align: center;
            font-size: 18px;
            line-height: 1.6;
            color: #333;
        }
        
        .highlight {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <?php
        session_start();
        if (!isLoggedIn()){
            echo " <a href='login/login.php'><button>Login</button></a>";
            echo " <a href='login/register.php'><button>Register</button></a>";
        } 
        if(isLoggedIn()&& isAdmin()){
            echo " <a href='admin/category.php'><button>Category Dashboard</button></a>";
            echo " <a href='functions/logout_user_action.php'><button>Logout</button></a>";
        }
        if(isLoggedIn() && !isAdmin()){
            echo " <a href='functions/logout_user_action.php'><button>Logout</button></a>";
        }

        ?>
    </nav>
    
    <div class="container">
        <div class="welcome-text">
            <h1>Welcome to E-commerce Lab</h1>
            <p>Please use the <span class="highlight">Login</span> or <span class="highlight">Register</span> options in the menu to sign in </br> or create a new account.</p>
            
        </div>
    </div>
    


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/logout.js"></script>
</body>
</html>