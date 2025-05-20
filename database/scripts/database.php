<?php
declare(strict_types=1);

class Database {
  private static ?PDO $instance = null;

  public static function getInstance(): PDO {
    if (self::$instance === null) {
      self::$instance = new PDO( 'sqlite:' . __DIR__ . '/../files/database.db');
      self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      self::$instance->exec('PRAGMA foreign_keys = ON;'); //not to sure if necessary but i will check later
    }
      return self::$instance;
  }

}