<?php
include($_SERVER['DOCUMENT_ROOT']."/bear/class/authUsers.php");

    $authUsers = new authUsers();
    $authUsers->checkLogin();
