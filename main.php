<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";  // Default XAMPP username
$password = "";  // Default XAMPP password is empty
$dbname = "regisdb";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        switch ($user['role']) {
            case 'admin':
                header("Location: admin.html");
                break;
            case 'manager':
                header("Location: manager2.html");
                break;
            case 'employee':
                header("Location: Employee.html");
                break;
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>HRM Login</title>
        <style>
            body{

                margin: 0;
                height: 100vh;
                background-color: #dcdcdd;
                display: flex;
                justify-content: center;
                align-items: center;

            }
            .boxoflogin{
                background-color: rgb(255, 255, 255);
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
               
                width: 40%;
                
            }
            .button{
                padding: 10px;
                background-color: #007bff;
                border: none;
                color: #ffffff;
                font-size: 16px;
                border-radius: 8px;
                cursor: pointer;
                width: 65%;
            }
            .button:hover{
                background-color: #0056b3;
            }

            @media (max-width: 599px) {
              .boxoflogin{
                padding: 15px;
                font-size: 20px;
                width:100%;
              }
              h2{
                font-size: 25px;
              }

              
           } 

        </style>
    </head>
    <body>
        <div class="boxoflogin"><br>
            <h2 style="text-align: center;"> HRM  System Login</h2><br><br>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="post" action="" style="text-align: center;font-size:20px;">
                Username:
                <input type="text" id="username" name="username" placeholder="Enter your Username" style="width:50%;height:30px; font-size:15px;border-radius: 8px;"required>
                <br><br><br>
                Password :
                <input type="password" id="password" name="password" placeholder="Enter your Password"style="width:50%;height:30px; font-size:15px;border-radius: 8px;" required>
                <br><br><br>
                <button  type="submit" class="button" >Login</button>
                <br><br>

            </form>
            <p style="text-align: center; font-size: 16px;"> Login as <strong>Admin | Manager | Employee</strong></p><br><br>
        </div>

    </body>

</html>
