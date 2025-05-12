PRAGMA foreign_keys = ON;
DROP TABLE IF EXISTS Service;
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS FreeLancer;
DROP TABLE IF EXISTS Client;
DROP TABLE IF EXISTS Admin;
DROP TABLE IF EXISTS Comment;

CREATE TABLE User (
    UserId INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    UserName TEXT UNIQUE NOT NULL,
    FirstName TEXT NOT NULL, -- NVARCHAR(50) mapeado para TEXT
    LastName TEXT NOT NULL,  -- NVARCHAR(50) mapeado para TEXT
    Email TEXT UNIQUE NOT NULL, -- NVARCHAR(100) mapeado para TEXT
    PasswordHash TEXT NOT NULL, -- Armazenar HASH+SALT!
    Phone TEXT,                 -- NVARCHAR(24) mapeado para TEXT
    CreatedAt TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Formato 'YYYY-MM-DD HH:MM:SS'
    IsActive INTEGER NOT NULL DEFAULT 1 CHECK (IsActive IN (0, 1)) -- BOOLEAN como INTEGER (0 ou 1)
);


CREATE TABLE FreeLancer (
    UserId INTEGER PRIMARY KEY NOT NULL,
    Headline TEXT,          -- NVARCHAR(150) mapeado para TEXT
    Description TEXT,       -- NVARCHAR(2000) mapeado para TEXT
    -- NOTA: REAL em SQLite é floating-point. Para valores monetários precisos,
    FOREIGN KEY (UserId) REFERENCES User (UserId) ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE TABLE Client (
    UserId INTEGER PRIMARY KEY NOT NULL,
    FOREIGN KEY (UserId) REFERENCES User (UserId) ON UPDATE CASCADE ON DELETE CASCADE
);


Create Table Admin(
     UserId INTEGER NOT NULL,
    FOREIGN KEY (UserId) REFERENCES User (UserId)
	    ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE Service (
    ServiceId INTEGER PRIMARY KEY AUTOINCREMENT,
    FreelancerUserId INTEGER NOT NULL,
    CategoryId INTEGER NOT NULL,
    Title TEXT NOT NULL,        -- NVARCHAR(150) mapeado para TEXT
    Description TEXT NOT NULL,  -- NVARCHAR(2000) mapeado para TEXT
    -- NOTA: Usar REAL para moeda pode levar a problemas de precisão. Ver nota em FreeLancer.HourlyRate.
    BasePrice REAL NOT NULL CHECK (BasePrice >= 0), -- DECIMAL(10, 2) mapeado para REAL
    Currency TEXT NOT NULL DEFAULT 'EUR', -- NVARCHAR(3) mapeado para TEXT
    DeliveryDays INTEGER CHECK (DeliveryDays IS NULL OR DeliveryDays > 0),
    Revisions INTEGER DEFAULT 1,
    IsActive INTEGER NOT NULL DEFAULT 1 CHECK (IsActive IN (0, 1)), -- BOOLEAN
    CreatedAt TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TEXT, -- TIMESTAMP mapeado para TEXT

    FOREIGN KEY (FreelancerUserId) REFERENCES User (UserId) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (CategoryId) REFERENCES Category (CategoryId) ON UPDATE CASCADE ON DELETE RESTRICT
);


CREATE TABLE Comment (
    CommentId INTEGER PRIMARY KEY AUTOINCREMENT,
    JobOrderId INTEGER UNIQUE NOT NULL, -- Garante 1 comentário por JobOrder
    ClientId INTEGER NOT NULL,
    ServiceId INTEGER NOT NULL, -- Redundante com JobOrderId mas pode ajudar em queries
    Rating INTEGER NOT NULL CHECK (Rating >= 1 AND Rating <= 5),
    Description TEXT, -- NVARCHAR(1000) mapeado para TEXT
    CommentDate TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (JobOrderId) REFERENCES JobOrder (JobOrderId) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (ClientId) REFERENCES User (UserId) ON UPDATE CASCADE ON DELETE NO ACTION,
    FOREIGN KEY (ServiceId) REFERENCES Service (ServiceId) ON UPDATE CASCADE ON DELETE NO ACTION
);


CREATE TABLE Category (
    CategoryId INTEGER PRIMARY KEY AUTOINCREMENT, -- SERIAL mapeado para INTEGER PK AUTOINCREMENT
    Name TEXT UNIQUE NOT NULL, -- NVARCHAR(100) mapeado para TEXT
    Description TEXT,          -- NVARCHAR(255) mapeado para TEXT
    ParentCategoryId INTEGER,
    FOREIGN KEY (ParentCategoryId) REFERENCES Category (CategoryId) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE JobOrder (
    JobOrderId INTEGER PRIMARY KEY AUTOINCREMENT,
    ServiceId INTEGER NOT NULL,
    ClientId INTEGER NOT NULL,
    FreelancerUserId INTEGER NOT NULL,
    -- NOTA: Usar REAL para moeda pode levar a problemas de precisão. Ver nota em FreeLancer.HourlyRate.
    AgreedPrice REAL NOT NULL, -- DECIMAL(10, 2) mapeado para REAL
    Currency TEXT NOT NULL,    -- NVARCHAR(3) mapeado para TEXT
    Status TEXT NOT NULL DEFAULT 'Pending' CHECK (Status IN ('Pending', 'Accepted', 'InProgress', 'Delivered', 'Revision', 'Completed', 'Disputed', 'Cancelled')), -- NVARCHAR(20)
    Requirements TEXT,
    OrderDate TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    StartDate TEXT,
    ExpectedDeliveryDate TEXT,
    ActualDeliveryDate TEXT,
    CompletionDate TEXT,

    FOREIGN KEY (ServiceId) REFERENCES Service (ServiceId) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (ClientId) REFERENCES User (UserId) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (FreelancerUserId) REFERENCES User (UserId) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- --- Criação de Índices para Otimização ---
CREATE INDEX idx_joborder_client ON JobOrder (ClientId);
CREATE INDEX idx_joborder_freelancer ON JobOrder (FreelancerUserId);
CREATE INDEX idx_joborder_status ON JobOrder (Status);

CREATE INDEX idx_comment_client ON Comment (ClientId);
CREATE INDEX idx_comment_service ON Comment (ServiceId);

CREATE INDEX idx_service_category ON Service (CategoryId);
CREATE INDEX idx_service_freelancer ON Service (FreelancerUserId);


-- ─── Temporary seed data for testing ─────────────────────────────────────

PRAGMA foreign_keys = OFF;
BEGIN TRANSACTION;

-- 3) some categories
INSERT INTO Category (Name, Description) VALUES
  ('Development & IT','All your coding needs'),
  ('Design & Creative','Logos, graphics, UI/UX'),
  ('Writing & Translation','Copywriting, translation'),
  ('Sales & Marketing','Ad campaigns, SEO'),
  ('Finance & Accounting','Bookkeeping, CFO services'),
  ('Video & Animation',     'Explainers, commercials, motion graphics'),
  ('Music & Audio',         'Voice‐overs, jingles, editing'),
  ('Business Consulting',   'Strategy, operations, market research'),
  ('Legal Services',        'Contracts, IP, compliance'),
  ('Lifestyle',             'Cooking, fitness, crafts');


COMMIT;
PRAGMA foreign_keys = ON;