<?php
    declare(strict_types = 1);

  class User {
    public int $id;
    public string $userName;
    public string $firstName;
    public string $lastName;
    public string $email;
    public string $phone;
    public string $creationDate;
    public $isActive;

    public function __construct(int $id, string $username, string $firstName,string $lastName, string $email, string $phone, string $creationDate, $isActive)
    {
      $this->id = $id;
      $this->username= $username;
      $this->firstName = $firstName;
      $this->lastName = $lastName;
      $this->email = $email;
      $this->phone = $phone;
      $this->creationDate = $creationDate;
      $this->isActive = $isActive;
    }

    function name() {
      return $this->firstName . ' ' . $this->lastName;
    }

    function save($db) {
      $stmt = $db->prepare('
        UPDATE User SET FirstName = ?, LastName = ?
        WHERE UserId = ?
      ');

      $stmt->execute(array($this->firstName, $this->lastName, $this->id));
    }
    
    static function getUserWithPassword(PDO $db, string $email, string $password) : ?User {
      $stmt = $db->prepare('
        SELECT UserId, UserName, FirstName, LastName, Email, Phone, CreatedAt, IsActive
        FROM User 
        WHERE lower(email) = ? AND passwordHash = ?
      ');

      $stmt->execute(array(strtolower($email), sha1($password)));
  
      if ($User = $stmt->fetch()) {
        return new User(
          $User['UserId'],
          $User['UserName'],
          $User['FirstName'],
          $User['LastName'],
          $User['Email'],
          $User['Phone'],
          $User['CreatedAt'],
          $User['IsActive']
        );
      } else return null;
    }

    static function getUser(PDO $db, int $id) : User {
      $stmt = $db->prepare('
        SELECT UserId, FirstName, LastName, Address, City, State, Country, PostalCode, Phone, Email
        FROM User 
        WHERE UserId = ?
      ');

      $stmt->execute(array($id));
      $User = $stmt->fetch();
      
      echo "assdas";
      return new User(
        $User['UserId'],
          $User['UserName'],
          $User['FirstName'],
          $User['LastName'],
          $User['Email'],
          $User['Phone'],
          $User['CreatedAt'],
          $User['IsActive']
      );
    }
  }
?>