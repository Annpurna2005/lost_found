<?php
session_start();
session_destroy();
header("Location: ../views/login.html"); // जहाँ तुमने login फॉर्म रखा है
exit();
?>
