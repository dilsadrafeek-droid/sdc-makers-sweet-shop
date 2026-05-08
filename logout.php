<?php
session_start();
session_destroy();
header("Location: /sweet_shop/index.php");
exit;

