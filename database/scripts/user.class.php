<?php
    declare(strict_types = 1);
    require_once __DIR__ . '/database.php';

  class User {
    public int $id;
    public string $userName;
    public string $firstName;
    public string $lastName;
    public string $email;
    public ?string $phone;
    public string $creationDate;  
    public bool $isActive;
    public ?string $headline;
    public ?string $description;

    public function __construct(int $id, string $username, string $firstName,string $lastName, string $email, ?string $phone, string $creationDate, bool $isActive, ?string $headline, ?string $description)
    {
      $this->id = $id;
      $this->username= $username;
      $this->firstName = $firstName;
      $this->lastName = $lastName;
      $this->email = $email;
      $this->phone = $phone;
      $this->creationDate = $creationDate;
      $this->isActive = $isActive;
      $this->headline    = $headline;
      $this->description = $description;
    } 

    public function name(): string {
      return $this->firstName . ' ' . $this->lastName;
    }

    public function save(): void {
      $db   = Database::getInstance();
      $stmt = $db->prepare('UPDATE User SET FirstName = ?, LastName = ?, Email=?, Headline=?, Description=? WHERE UserId = ?');
      $stmt->execute([$this->firstName, $this->lastName, $this->email, $this->headline, $this->description ,$this->id]);
    }
    
    static function getUser(int $id) : ?User {
      $db = Database::getInstance();
      $stmt = $db->prepare('SELECT UserId, UserName, FirstName, LastName, Email, Phone, CreatedAt, IsActive, Headline, Description FROM User WHERE UserId = ?');
      $stmt->execute([$id]);
      $row = $stmt->fetch();

      if(! $row) return null;
    
      return new User(
        (int) $row['UserId'],
        (string) $row['UserName'],
        (string) $row['FirstName'],
        (string) $row['LastName'],
        (string) $row['Email'],
        $row['Phone'] !== null ? (string)$row['Phone'] : null,
        (string) $row['CreatedAt'],
        (bool) $row['IsActive'],
        $row['Headline'] !== null ? (string)$row['Headline']    : null,
        $row['Description'] !== null ? (string)$row['Description'] : null
      );
    }

    public static function authenticate(string $email, string $password): array {
      $db   = Database::getInstance();
      $stmt = $db->prepare('SELECT UserId, UserName, FirstName, LastName, Email, Phone, CreatedAt, IsActive, PasswordHash, Headline, Description FROM User WHERE lower(Email) = ?');
      $stmt->execute([strtolower($email)]);
      $row = $stmt->fetch();
      if (!$row) return ['status' => 'email_not_found'];
      if (!password_verify($password, (string)$row['PasswordHash'])) return ['status' => 'invalid_password'];

      $user = new User(
        (int)    $row['UserId'],
        (string) $row['UserName'],
        (string) $row['FirstName'],
        (string) $row['LastName'],
        (string) $row['Email'],
        $row['Phone'] !== null ? (string)$row['Phone'] : null,
        (string) $row['CreatedAt'],
        (bool)   $row['IsActive'],
        $row['Headline']    ?? null,
        $row['Description'] ?? null
      );

      return ['status' => 'success', 'user' => $user];
    }

    public static function register(string $username, string $firstName, string $lastName, string $email, string $password, ?string $headline = null, ?string $description = null): ?User {
      $db   = Database::getInstance();
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $db->prepare('INSERT INTO User (UserName, FirstName, LastName, Email, PasswordHash, Headline, Description) VALUES(:user, :first, :last, :email, :hash, :headline, :description)');
      if (!$stmt->execute([':user' => $username, ':first' => $firstName, ':last'  => $lastName, ':email' => $email,':hash' => $hash, ':headline' => $headline,':description' => $description])){
        echo "error";
        return null;
      } 
      
      $newId = (int) $db->lastInsertId();
      return self::getUser($newId);
    }

    public static function emailExists(string $email): bool {
      $db   = Database::getInstance();
      $stmt = $db->prepare('SELECT 1 FROM User WHERE lower(Email) = ?');
      $stmt->execute([strtolower($email)]);
      return (bool)$stmt->fetchColumn();
    }

    public static function usernameExists(string $username): bool {
      $db   = Database::getInstance();
      $stmt = $db->prepare('SELECT 1 FROM User WHERE UserName = ?');
      $stmt->execute([$username]);
      return (bool)$stmt->fetchColumn();
    }

    public static function isAdmin(int $userId): bool {
      $db   = Database::getInstance();
      $stmt = $db->prepare('SELECT 1 FROM Admin WHERE UserId = ?');
      $stmt->execute([$userId]);
      return (bool)$stmt->fetchColumn();
    }
  }
?>