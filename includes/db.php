<?php
include_once __DIR__ . '/config.php';
class Database
{
  private static $connection;
  public static function getConnection()
  {
    if (!isset(self::$connection)) {
      self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306);
      if (self::$connection->connect_error) {
        die("Connection failed: " . self::$connection->connect_error);
      }
    }
    return self::$connection;
  }
  public static function iud($query, $types, $params)
  {
    $conn = self::getConnection();
    $statement = $conn->prepare($query);
    $statement->bind_param($types, ...$params);
    $result = $statement->execute();
    $statement->close();
    return $result;
  }

  public static function search($query, $types = null, $params = [])
  {
    $conn = self::getConnection();
    $statement = $conn->prepare($query);

    if ($types != null && !empty($params)) {
      $statement->bind_param($types, ...$params);
    }

    $statement->execute();
    $result = $statement->get_result();
    $statement->close();
    return $result;
  }
}
