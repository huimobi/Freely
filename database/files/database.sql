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
        FirstName TEXT NOT NULL,
        LastName TEXT NOT NULL,
        Email TEXT UNIQUE NOT NULL,
        PasswordHash TEXT NOT NULL,
        Phone TEXT,
        Headline TEXT,
        Description TEXT,
        CreatedAt TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        IsActive INTEGER NOT NULL DEFAULT 1 CHECK (IsActive IN (0, 1))
    );

    Create Table Admin(
        UserId INTEGER NOT NULL,
        FOREIGN KEY (UserId) REFERENCES User (UserId)
            ON DELETE NO ACTION ON UPDATE NO ACTION
    );

    CREATE TABLE Service (
        ServiceId INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        SellerUserId INTEGER NOT NULL,
        CategoryId INTEGER NOT NULL,
        Title TEXT NOT NULL,
        Description TEXT NOT NULL,
        BasePrice REAL NOT NULL CHECK (BasePrice >= 0), -- DECIMAL(10, 2) mapeado para REAL
        Currency TEXT NOT NULL DEFAULT 'EUR', -- NVARCHAR(3) mapeado para TEXT
        DeliveryDays INTEGER CHECK (DeliveryDays IS NULL OR DeliveryDays > 0),
        Revisions INTEGER DEFAULT 1,
        IsActive INTEGER NOT NULL DEFAULT 1 CHECK (IsActive IN (0, 1)), -- BOOLEAN
        CreatedAt TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UpdatedAt TEXT, -- TIMESTAMP mapeado para TEXT

        FOREIGN KEY (SellerUserId) REFERENCES User (UserId) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (CategoryId) REFERENCES Category (CategoryId) ON UPDATE CASCADE ON DELETE RESTRICT
    );

    CREATE TABLE Category (
        CategoryId INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, -- SERIAL mapeado para INTEGER PK AUTOINCREMENT
        Name TEXT UNIQUE NOT NULL, -- NVARCHAR(100) mapeado para TEXT
        Description TEXT,          -- NVARCHAR(255) mapeado para TEXT
        ParentCategoryId INTEGER,
        FOREIGN KEY (ParentCategoryId) REFERENCES Category (CategoryId) ON UPDATE CASCADE ON DELETE SET NULL
    );

    CREATE TABLE Tag (
        TagId INTEGER PRIMARY KEY AUTOINCREMENT,
        Name TEXT NOT NULL UNIQUE
    );

    CREATE TABLE IF NOT EXISTS ServiceTag (
        ServiceId INTEGER,
        TagId INTEGER,
        PRIMARY KEY (ServiceId, TagId),
        FOREIGN KEY (ServiceId) REFERENCES Service(ServiceId) ON DELETE CASCADE,
        FOREIGN KEY (TagId) REFERENCES Tag(TagId) ON DELETE CASCADE
    );

    CREATE TABLE JobOrder (
        JobOrderId INTEGER PRIMARY KEY AUTOINCREMENT,
        ServiceId INTEGER NOT NULL,
        BuyerUserId INTEGER NOT NULL,
        SellerUserId INTEGER NOT NULL,

        AgreedPrice REAL NOT NULL,
        Currency TEXT NOT NULL,
        Status TEXT NOT NULL DEFAULT 'Pending' CHECK (Status IN ('Pending', 'Accepted', 'InProgress', 'Delivered', 'Revision', 'Completed', 'Disputed', 'Cancelled')), -- NVARCHAR(20)
        Requirements TEXT,
        OrderDate TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        StartDate TEXT,
        ExpectedDeliveryDate TEXT,
        ActualDeliveryDate TEXT,
        CompletionDate TEXT,

        FOREIGN KEY (ServiceId) REFERENCES Service (ServiceId) ON UPDATE CASCADE ON DELETE RESTRICT,
        FOREIGN KEY (BuyerUserId) REFERENCES User (UserId) ON UPDATE CASCADE ON DELETE RESTRICT,
        FOREIGN KEY (SellerUserId) REFERENCES User (UserId) ON UPDATE CASCADE ON DELETE RESTRICT
    );

    CREATE TABLE Comment (
        CommentId INTEGER PRIMARY KEY AUTOINCREMENT,
        JobOrderId INTEGER UNIQUE NOT NULL,
        BuyerUserId INTEGER NOT NULL,
        ServiceId INTEGER NOT NULL,
        Rating INTEGER NOT NULL CHECK (Rating >= 1 AND Rating <= 5),
        Description TEXT,
        CommentDate TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,

        FOREIGN KEY (JobOrderId) REFERENCES JobOrder (JobOrderId) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (BuyerUserId) REFERENCES User (UserId) ON UPDATE CASCADE ON DELETE NO ACTION,
        FOREIGN KEY (ServiceId) REFERENCES Service (ServiceId) ON UPDATE CASCADE ON DELETE NO ACTION
    );

    -- --- Criação de Índices para Otimização ---
    CREATE INDEX idx_service_seller ON Service(SellerUserId);
    CREATE INDEX idx_service_category ON Service(CategoryId);
    CREATE INDEX idx_order_buyer ON JobOrder(BuyerUserId);
    CREATE INDEX idx_order_seller ON JobOrder(SellerUserId);
    CREATE INDEX idx_order_status ON JobOrder(Status);
    CREATE INDEX idx_comment_buyer ON Comment(BuyerUserId);
    CREATE INDEX idx_comment_service ON Comment(ServiceId);
    CREATE INDEX idx_service_tag_tagid ON ServiceTag(TagId);
    CREATE INDEX idx_service_tag_serviceid ON ServiceTag(ServiceId);
    CREATE INDEX idx_tag_name ON Tag(Name);


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