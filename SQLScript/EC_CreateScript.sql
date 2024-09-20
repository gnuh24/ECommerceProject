DROP DATABASE IF EXISTS `SGU_E_Commerce`;
CREATE DATABASE IF NOT EXISTS `SGU_E_Commerce`;

USE `SGU_E_Commerce`;
/*________________________________________________________________________ TODO: Account tables_______________________________________________________________________ */

DROP TABLE IF EXISTS `UserInformation`;
CREATE TABLE IF NOT EXISTS `UserInformation`(
    `Id`            INT UNSIGNED       PRIMARY KEY    AUTO_INCREMENT,
    `Email`         NVARCHAR(255)                   UNIQUE,
    `Address`       NVARCHAR(255),
    `Birthday`      DATE,
    `Fullname`      NVARCHAR(255),
    `Gender`        ENUM("Male", "Female", "Other"),
    `PhoneNumber`   NVARCHAR(20) 
);


DROP TABLE IF EXISTS `Account`;
CREATE TABLE IF NOT EXISTS `Account`(
    `Id`                INT UNSIGNED                                PRIMARY KEY         AUTO_INCREMENT,
    `Password`          NVARCHAR(800)                               NOT NULL,
    `CreateTime`        DATETIME                                    NOT NULL            DEFAULT NOW(),
    `Status`            BOOLEAN                                     NOT NULL            DEFAULT TRUE,
    `Active`			BOOLEAN 		                            NOT NULL            DEFAULT FALSE,
    `Role`              ENUM("User", "Admin")                       NOT NULL            DEFAULT "User",
    `Type`              ENUM("Standard", "Google")                  NOT NULL            DEFAULT "Standard",
    `UserInformationId` INT UNSIGNED                                NOT NULL,
    FOREIGN KEY (`UserInformationId`) REFERENCES `UserInformation`(`Id`)
);


DROP TABLE IF EXISTS `Token`;
CREATE TABLE IF NOT EXISTS `Token`(
    `Id`                INT UNSIGNED                            PRIMARY KEY      AUTO_INCREMENT,
    `Token`             CHAR(36)                                NOT NULL         UNIQUE,
    `CreateTime`	    DATETIME		                        NOT NULL         DEFAULT NOW(),
    `Expiration`    	DATETIME                                NOT NULL        ,
    `Type`              ENUM("Registration", "ResetPassword")   NOT NULL,         
    `AccountId`         INT UNSIGNED                            NOT NULL,
    FOREIGN KEY (`AccountId`) REFERENCES `Account`(`Id`)
);
