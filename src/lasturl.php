<?php
session_start();
echo isset($_SESSION['lasturl']) ? urldecode($_SESSION['lasturl']) : null;
