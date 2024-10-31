DROP DATABASE IF EXISTS `SGU_E_Commerce`;
CREATE DATABASE IF NOT EXISTS `SGU_E_Commerce`;

USE `SGU_E_Commerce`;
/*________________________________________________________________________ TODO: Account tables_______________________________________________________________________ */

-- DROP TABLE IF EXISTS `UserInformation`;
CREATE TABLE IF NOT EXISTS `UserInformation`(
    `Id`            INT UNSIGNED       PRIMARY KEY    AUTO_INCREMENT,
    `Email`         NVARCHAR(255)                      UNIQUE,
    `Address`       NVARCHAR(255),
    `Birthday`      DATE,
    `Fullname`      NVARCHAR(255),
    `Gender`        ENUM("Male", "Female", "Other"),
    `PhoneNumber`   NVARCHAR(20) 
);


-- DROP TABLE IF EXISTS `Account`;
CREATE TABLE IF NOT EXISTS `Account`(
    `Id`                INT UNSIGNED                                	PRIMARY KEY         AUTO_INCREMENT,
    `Password`          NVARCHAR(800)                               	NOT NULL,
    `CreateTime`        DATETIME                                    	NOT NULL            DEFAULT NOW(),
    `Status`            BOOLEAN                                     	NOT NULL            DEFAULT TRUE,
    `Role`              ENUM("User", "Employee", "Manager", "Admin")    NOT NULL            DEFAULT "User",
    `Type`              ENUM("Standard", "Google")                  	NOT NULL            DEFAULT "Standard",
    `UserInformationId` INT UNSIGNED                                	NOT NULL,
    FOREIGN KEY (`UserInformationId`) REFERENCES `UserInformation`(`Id`)
);


-- DROP TABLE IF EXISTS `Token`;
CREATE TABLE IF NOT EXISTS `Token`(
    `Id`                INT UNSIGNED                            PRIMARY KEY      AUTO_INCREMENT,
    `Token`             CHAR(36)                                NOT NULL         UNIQUE,
    `CreateTime`	    DATETIME		                        NOT NULL         DEFAULT NOW(),
    `Expiration`    	DATETIME                                NOT NULL        ,
    `Type`              ENUM("Registration", "ResetPassword")   NOT NULL,         
    `AccountId`         INT UNSIGNED                            NOT NULL,
    FOREIGN KEY (`AccountId`) REFERENCES `Account`(`Id`)
);

/*________________________________________________________________________ TODO: Product tables_______________________________________________________________________ */
-- DROP TABLE IF EXISTS `Category`;
CREATE TABLE IF NOT EXISTS `Category`(
    `Id`    INT UNSIGNED        PRIMARY KEY    AUTO_INCREMENT,
    `CategoryName`  NVARCHAR(255)       NOT NULL
);

-- DROP TABLE IF EXISTS `Brand`;
CREATE TABLE IF NOT EXISTS `Brand`(
    `Id`       INT UNSIGNED        PRIMARY KEY    AUTO_INCREMENT,
    `BrandName`     NVARCHAR(255)       NOT NULL
);

-- DROP TABLE IF EXISTS `Product`;
CREATE TABLE IF NOT EXISTS `Product`(
    `Id`         INT UNSIGNED        PRIMARY KEY    AUTO_INCREMENT,
    `ProductName`       NVARCHAR(1000)      NOT NULL,
    `Status`            BOOLEAN            	NOT NULL,
    `CreateTime`        DATETIME           	NOT NULL,
    `Image`             VARCHAR(255)        ,

    `Quantity`          INT UNSIGNED        NOT NULL,
    `UnitPrice`				INT UNSIGNED        NOT NULL,
	`Sale`				INT UNSIGNED        NOT NULL	DEFAULT 0,	 

    `Origin`            NVARCHAR(255)       ,
    `Capacity`          INT UNSIGNED        ,
    `ABV`               INT UNSIGNED        ,
    `Description`       TEXT				,

    `BrandId`           INT UNSIGNED   		NOT NULL,
	`CategoryId`	    INT UNSIGNED   		NOT NULL,
    FOREIGN KEY (`BrandId`)     	REFERENCES `Brand`(`Id`),
    FOREIGN KEY (`CategoryId`)  	REFERENCES `Category`(`Id`)
);

/*________________________________________________________________________ TODO: Order tables_______________________________________________________________________ */

-- DROP TABLE IF EXISTS `Voucher`;
CREATE TABLE IF NOT EXISTS `Voucher` (
	`Id` 				INT AUTO_INCREMENT PRIMARY KEY,
    `ExpirationTime` 	DATETIME NOT NULL,
    `Code` 				VARCHAR(50) UNIQUE NOT NULL,
    `Condition` 		INT NOT NULL,
    `SaleAmount` 		INT NOT NULL,
    `IsPublic` 			BOOLEAN DEFAULT TRUE,
    `CreateTime` 		DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- DROP TABLE IF EXISTS `Order`;
CREATE TABLE IF NOT EXISTS `Order` (
    `Id`                CHAR(12)           NOT NULL    PRIMARY KEY,
    `OrderTime`         DATETIME           NOT NULL		DEFAULT NOW(),
    `TotalPrice`        INT UNSIGNED       NOT NULL,
    `Note`              TEXT,		
    `AccountId`         INT UNSIGNED, 
    `Payment`			ENUM("COD", "VNPAY")	NOT NULL,
    `isPaid`			BOOLEAN 			NOT NULL DEFAULT False,
    `VoucherId`			INT 		,
    FOREIGN KEY (`AccountId`) REFERENCES `Account` (`Id`)
);

-- DROP TABLE IF EXISTS `OrderStatus`;
CREATE TABLE IF NOT EXISTS `OrderStatus` (
    `OrderId`       CHAR(12)                                                        NOT NULL,
    `Status`        ENUM("ChoDuyet", "DaDuyet", "DangGiao", "GiaoThanhCong", "Huy") NOT NULL,
    `UpdateTime`    DATETIME                                                        NOT NULL	DEFAULT NOW(),
    PRIMARY KEY (`OrderId`, `Status`),
    FOREIGN KEY (`OrderId`) REFERENCES `Order`(`Id`)
);

-- DROP TABLE IF EXISTS `OrderDetail`;
CREATE TABLE IF NOT EXISTS `OrderDetail` (
    `OrderId`       CHAR(12)           NOT NULL,
    `ProductId`     INT UNSIGNED       NOT NULL,
    `Quantity`      INT UNSIGNED       NOT NULL,
    `UnitPrice`     INT UNSIGNED       NOT NULL,
    `Total`         INT UNSIGNED       NOT NULL,
    FOREIGN KEY (`OrderId`) REFERENCES `Order`(`Id`),
    FOREIGN KEY (`ProductId`)     REFERENCES `Product`(`Id`),
    PRIMARY KEY (`ProductId`, `OrderId`)
);
