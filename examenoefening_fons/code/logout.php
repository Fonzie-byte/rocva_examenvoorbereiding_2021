<?php

session_start();

unset($_SESSION['login']);

header('Location: /examenoefening_fons');