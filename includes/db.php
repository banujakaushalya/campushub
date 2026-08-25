<?php
include_once __DIR__ . '/config.php';

class Database
{
  private static $connection = null;

  public static function getConnection()
  {
    if (self::$connection === null) {
      // Turn off default error reporting to prevent fatal connection crashes
      mysqli_report(MYSQLI_REPORT_OFF);
      $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306);
      if ($conn->connect_error) {
        return null;
      }
      $conn->set_charset("utf8mb4");
      self::$connection = $conn;
    }
    return self::$connection;
  }

  public static function iud($query, $types = "", $params = [])
  {
    $conn = self::getConnection();
    if (!$conn) {
      return false;
    }
    $statement = $conn->prepare($query);
    if (!$statement) {
      return false;
    }
    if (!empty($types) && !empty($params)) {
      $statement->bind_param($types, ...$params);
    }
    $result = $statement->execute();
    $statement->close();
    return $result;
  }

  public static function search($query, $types = null, $params = [])
  {
    $conn = self::getConnection();
    if (!$conn) {
      return false;
    }
    $statement = $conn->prepare($query);
    if (!$statement) {
      return false;
    }
    if ($types != null && !empty($params)) {
      $statement->bind_param($types, ...$params);
    }
    $statement->execute();
    $result = $statement->get_result();
    $statement->close();
    return $result;
  }
}

