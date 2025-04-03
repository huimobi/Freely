DROP TABLE IF EXISTS Service;
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS FreeLancer;
DROP TABLE IF EXISTS Client;
DROP TABLE IF EXISTS Admin;

CREATE TABLE User(
    UserName TEXT NOT NULL,
    FirstName NVARCHAR(40)  NOT NULL,
    LastName NVARCHAR(20)  NOT NULL,
    Phone NVARCHAR(24),
    Email NVARCHAR(60) NOT NULL,
    Password NVARCHAR(40) NOT NULL,
    CONSTRAINT PK_User PRIMARY KEY  (UserName)
);

Create Table FreeLancer(
    UserName TEXT NOT NULL,
    Description NVARCHAR(200) NOT NULL,
    FOREIGN KEY (UserName) REFERENCES User (UserName)
	    ON DELETE NO ACTION ON UPDATE NO ACTION
    );

Create Table Client(
     UserName TEXT NOT NULL,
    FOREIGN KEY (UserName) REFERENCES User (UserName)
	    ON DELETE NO ACTION ON UPDATE NO ACTION
);

Create Table Admin(
     UserName TEXT NOT NULL,
    FOREIGN KEY (UserName) REFERENCES User (UserName)
	    ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE Service(
    ServiceId INTEGER NOT NULL,
    CONSTRAINT PK_Service PRIMARY KEY  (ServiceId)
);

