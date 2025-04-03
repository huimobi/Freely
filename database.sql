DROP TABLE IF EXISTS Service;
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS FreeLancer;
DROP TABLE IF EXISTS Client;
DROP TABLE IF EXISTS Admin;~
DROP TABLE IF EXISTS Comment;

CREATE TABLE User(
    UserId TEXT NOT NULL,
    UserName TEXT NOT NULL UNIQUE,
    FirstName NVARCHAR(40)  NOT NULL,
    LastName NVARCHAR(20)  NOT NULL,
    Phone NVARCHAR(24),
    Email NVARCHAR(60) NOT NULL,
    Password NVARCHAR(40) NOT NULL,
    CONSTRAINT PK_User PRIMARY KEY  (UserId)
);

Create Table FreeLancer(
    UserId INTEGER NOT NULL,
    Description NVARCHAR(200) NOT NULL,
    FOREIGN KEY (UserId) REFERENCES User (UserId)
	    ON DELETE NO ACTION ON UPDATE NO ACTION
    );

Create Table Client(
    UserId TEXT NOT NULL,
    FOREIGN KEY (UserId) REFERENCES User (UserId)
	    ON DELETE NO ACTION ON UPDATE NO ACTION
);

Create Table Admin(
     UserId TEXT NOT NULL,
    FOREIGN KEY (UserId) REFERENCES User (UserId)
	    ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE Service(
    ServiceId INTEGER NOT NULL,
    Description NVARCHAR(200) NOT NULL,
    Pricing INTEGER NOT NULL,
    DeliveryTime TIME,
    Category TEXT NOT NULL,
    CONSTRAINT PK_Service PRIMARY KEY  (ServiceId)
);

CREATE TABLE Comment(
    CommentId INTEGER NOT NULL,
    Rating INTEGER NOT NULL CHECK(Rating>= 1 AND Rating <=5),
    Description NVARCHAR(500),
    Date TIME NOT NULL DEFAULT CURRENT_TIME,

    FOREIGN KEY (UserId) REFERENCES User(UserId)
	    ON DELETE NO ACTION ON UPDATE NO ACTION,
    FOREIGN KEY (ServiceId) REFERENCES Service(ServiceId)
	    ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT PK_Comment PRIMARY KEY  (CommentId),
);

