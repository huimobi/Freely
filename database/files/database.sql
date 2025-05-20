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
        FOREIGN KEY (UserId) REFERENCES User (UserId) ON DELETE CASCADE ON UPDATE NO ACTION
    );

    CREATE TABLE Service (
        ServiceId INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        SellerUserId INTEGER NOT NULL,
        CategoryId INTEGER NOT NULL,
        Title TEXT NOT NULL,
        Description TEXT NOT NULL,
        BasePrice REAL NOT NULL CHECK (BasePrice >= 0),
        Currency TEXT NOT NULL DEFAULT 'EUR',
        DeliveryDays INTEGER CHECK (DeliveryDays IS NULL OR DeliveryDays > 0),
        Revisions INTEGER DEFAULT 1,
        IsActive INTEGER NOT NULL DEFAULT 1 CHECK (IsActive IN (0, 1)),
        CreatedAt TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UpdatedAt TEXT,

        FOREIGN KEY (SellerUserId) REFERENCES User (UserId) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (CategoryId) REFERENCES Category (CategoryId) ON UPDATE CASCADE ON DELETE RESTRICT
    );

    CREATE TABLE Category (
        CategoryId INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        Name TEXT UNIQUE NOT NULL,
        Description TEXT,
        ParentCategoryId INTEGER,
        FOREIGN KEY (ParentCategoryId) REFERENCES Category (CategoryId) ON UPDATE CASCADE ON DELETE SET NULL
    );

    CREATE TABLE Tag (
        TagId INTEGER PRIMARY KEY AUTOINCREMENT,
        Name TEXT NOT NULL UNIQUE
    );

    CREATE TABLE ServiceTag (
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

        FOREIGN KEY (ServiceId) REFERENCES Service (ServiceId) ON DELETE CASCADE ON UPDATE NO ACTION,
        FOREIGN KEY (BuyerUserId) REFERENCES User (UserId) ON DELETE CASCADE ON UPDATE NO ACTION,
        FOREIGN KEY (SellerUserId) REFERENCES User (UserId) ON DELETE CASCADE ON UPDATE NO ACTION
    );

    CREATE TABLE Comment (
        CommentId INTEGER PRIMARY KEY AUTOINCREMENT,
        JobOrderId INTEGER UNIQUE NOT NULL,
        BuyerUserId INTEGER NOT NULL,
        ServiceId INTEGER NOT NULL,
        Rating INTEGER NOT NULL CHECK (Rating >= 1 AND Rating <= 5),
        Description TEXT,
        CommentDate TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,

        FOREIGN KEY (JobOrderId) REFERENCES JobOrder (JobOrderId) ON DELETE CASCADE ON UPDATE NO ACTION,
        FOREIGN KEY (BuyerUserId) REFERENCES User (UserId) ON DELETE CASCADE ON UPDATE NO ACTION,
        FOREIGN KEY (ServiceId) REFERENCES Service (ServiceId) ON DELETE CASCADE ON UPDATE NO ACTION
    );

    CREATE TABLE Message (
        MessageId INTEGER PRIMARY KEY AUTOINCREMENT,
        SenderUserId INTEGER NOT NULL,
        ReceiverUserId INTEGER NOT NULL,
        Content TEXT NOT NULL,
        Timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,

        FOREIGN KEY (SenderUserId) REFERENCES User(UserId) ON DELETE CASCADE ON UPDATE NO ACTION,
        FOREIGN KEY (ReceiverUserId) REFERENCES User(UserId) ON DELETE CASCADE ON UPDATE NO ACTION
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
    CREATE INDEX idx_message_sender ON Message(SenderUserId);
    CREATE INDEX idx_message_receiver ON Message(ReceiverUserId);


    --------------------------- Temporary seed data for testing ---------------------------
    PRAGMA foreign_keys = OFF;
    BEGIN TRANSACTION;

    INSERT OR IGNORE INTO Category (CategoryId, Name, Description) VALUES (1, 'Other', 'Fallback for uncategorized services');

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

    -- 5 Users with extra fields
    INSERT INTO User (UserName, FirstName, LastName, Email, PasswordHash, Headline, Description, Phone) VALUES 
    ('ana_dev', 'Ana', 'Silva', 'ana@example.com', '$2y$10$aAcWsrPCydUOca8RkrwBJevKrY0/GMtsIaRU6Pq1w2cYtYrSX93K6', 'Full-Stack Developer', 'I build robust web applications with modern tech.', '912345678'),
    ('bruno_code', 'Bruno', 'Pereira', 'bruno@example.com', 'hashed_pw2', 'Frontend Specialist', 'Passionate about clean UI and smooth UX.', '913456789'),
    ('carla_js', 'Carla', 'Oliveira', 'carla@example.com', 'hashed_pw3', 'JavaScript Enthusiast', 'I solve problems using code and creativity.', '914567890'),
    ('daniel_php', 'Daniel', 'Santos', 'daniel@example.com', 'hashed_pw4', 'PHP Expert', '10+ years writing scalable backend systems.', '915678901'),
    ('eva_uiux', 'Eva', 'Rocha', 'eva@example.com', 'hashed_pw5', 'UI/UX Designer', 'I design user-friendly and beautiful interfaces.', '916789012');


    INSERT INTO Tag (Name) VALUES ('Web Development');

    INSERT INTO Service (SellerUserId, CategoryId, Title, Description, BasePrice, Currency, DeliveryDays, Revisions) VALUES
    (1, 2, 'Landing Page Design', 'Clean and modern landing page built with HTML/CSS/JS.', 60.0, 'EUR', 3, 1),
    (1, 2, 'Full Website with CMS', 'A complete dynamic website with admin panel (Laravel).', 250.0, 'EUR', 10, 2),
    (1, 2, 'Bug Fixing and Code Review', 'I will fix bugs and review your code for quality.', 40.0, 'EUR', 2, 0),
    (1, 2, 'E-commerce Website', 'Complete online store with cart, checkout, and admin.', 300.0, 'EUR', 12, 3),
    (1, 2, 'Portfolio Website', 'Custom portfolio to showcase your work professionally.', 100.0, 'EUR', 5, 1),
    (1, 3, 'Single Page Application', 'React-based SPA with smooth navigation and backend.', 180.0, 'EUR', 7, 2),
    (1, 3, 'Database Design', 'Optimized relational database schema + ER diagram.', 70.0, 'EUR', 3, 1),
    (1, 3, 'API Development', 'RESTful APIs using Node.js or PHP, with documentation.', 120.0, 'EUR', 4, 2),
    (1, 3, 'Web App Debugging', 'Advanced debugging for JS/PHP applications.', 55.0, 'EUR', 2, 0),
    (1, 3, 'Speed Optimization', 'Improve loading speed of your website (Lighthouse).', 90.0, 'EUR', 3, 1);

    INSERT INTO Message (SenderUserId, ReceiverUserId, Content) VALUES
    (1, 2, 'Hi Bruno! Just saw your frontend portfolio — very nice.'),
    (2, 1, 'Thanks Ana! Let me know if you need a UI review.'),
    (1, 3, 'Hey Carla, do you have time to pair on a JS bug?'),
    (3, 1, 'Sure Ana, send me the code and I’ll take a look.'),
    (4, 1, 'Ana, I refactored the PHP module you mentioned. Thoughts?'),
    (1, 4, 'Thanks Daniel! I’ll review it tonight.'),
    (5, 1, 'Hi Ana, I’d love to collaborate on a UI/UX project. Interested?'),
    (1, 5, 'Hey Eva! Absolutely, your design work is excellent. Let’s plan.');



    INSERT INTO ServiceTag (ServiceId, TagId) SELECT ServiceId, 1 FROM Service WHERE SellerUserId = 1;

    INSERT INTO Admin (UserId) VALUES (1);


    COMMIT;
    PRAGMA foreign_keys = ON;