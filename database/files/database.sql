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
        Status TEXT NOT NULL DEFAULT 'InProgress' CHECK (Status IN ('InProgress', 'Revision', 'Completed','Cancelled')), -- NVARCHAR(20)
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

    CREATE TRIGGER trg_JobOrder_SetActualDeliveryDate
    AFTER UPDATE OF Status ON JobOrder
    FOR EACH ROW
    WHEN
        NEW.Status = 'Completed'
        AND OLD.Status != 'Completed'
        AND NEW.ActualDeliveryDate IS NULL
    BEGIN
        UPDATE JobOrder
        SET ActualDeliveryDate = DATETIME('now')
        WHERE JobOrderId = NEW.JobOrderId;
    END;

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

    DROP TABLE IF EXISTS Offer;

    CREATE TABLE Offer (
      OfferId        INTEGER PRIMARY KEY AUTOINCREMENT,
      SellerUserId   INTEGER NOT NULL,
      BuyerUserId    INTEGER NOT NULL,
      ServiceId      INTEGER NOT NULL,
      Requirements   TEXT,
      Price          REAL    NOT NULL,
      Currency       TEXT    NOT NULL DEFAULT 'EUR',
      Status         TEXT    NOT NULL DEFAULT 'pending',  -- 'pending','accepted','declined'
      CreatedAt      DATETIME DEFAULT CURRENT_TIMESTAMP,
      UpdatedAt      DATETIME DEFAULT CURRENT_TIMESTAMP,

      FOREIGN KEY (SellerUserId) REFERENCES User(UserId)    ON DELETE CASCADE,
      FOREIGN KEY (BuyerUserId)  REFERENCES User(UserId)    ON DELETE CASCADE,
      FOREIGN KEY (ServiceId)    REFERENCES Service(ServiceId) ON DELETE CASCADE
    );

    CREATE INDEX idx_offer_seller ON Offer(SellerUserId);
    CREATE INDEX idx_offer_buyer  ON Offer(BuyerUserId);
    CREATE INDEX idx_offer_status ON Offer(Status);

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

--not made by me, used ai to make seed data--
-- Categories
INSERT OR IGNORE INTO Category (CategoryId, Name, Description) VALUES (1, 'Other', 'Fallback for uncategorized services');
INSERT INTO Category (Name, Description) VALUES
  ('Development & IT','All your coding needs'),
  ('Design & Creative','Logos, graphics, UI/UX'),
  ('Writing & Translation','Copywriting, translation'),
  ('Sales & Marketing','Ad campaigns, SEO'),
  ('Finance & Accounting','Bookkeeping, CFO services'),
  ('Video & Animation','Explainers, commercials, motion graphics'),
  ('Music & Audio','Voice‐overs, jingles, editing'),
  ('Business Consulting','Strategy, operations, market research'),
  ('Legal Services','Contracts, IP, compliance'),
  ('Lifestyle','Cooking, fitness, crafts');

-- Users
INSERT INTO User (UserName, FirstName, LastName, Email, PasswordHash, Headline, Description, Phone) VALUES
  ('alice_smith','Alice','Smith','1@example.com','$2y$10$aAcWsrPCydUOca8RkrwBJevKrY0/GMtsIaRU6Pq1w2cYtYrSX93K6','Creative Graphic Designer','Over 5 years in branding and logo design.','912345001'),
  ('bob_dev','Bob','Johnson','2@example.com','$2y$10$aAcWsrPCydUOca8RkrwBJevKrY0/GMtsIaRU6Pq1w2cYtYrSX93K6','Full-Stack Developer','Building scalable applications with Node.js and React.','912345002'),
  ('carol_writer','Carol','Williams','carol.williams@example.com','hashed_pw3','Content Strategist','Expert in SEO and engaging copywriting.','912345003'),
  ('david_marketer','David','Brown','david.brown@example.com','hashed_pw4','Digital Marketer','Specialist in PPC campaigns and social media.','912345004'),
  ('emma_account','Emma','Davis','emma.davis@example.com','hashed_pw5','Certified Accountant','10+ years in bookkeeping and tax preparation.','912345005'),
  ('frank_video','Frank','Miller','frank.miller@example.com','hashed_pw6','Video Producer','Animations, editing, and motion graphics.','912345006'),
  ('grace_voice','Grace','Wilson','grace.wilson@example.com','hashed_pw7','Voice Over Artist','Professional voiceovers in multiple accents.','912345007'),
  ('hank_consult','Hank','Moore','hank.moore@example.com','hashed_pw8','Business Consultant','Process optimization and market research.','912345008'),
  ('ivy_legal','Ivy','Taylor','ivy.taylor@example.com','hashed_pw9','Legal Advisor','Contracts, IP, and compliance services.','912345009'),
  ('jack_coach','Jack','Anderson','jack.anderson@example.com','hashed_pw10','Fitness Coach','Personal training and nutrition guidance.','912345010'),
  -- Development & IT
  ('dev_alex_ramos','Alex','Ramos','alex.ramos@dev.example.com','hashed_pw31','Full-Stack Developer','Desenvolvimento de aplicações web completas com React, Node.js e banco de dados SQL. Experiência em microserviços.','912345031'),
  ('dev_bruna_santos','Bruna','Santos','bruna.santos@dev.example.com','hashed_pw32','Software Engineer','Especialista em sistemas distribuídos e escalabilidade. Estudo de arquiteturas serverless e CI/CD.','912345032'),
  ('dev_carlos_moura','Carlos','Moura','carlos.moura@dev.example.com','hashed_pw33','Backend Developer','Focado em design de APIs RESTful e otimização de queries em bancos NoSQL e SQL.','912345033'),
  -- Design & Creative
  ('design_luiza_coelho','Luiza','Coelho','luiza.coelho@design.example.com','hashed_pw34','UI/UX Designer','Design de interfaces responsivas e prototipagem de alta fidelidade com Figma e Adobe XD.','912345034'),
  ('design_marcos_almeida','Marcos','Almeida','marcos.almeida@design.example.com','hashed_pw35','Graphic Designer','Criação de identidades visuais, logotipos e material para impressão. Experiência em branding.','912345035'),
  ('design_naomi_silva','Naomi','Silva','naomi.silva@design.example.com','hashed_pw36','Illustrator','Ilustração vetorial e arte digital para campanhas, livros infantis e produtos.','912345036'),
  -- Writing & Translation
  ('write_fernanda_paulo','Fernanda','Paulo','fernanda.paulo@write.example.com','hashed_pw37','Copywriter','Redação publicitária com foco em storytelling e conversão para sites e anúncios.','912345037'),
  ('write_gabriel_oliveira','Gabriel','Oliveira','gabriel.oliveira@write.example.com','hashed_pw38','Technical Writer','Documentação técnica clara e detalhada, guias de usuário e whitepapers.','912345038'),
  ('write_helena_rodrigues','Helena','Rodrigues','helena.rodrigues@write.example.com','hashed_pw39','Translator','Tradução do inglês para o português e vice-versa, revisões e localização de conteúdo.','912345039'),
  -- Sales & Marketing
  ('market_igor_rocha','Igor','Rocha','igor.rocha@market.example.com','hashed_pw40','Marketing Strategist','Planejamento de campanhas de marketing digital, SEO e inbound marketing.','912345040'),
  ('market_julia_costa','Julia','Costa','julia.costa@market.example.com','hashed_pw41','Social Media Manager','Gestão de perfis em redes sociais, criação de calendário editorial e anúncios.','912345041'),
  ('market_kevin_araujo','Kevin','Araujo','kevin.araujo@market.example.com','hashed_pw42','PPC Specialist','Otimização de campanhas Google Ads e Facebook Ads com foco em ROI e CPA.','912345042'),
  -- Finance & Accounting
  ('finance_leonardo_fernandes','Leonardo','Fernandes','leonardo.fernandes@finance.example.com','hashed_pw43','Accountant','Serviços de contabilidade, fechamento mensal, balanços e assessoria fiscal.','912345043'),
  ('finance_manuela_barbosa','Manuela','Barbosa','manuela.barbosa@finance.example.com','hashed_pw44','Financial Analyst','Análise financeira, projeções de fluxo de caixa e relatórios gerenciais.','912345044'),
  ('finance_nicolas_pereira','Nicolas','Pereira','nicolas.pereira@finance.example.com','hashed_pw45','Tax Consultant','Consultoria tributária, planejamento fiscal e declarações de imposto de renda.','912345045'),
  -- Video & Animation
  ('video_oliveira_campos','Oliveira','Campos','oliveira.campos@video.example.com','hashed_pw46','Video Editor','Edição de vídeos promocionais, cortes dinâmicos e correção de cor no Premiere Pro.','912345046'),
  ('video_pedro_tavares','Pedro','Tavares','pedro.tavares@video.example.com','hashed_pw47','Animator','Animações 2D e motion graphics para apresentações e vídeos institucionais.','912345047'),
  ('video_ricardo_vas','Ricardo','Vas','ricardo.vas@video.example.com','hashed_pw48','Cinematographer','Captação de imagem, direção de fotografia e edição avançada para curtas e comerciais.','912345048'),
  -- Music & Audio
  ('audio_sophia_mello','Sophia','Mello','sophia.mello@audio.example.com','hashed_pw49','Audio Engineer','Mixagem, masterização e restauração de áudio para podcasts e músicas.','912345049'),
  ('audio_tiago_lima','Tiago','Lima','tiago.lima@audio.example.com','hashed_pw50','Voice Actor','Locução profissional em diferentes estilos para comerciais, e-learning e narrações.','912345050'),
  ('audio_ursula_nunes','Ursula','Nunes','ursula.nunes@audio.example.com','hashed_pw51','Composer','Composição de trilhas sonoras e jingles personalizados para marcas.','912345051'),
  -- Business Consulting
  ('consult_victor_mendes','Victor','Mendes','victor.mendes@consult.example.com','hashed_pw52','Business Consultant','Análise de processos, planejamento estratégico e melhoria de operações.','912345052'),
  ('consult_wagner_gomes','Wagner','Gomes','wagner.gomes@consult.example.com','hashed_pw53','Management Advisor','Consultoria em gestão empresarial, liderança e desenvolvimento organizacional.','912345053'),
  ('consult_xavier_faria','Xavier','Faria','xavier.faria@consult.example.com','hashed_pw54','Market Researcher','Pesquisa de mercado, análise de concorrência e identificação de oportunidades de nicho.','912345054'),
  -- Legal Services
  ('legal_yasmin_rocha','Yasmin','Rocha','yasmin.rocha@legal.example.com','hashedPw55','Legal Advisor','Assessoria jurídica em contratos, disputas comerciais e compliance regulatório.','912345055'),
  ('legal_zara_castro','Zara','Castro','zara.castro@legal.example.com','hashed_pw56','Corporate Lawyer','Consultoria em constituição de empresas, fusões e aquisições.','912345056'),
  ('legal_andre_pinto','Andre','Pinto','andre.pinto@legal.example.com','hashed_pw57','IP Specialist','Proteção de propriedade intelectual, registros de marca e patentes.','912345057'),
  -- Lifestyle
  ('life_ana_leitao','Ana','Leitao','ana.leitao@life.example.com','hashed_pw58','Fitness Coach','Treinos personalizados, planejamento alimentar e acompanhamento remoto.','912345058'),
  ('life_bruno_silva','Bruno','Silva','bruno.silva@life.example.com','hashed_pw59','Chef','Aulas de culinária, elaboração de cardápios e consultoria gastronômica.','912345059'),
  ('life_carla_neto','Carla','Neto','carla.neto@life.example.com','hashed_pw60','Life Coach','Mentoria de desenvolvimento pessoal, estabelecimento de metas e bem-estar.','912345060');

-- Tags
INSERT INTO Tag (Name) VALUES
  ('graphic design'),
  ('web development'),
  ('seo'),
  ('content writing'),
  ('digital marketing'),
  ('mobile app'),
  ('e-commerce'),
  ('data analysis'),
  ('video editing'),
  ('voice over'),
  ('bookkeeping'),
  ('tax'),
  ('business strategy'),
  ('legal'),
  ('fitness'),
  ('nutrition');

-- Services
INSERT INTO Service (SellerUserId, CategoryId, Title, Description, BasePrice, Currency, DeliveryDays, Revisions) VALUES
  (1,3,'Modern Logo Design','Unique logo concepts with unlimited revisions',80.0,'EUR',2,3),
  (1,3,'Social Media Graphics','Custom graphics for Facebook, Instagram, and LinkedIn',50.0,'EUR',1,2),
  (2,2,'Full-Stack Web Application','Complete web app with frontend and backend development',1000.0,'EUR',14,5),
  (2,2,'REST API Development','Design and implement RESTful APIs with documentation',300.0,'EUR',7,2),
  (3,4,'Blog Post Writing','SEO-friendly blog posts up to 1000 words',25.0,'EUR',3,1),
  (3,4,'Website Copywriting','Engaging website copy for your landing pages',100.0,'EUR',5,2),
  (4,5,'Google Ads Campaign Setup','Targeted Google Ads setup and optimization',200.0,'EUR',5,1),
  (4,5,'Social Media Management','Monthly management of Facebook and Instagram accounts',500.0,'EUR',30,4),
  (5,6,'Monthly Bookkeeping','Reconciliation and monthly financial reports',150.0,'EUR',30,0),
  (5,6,'Tax Consultation','One-hour tax consultation session',200.0,'EUR',7,1),
  (6,7,'Promotional Video Editing','Editing raw footage into a polished promo video',250.0,'EUR',10,2),
  (6,7,'2D Animation Explainer','Animated explainer video up to 60 seconds',400.0,'EUR',15,3),
  (7,8,'American English Voice Over','Clear and professional voiceover in American accent',100.0,'EUR',2,1),
  (7,8,'British Accent Narration','Professional British English narration',120.0,'EUR',2,1),
  (8,9,'Business Plan Development','Comprehensive business plan for startups',500.0,'EUR',10,2),
  (8,9,'Market Research Report','Detailed market analysis and opportunities report',700.0,'EUR',14,1),
  (9,10,'Contract Drafting','Drafting and review of legal contracts',300.0,'EUR',7,1),
  (9,10,'Legal Consultation Session','One-hour legal advice session',150.0,'EUR',1,0),
  (10,11,'Personal Training Plan','Customized 4-week fitness training plan',100.0,'EUR',7,0),
  (10,11,'Nutritional Meal Plan','Personalized 4-week meal plan',81.0,'EUR',5,0);


-- Messages
INSERT INTO Message (SenderUserId, ReceiverUserId, Content) VALUES
  (1,2,'Hi Bob, could you review my portfolio website?'),
  (2,1,'Sure, send me the link when you have it.'),
  (1,2,'Here it is: https://aliceportfolio.com'),
  (3,4,'Hey David, interested in a content marketing campaign?'),
  (4,3,'Absolutely, let''s discuss your goals over a call.'),
  (6,7,'Grace, can you record a promotional voiceover for our new product?'),
  (7,6,'Yes, I''d love to! Let me know the script details.'),
  (5,8,'Hank, I need last quarter''s financial analysis by next week.'),
  (8,5,'I''ll send the market analysis report by Friday.');

-- Job Orders
INSERT INTO JobOrder (ServiceId, BuyerUserId, SellerUserId, AgreedPrice, Currency, Status, Requirements, StartDate, ExpectedDeliveryDate, ActualDeliveryDate, CompletionDate) VALUES
  (1,3,1,80.0,'EUR','Completed','Logo for tech startup','2025-04-01','2025-04-03','2025-04-02 10:15:00','2025-04-02 10:15:00'),
  (5,4,3,25.0,'EUR','Completed','Two blog posts about web design','2025-03-20','2025-03-23','2025-03-22 14:30:00','2025-03-22 14:30:00'),
  (7,3,4,200.0,'EUR','InProgress','Setup Google Ads for product launch','2025-05-10','2025-05-15',NULL,NULL),
  (2,4,1,50.0,'EUR','Revision','Graphics for upcoming Instagram campaign','2025-05-05','2025-05-06',NULL,NULL),
  (12,10,6,400.0,'EUR','Cancelled','Explainer animation for new service',NULL,NULL,NULL,NULL),
  (3,10,2,1000.0,'EUR','InProgress','Full-stack application for client portal','2025-05-01','2025-05-15',NULL,NULL),
  (4,1,2,300.0,'EUR','Completed','API integration for mobile app','2025-04-10','2025-04-17','2025-04-16 09:00:00','2025-04-16 09:00:00'),
  (6,5,3,100.0,'EUR','InProgress','High-conversion landing page copy','2025-05-12','2025-05-17',NULL,NULL),
  (13,2,7,100.0,'EUR','Revision','Voiceover for social media ads','2025-05-08','2025-05-10',NULL,NULL),
  (14,5,7,120.0,'EUR','Completed','Narration for corporate video','2025-03-28','2025-03-30','2025-03-29 16:45:00','2025-03-29 16:45:00'),
  (15,6,8,500.0,'EUR','InProgress','Business plan for fundraising round','2025-05-03','2025-05-13',NULL,NULL),
  (17,2,9,300.0,'EUR','Completed','Draft contract for partnership agreement','2025-02-14','2025-02-21','2025-02-20 11:20:00','2025-02-20 11:20:00');

-- Service Tags
INSERT INTO ServiceTag (ServiceId, TagId) VALUES
  (1,1),
  (2,1),(2,5),
  (3,2),(3,6),
  (4,2),(4,8),
  (5,4),(5,3),
  (6,4),(6,3),
  (7,5),(7,3),
  (8,5),(8,3),
  (9,8),
  (10,12),
  (11,9),(11,5),
  (12,9),
  (13,10),
  (14,10),
  (15,13),
  (16,8),
  (17,14),
  (19,15),
  (20,16);

-- Admins
INSERT INTO Admin (UserId) VALUES
  (1),
  (4);



COMMIT;
PRAGMA foreign_keys = ON;




PRAGMA foreign_keys = OFF;
BEGIN TRANSACTION;


COMMIT;
PRAGMA foreign_keys = ON;
