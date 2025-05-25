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
  ('life_carla_neto','Carla','Neto','carla.neto@life.example.com','hashed_pw60','Life Coach','Mentoria de desenvolvimento pessoal, estabelecimento de metas e bem-estar.','912345060'),
  ('other_anna_martins','Anna','Martins','anna.martins@example.com','hashed_pw61','Artisan','Handcrafted leather goods maker','912345061'),
  ('other_joao_pereira','João','Pereira','joao.pereira@example.com','hashed_pw62','Virtual Assistant','Professional virtual assistant for administrative tasks','912345062'),
  ('other_fernanda_lopes','Fernanda','Lopes','fernanda.lopes@example.com','hashed_pw63','Custom Jewelry Designer','Bespoke handmade jewelry creations','912345063'),
  ('other_rodrigo_silva','Rodrigo','Silva','rodrigo.silva@example.com','hashed_pw64','Language Tutor','Personalized language tutoring sessions','912345064'),
  ('other_maria_souza','Maria','Souza','maria.souza@example.com','hashed_pw65','Event Planner','Creative event planning and coordination','912345065');

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
  (10,11,'Nutritional Meal Plan','Personalized 4-week meal plan',81.0,'EUR',5,0),
  -- additional services for existing freelancers
  (1,3,'Brand Style Guide','Detailed brand guidelines for consistent identity',120.0,'EUR',7,1),
  (2,2,'Serverless Architecture Setup','Configure and deploy serverless infrastructure on AWS',500.0,'EUR',5,2),
  (3,4,'Technical Editing','Proofreading and technical editing of documents up to 2000 words',40.0,'EUR',3,1),
  (4,5,'LinkedIn Profile Optimization','Optimize LinkedIn profile for professional visibility and networking',60.0,'EUR',2,1),
  (5,6,'Payroll Processing','Comprehensive payroll setup and monthly processing',200.0,'EUR',10,0),
  -- services for new 'Other' category freelancers
  (41,1,'Handcrafted Leather Wallets','High-quality leather wallets handmade to order',75.0,'EUR',5,2),
  (42,1,'Virtual Admin Support','Efficient virtual administrative assistance for your business',150.0,'EUR',7,3),
  (43,1,'Bespoke Jewelry Design','Custom jewelry pieces designed and crafted to your specifications',200.0,'EUR',14,2),
  (44,1,'Online Language Tutoring','One-hour personalized language tutoring session',30.0,'EUR',1,1),
  (45,1,'Event Planning Coordination','Professional event planning and coordination services',300.0,'EUR',15,2),
  (2,6,'Data Visualization Dashboard','Interactive dashboard using Python and D3.js',300.0,'EUR',7,2),
  (3,4,'Resume & Cover Letter','Custom resume and cover letter tailored to job applications',40.0,'EUR',2,1),
  (4,5,'Email Marketing Campaign','Design and manage targeted email marketing campaigns',150.0,'EUR',7,3),
  (5,6,'Tax Filing Consultation','Assistance with personal tax filing and compliance',100.0,'EUR',3,1),
  (6,7,'Short Video Clip','Create short promotional video clip up to 30 seconds',100.0,'EUR',5,1),
  (7,8,'Podcast Editing','Edit and enhance up to 10-minute podcast episode',80.0,'EUR',3,1),
  (8,9,'Competitive Analysis','In-depth competitor analysis and market positioning report',500.0,'EUR',10,2),
  (9,10,'Trademark Registration','Guidance on trademark application and filing',400.0,'EUR',14,1),
  (10,11,'Yoga Session Plan','Detailed 4-week yoga session plan and guidance',90.0,'EUR',2,0),
  (41,1,'Leather Repair Service','Repair and refurbishment of leather goods',50.0,'EUR',3,0),
  (42,1,'Appointment Scheduling','Manage and schedule professional appointments',100.0,'EUR',5,2),
  (43,1,'Jewelry Repair','Cleaning and repair services for jewelry items',60.0,'EUR',7,1),
  (44,1,'Language Exam Prep','Preparation sessions for language proficiency exams',50.0,'EUR',3,1),
  (45,1,'Event Budget Planning','Detailed budget planning for events and coordination',120.0,'EUR',5,1),
  (1,2,'Mobile App UI Review','Expert review of mobile app UI/UX design',70.0,'EUR',2,1);

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


-- ──────────────────────────────────────────────────────────────────────────────
-- 1) Add 50 new completed job orders (IDs 13–62)
-- ──────────────────────────────────────────────────────────────────────────────
INSERT INTO JobOrder (ServiceId, BuyerUserId, SellerUserId, AgreedPrice, Currency, Status, Requirements, StartDate, ExpectedDeliveryDate, ActualDeliveryDate, CompletionDate) VALUES
  (1,  4, 1,   80.0, 'EUR', 'Completed', 'Logo refresh for new branding',             '2025-03-01','2025-03-03','2025-03-03 10:00:00','2025-03-03 10:00:00'),
  (2,  5, 1,   50.0, 'EUR', 'Completed', 'Social media graphics for campaign',        '2025-03-02','2025-03-03','2025-03-03 11:15:00','2025-03-03 11:15:00'),
  (3,  6, 2, 1000.0, 'EUR', 'Completed', 'Full-stack portal for client onboarding',   '2025-03-04','2025-03-18','2025-03-18 09:45:00','2025-03-18 09:45:00'),
  (4,  7, 2,  300.0, 'EUR', 'Completed', 'REST API for mobile app integration',        '2025-03-05','2025-03-12','2025-03-12 14:20:00','2025-03-12 14:20:00'),
  (5,  2, 3,   25.0, 'EUR', 'Completed', 'SEO blog post on web design trends',         '2025-03-07','2025-03-10','2025-03-10 13:00:00','2025-03-10 13:00:00'),
  (6,  1, 3,  100.0, 'EUR', 'Completed', 'Landing page copy with clear CTAs',          '2025-03-08','2025-03-13','2025-03-13 15:30:00','2025-03-13 15:30:00'),
  (7,  3, 4,  200.0, 'EUR', 'Completed', 'Google Ads setup for product launch',         '2025-03-09','2025-03-14','2025-03-14 10:10:00','2025-03-14 10:10:00'),
  (8,  6, 4,  500.0, 'EUR', 'Completed', 'Monthly social media management',             '2025-03-11','2025-04-10','2025-04-10 16:00:00','2025-04-10 16:00:00'),
  (9,  8, 5,  150.0, 'EUR', 'Completed', 'Monthly bookkeeping and reconciliation',        '2025-03-12','2025-04-11','2025-04-11 09:25:00','2025-04-11 09:25:00'),
  (10, 7, 5,  200.0, 'EUR', 'Completed', 'In-depth tax consultation',                     '2025-03-13','2025-03-20','2025-03-20 12:45:00','2025-03-20 12:45:00'),
  (11, 2, 6,  250.0, 'EUR', 'Completed', 'Promo video editing for social ads',           '2025-03-15','2025-03-25','2025-03-25 13:30:00','2025-03-25 13:30:00'),
  (12, 3, 6,  400.0, 'EUR', 'Completed', '60s explainer animation',                       '2025-03-16','2025-03-31','2025-03-31 11:00:00','2025-03-31 11:00:00'),
  (13, 5, 7,  100.0, 'EUR', 'Completed', 'American voice-over for commercial',            '2025-03-18','2025-03-20','2025-03-20 10:50:00','2025-03-20 10:50:00'),
  (14, 4, 7,  120.0, 'EUR', 'Completed', 'British narration for corporate video',          '2025-03-19','2025-03-21','2025-03-21 14:15:00','2025-03-21 14:15:00'),
  (15, 1, 8,  500.0, 'EUR', 'Completed', 'Startup business plan',                           '2025-03-20','2025-03-30','2025-03-30 09:00:00','2025-03-30 09:00:00'),
  (16, 2, 8,  700.0, 'EUR', 'Completed', 'Detailed market research report',                '2025-03-21','2025-04-04','2025-04-04 10:20:00','2025-04-04 10:20:00'),
  (17, 3, 9,  300.0, 'EUR', 'Completed', 'Contract drafting for partnership',               '2025-03-22','2025-03-29','2025-03-29 11:10:00','2025-03-29 11:10:00'),
  (18, 6, 9,  150.0, 'EUR', 'Completed', 'Quick legal consultation session',               '2025-03-23','2025-03-24','2025-03-24 12:00:00','2025-03-24 12:00:00'),
  (19, 7, 10, 100.0, 'EUR', 'Completed', '4-week personal training plan',                  '2025-03-24','2025-03-31','2025-03-31 14:00:00','2025-03-31 14:00:00'),
  (20, 8, 10,  81.0, 'EUR', 'Completed', '4-week nutritional meal plan',                   '2025-03-25','2025-03-30','2025-03-30 13:45:00','2025-03-30 13:45:00'),
  (21,  5, 1, 120.0, 'EUR', 'Completed', 'Brand style guide document',                     '2025-04-01','2025-04-08','2025-04-08 10:30:00','2025-04-08 10:30:00'),
  (22,  6, 2, 500.0, 'EUR', 'Completed', 'Serverless AWS setup',                             '2025-04-02','2025-04-07','2025-04-07 09:15:00','2025-04-07 09:15:00'),
  (23,  7, 3,  40.0, 'EUR', 'Completed', 'Technical editing of whitepaper',                 '2025-04-03','2025-04-05','2025-04-05 11:50:00','2025-04-05 11:50:00'),
  (24,  8, 4,  60.0, 'EUR', 'Completed', 'LinkedIn profile optimization',                  '2025-04-04','2025-04-06','2025-04-06 10:40:00','2025-04-06 10:40:00'),
  (25,  9, 5, 200.0, 'EUR', 'Completed', 'Monthly payroll processing',                    '2025-04-05','2025-04-15','2025-04-15 09:05:00','2025-04-15 09:05:00'),
  (26, 10,41,  75.0, 'EUR', 'Completed', 'Handcrafted leather wallet order',                '2025-04-06','2025-04-11','2025-04-11 14:20:00','2025-04-11 14:20:00'),
  (27,  1,42, 150.0, 'EUR', 'Completed', 'Virtual admin support package',                   '2025-04-07','2025-04-14','2025-04-14 13:00:00','2025-04-14 13:00:00'),
  (28,  2,43, 200.0, 'EUR', 'Completed', 'Custom jewelry design',                            '2025-04-08','2025-04-22','2025-04-22 12:10:00','2025-04-22 12:10:00'),
  (29,  3,44,  30.0, 'EUR', 'Completed', '1-hour language tutoring session',                '2025-04-09','2025-04-09','2025-04-09 15:30:00','2025-04-09 15:30:00'),
  (30,  4,45, 300.0, 'EUR', 'Completed', 'Event planning coordination',                     '2025-04-10','2025-04-25','2025-04-25 10:45:00','2025-04-25 10:45:00'),
  (31,  5, 2, 300.0, 'EUR', 'Completed', 'Interactive data dashboard',                     '2025-04-11','2025-04-18','2025-04-18 11:20:00','2025-04-18 11:20:00'),
  (32,  6, 3,  40.0, 'EUR', 'Completed', 'Resume and cover letter package',                '2025-04-12','2025-04-14','2025-04-14 09:50:00','2025-04-14 09:50:00'),
  (33,  7, 4, 150.0, 'EUR', 'Completed', 'Email marketing campaign setup',                 '2025-04-13','2025-04-20','2025-04-20 14:00:00','2025-04-20 14:00:00'),
  (34,  8, 5, 100.0, 'EUR', 'Completed', 'Personal tax filing help',                       '2025-04-14','2025-04-17','2025-04-17 10:30:00','2025-04-17 10:30:00'),
  (35,  9, 6, 100.0, 'EUR', 'Completed', '30-sec promotional clip',                        '2025-04-15','2025-04-20','2025-04-20 09:10:00','2025-04-20 09:10:00'),
  (36, 10, 7,  80.0, 'EUR', 'Completed', 'Podcast episode editing',                         '2025-04-16','2025-04-19','2025-04-19 11:45:00','2025-04-19 11:45:00'),
  (37,  1, 8, 500.0, 'EUR', 'Completed', 'Competitor analysis report',                     '2025-04-17','2025-04-27','2025-04-27 15:00:00','2025-04-27 15:00:00'),
  (38,  2, 9, 400.0, 'EUR', 'Completed', 'Trademark filing guidance',                      '2025-04-18','2025-05-02','2025-05-02 10:05:00','2025-05-02 10:05:00'),
  (39,  3,10,  90.0, 'EUR', 'Completed', '4-week yoga session plan',                       '2025-04-19','2025-04-23','2025-04-23 14:30:00','2025-04-23 14:30:00'),
  (40,  4,41,  50.0, 'EUR', 'Completed', 'Leather repair and cleaning',                     '2025-04-20','2025-04-22','2025-04-22 09:20:00','2025-04-22 09:20:00'),
  (41,  5, 1,  70.0, 'EUR', 'Completed', 'Mobile app UI/UX review',                         '2025-04-21','2025-04-23','2025-04-23 11:15:00','2025-04-23 11:15:00'),
  (1,  6, 1,   85.0, 'EUR', 'Completed', 'Logo tweaks and color variations',               '2025-04-22','2025-04-24','2025-04-24 13:00:00','2025-04-24 13:00:00'),
  (2,  7, 1,   55.0, 'EUR', 'Completed', 'Extra banner graphics',                           '2025-04-23','2025-04-25','2025-04-25 14:40:00','2025-04-25 14:40:00'),
  (3,  8, 2, 1100.0,'EUR', 'Completed', 'Extended web portal with analytics',              '2025-04-24','2025-05-08','2025-05-08 10:25:00','2025-05-08 10:25:00'),
  (4,  9, 2,  320.0,'EUR', 'Completed', 'API docs and testing suite',                     '2025-04-25','2025-05-02','2025-05-02 12:10:00','2025-05-02 12:10:00'),
  (5, 10, 3,   30.0,'EUR', 'Completed', 'Extra blog post on UX design',                   '2025-04-26','2025-04-29','2025-04-29 15:20:00','2025-04-29 15:20:00'),
  (6,  1, 3,  110.0,'EUR', 'Completed', 'Long-form website copy',                          '2025-04-27','2025-05-02','2025-05-02 11:00:00','2025-05-02 11:00:00'),
  (7,  2, 4,  210.0,'EUR', 'Completed', 'Optimized Google Ads keywords',                   '2025-04-28','2025-05-01','2025-05-01 10:55:00','2025-05-01 10:55:00'),
  (8,  3, 4,  520.0,'EUR', 'Completed', 'Extra social media posts',                        '2025-04-29','2025-05-29','2025-05-29 09:35:00','2025-05-29 09:35:00'),
  (9,  4, 5,  160.0,'EUR', 'Completed', 'Detailed ledger reconciliation',                  '2025-04-30','2025-05-30','2025-05-30 14:05:00','2025-05-30 14:05:00');

-- ──────────────────────────────────────────────────────────────────────────────
-- 2) Add 50 comments, one per the new JobOrder rows (13–62)
-- ──────────────────────────────────────────────────────────────────────────────
INSERT INTO Comment (JobOrderId, BuyerUserId, ServiceId, Rating, Description) VALUES
  (13,  5, 1, 5, 'Fantastic logo update – really captured our brand spirit.'),
  (14,  4, 2, 4, 'Good graphics, helped our campaign stand out.'),
  (15,  6, 3, 5, 'The portal is rock-solid, great work!'),
  (16,  7, 4, 4, 'API works well, documentation was clear.'),
  (17,  2, 5, 5, 'Blog post drove traffic immediately.'),
  (18,  1, 6, 4, 'Copy is engaging, but needed one small tweak.'),
  (19,  3, 7, 5, 'Ads setup doubled our conversions!'),
  (20,  6, 8, 5, 'Hands-off approach; everything was delivered on time.'),
  (21,  8, 9, 4, 'Bookkeeping reports were accurate and clear.'),
  (22,  7,10, 5, 'Tax advice saved me hundreds—highly recommended.'),
  (23,  2,11, 5, 'Video editing was smooth and on-brand.'),
  (24,  3,12, 4, 'Animation was great but needed minor color fix.'),
  (25,  5,13, 5, 'Voice-over quality is top-notch!'),
  (26,  4,14, 4, 'British accent was spot-on.'),
  (27,  1,15, 5, 'Business plan was thorough and well-presented.'),
  (28,  2,16, 5, 'Market report gave us clear insights.'),
  (29,  3,17, 4, 'Contract was well-written; just a minor clause added.'),
  (30,  6,18, 5, 'Legal session answered all my questions.'),
  (31,  7,19, 5, 'My workout plan is spot-on—I feel stronger already.'),
  (32,  8,20, 4, 'Meal plan is tasty but a bit repetitive.'),
  (33,  5,21, 5, 'Brand guide keeps our visuals consistent—thank you!'),
  (34,  6,22, 5, 'AWS setup was seamless, zero downtime.'),
  (35,  7,23, 4, 'Editing was solid, a few grammar suggestions.'),
  (36,  8,24, 5, 'LinkedIn revamp got me 10 new connections.'),
  (37,  9,25, 5, 'Payroll was handled perfectly, no errors.'),
  (38, 10,26, 4, 'Wallet quality is excellent and stitching is neat.'),
  (39,  1,27, 5, 'Admin support made my life so much easier.'),
  (40,  2,28, 5, 'Custom jewelry is stunning—exactly as I imagined.'),
  (41,  3,29, 4, 'Tutoring was helpful, pacing was just right.'),
  (42,  4,30, 5, 'Event ran smoothly thanks to the coordination.'),
  (43,  5,31, 5, 'Dashboard is intuitive and insightful.'),
  (44,  6,32, 4, 'Resume looks great; a couple of wording tweaks.'),
  (45,  7,33, 5, 'Email campaign open rate skyrocketed.'),
  (46,  8,34, 4, 'Tax filing went smoothly, some follow-up needed.'),
  (47,  9,35, 5, 'Promo clip is eye-catching and concise.'),
  (48, 10,36, 5, 'Podcast sounds crisp and professional.'),
  (49,  1,37, 4, 'Good analysis, but wanted a deeper competitive set.'),
  (50,  2,38, 5, 'Trademark process was demystified—excellent guidance.'),
  (51,  3,39, 5, 'Yoga plan is balanced and energizing.'),
  (52,  4,40, 4, 'Leather repair looks great; a small scuff remains.'),
  (53,  5,41, 5, 'UI review notes were spot-on, improved our app UX.'),
  (54,  6, 1, 5, 'Second logo round nailed the brief perfectly.'),
  (55,  7, 2, 4, 'Banner graphics look great; resized once.'),
  (56,  8, 3, 5, 'Extended portal is robust with analytics.'),
  (57,  9, 4, 5, 'API docs are clear and complete.'),
  (58, 10, 5, 4, 'Extra blog post was good, but could use more examples.'),
  (59,  1, 6, 5, 'Long-form copy reads very smoothly.'),
  (60,  2, 7, 5, 'Keyword optimization boosted our ROI.'),
  (61,  3, 8, 5, 'Extra posts kept engagement high all month.'),
  (62,  4, 9, 4, 'Ledger reconciliation was accurate after one tweak.'),
  (63,  5,10, 5, 'Loved the attention to detail on the reports.'),
  (64,  6,11, 5, 'Video color grading is perfect.'),
  (65,  7,12, 4, 'Animation pacing was slightly slow at start.'),
  (66,  8,13, 5, 'Voice-over timing is flawless.'),
  (67,  9,14, 5, 'Narration voice suited our brand tone.'),
  (68, 10,15, 5, 'Business plan impressed potential investors.'),
  (69,  1,16, 4, 'Market report could include a SWOT table.'),
  (70,  2,17, 5, 'Contract draft was legally sound.'),
  (71,  3,18, 5, 'Legal advice was concise and actionable.'),
  (72,  4,19, 5, 'Training plan is easy to follow.'),
  (73,  5,20, 4, 'Meal plan is varied but a bit heavy on carbs.');



COMMIT;
PRAGMA foreign_keys = ON;

PRAGMA foreign_keys = OFF;
BEGIN TRANSACTION;

COMMIT;
PRAGMA foreign_keys = ON;
