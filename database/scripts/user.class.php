<?php
    declare(strict_types = 1);
    require_once __DIR__ . '/../files/database.php';

  class User {
    public int $id;
    public string $userName;
    public string $firstName;
    public string $lastName;
    public string $email;
    public ?string $phone;
    public string $creationDate;  
    public $isActive;

    public function __construct(int $id, string $username, string $firstName,string $lastName, string $email, ?string $phone, string $creationDate, $isActive)
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

    public function name(): string {
      return $this->firstName . ' ' . $this->lastName;
    }

    public function save(): void {
      $db   = Database::getInstance();
      $stmt = $db->prepare('UPDATE User SET FirstName = ?, LastName = ? WHERE UserId = ?');
      $stmt->execute([$this->firstName, $this->lastName, $this->id]);
    }
    
    public static function getUserWithPassword(string $email, string $password) : ?User {
      $db   = Database::getInstance();
      $stmt = $db->prepare('SELECT * FROM User WHERE lower(email) = ? AND PasswordHash = ? ');
      $stmt->execute([strtolower($email), password_hash($password)]);
      $row = $stmt->fetch();
      if (!$row ) return null;
      
      return new User(
        (int)    $User['UserId'],
        (string) $User['UserName'],
        (string) $User['FirstName'],
        (string) $User['LastName'],
        (string) $User['Email'],
        $row['Phone'] !== null ? (string)$row['Phone'] : null,
        (string) $User['CreatedAt'],
        (bool)   $User['IsActive']
      );
    }

    static function getUser(int $id) : ?User {
      $db   = Database::getInstance();
      $stmt = $db->prepare('SELECT UserId, UserName, FirstName, LastName, Email, Phone, CreatedAt, IsActive FROM User WHERE UserId = ?');
      $stmt->execute([$id]);
      $User = $stmt->fetch();

      if(! $row) return null;
    
      return new User(
        (int)    $User['UserId'],
        (string) $User['UserName'],
        (string) $User['FirstName'],
        (string) $User['LastName'],
        (string) $User['Email'],
        $row['Phone'] !== null ? (string)$row['Phone'] : null,
        (string) $User['CreatedAt'],
        (bool)   $User['IsActive']
      );
    }

    public static function register(string $username, string $firstName, string $lastName, string $email, string $password): ?User {
      $db   = Database::getInstance();
      $hash = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $db->prepare('INSERT INTO User (UserName, FirstName, LastName, Email, PasswordHash) VALUES(:user, :first, :last, :email, :hash)');

      if (! $stmt->execute([':user'  => $username, ':first' => $firstName, ':last'  => $lastName, ':email' => $email,':hash'  => $hash])) return null;
      
      $newId = (int) $db->lastInsertId();
      return self::getUser($db, $newId);
    }

    public static function isClient(int $userId): bool {
      $db   = Database::getInstance();
      $stmt = $db->prepare('SELECT 1 FROM Client WHERE UserId = ?');
      $stmt->execute([$userId]);
      return (bool)$stmt->fetchColumn();
    }

    public static function isFreelancer(int $userId): bool {
      $db   = Database::getInstance();
      $stmt = $db->prepare('SELECT 1 FROM FreeLancer WHERE UserId = ?');
      $stmt->execute([$userId]);
      return (bool) $stmt->fetchColumn();
    }

    public static function isAdmin(int $userId): bool {
      $db   = Database::getInstance();
      $stmt = $db->prepare('SELECT 1 FROM Admin WHERE UserId = ?');
      $stmt->execute([$userId]);
      return (bool)$stmt->fetchColumn();
    }
  }
?>