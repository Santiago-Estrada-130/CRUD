<?php

session_start();
include 'conexion.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_POST['username'];
	$password = md5($_POST['password']);
	
	$sql = "SELECT * FROM usuarios WHERE username = '$username' AND password = '$password'";
	$result = $conn->query($sql);
	
	if($result->num_rows > 0){
		$_SESSION['username'] = $username;
		header("Location: viajes.php");
		exit();
		} else {
			echo "Nombre de usuario o contraseña incorrectos";
	}
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <link href="estilo1.css" rel="stylesheet" type="text/css">
  <meta charset="utf-8">
  <title>Login</title>
</head>
<body>

  <div class="login-container">
    <h2>Iniciar Sesión</h2>
    <form method="post" action="login.php">
      <div class="input-group">
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="input-group">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit">Ingresar</button>
    </form>
  </div>

</body>
</html>
