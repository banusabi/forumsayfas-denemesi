<?php
session_start();
session_unset();
session_destroy();
header('Location: ../munforum/index.php');
exit;
