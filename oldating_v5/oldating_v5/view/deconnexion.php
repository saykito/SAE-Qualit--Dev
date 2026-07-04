<?php
session_start();
session_unset(); // supprime toutes les variables de session
session_destroy(); // détruit la session côté serveur
header('Location: authentification.php');
exit();
