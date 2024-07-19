<?php

class Model
{
   /**
     * @var null Database Connection
     */
    public static $DB = null;

  public function __construct()
  {

    try {
        self::open();
    } catch (\PDOException $e) {
        exit('Database connection could not be established.');
    }
    // Your "heavy" initialization stuff here
   
  }

  /**
     * Open the database connection with the credentials from application/config/config.php
     */
    private function open()
    {
        self::$DB = DB::connect('localhost', 'my_database', 'user', 'password');
    }

}