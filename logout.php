<?php

require_once __DIR__ . "/app/helpers/session_boot.php";
sesion_iniciar();
session_destroy();

header("Location: index.php");
exit();