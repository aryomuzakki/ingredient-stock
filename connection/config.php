<?php
  // load env
  $env = parse_ini_file("./.env");

  // default local
  $db_host = $env["DB_HOST"] ?? "localhost";
  $db_username = $env["DB_USERNAME"] ?? "root";
  $db_password = $env["DB_PASSWORD"] ?? "";
  $db_name = $env["DB_NAME"] ?? "db_ingredientstock";

  // timezone
  date_default_timezone_set("Asia/Jakarta");
?>