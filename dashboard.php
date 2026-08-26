<?php

session_start();

if (!isset($_SESSION["id_docente"])) {

    header("Location: index.php");
    exit();

}

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<title>Dashboard</title>

<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

    <h1>Bienvenido, <?php echo $_SESSION["nombre"]; ?> 👋</h1>

    <hr>

    <h3>Panel principal de IncluKids</h3>

</div>

</body>
</html>