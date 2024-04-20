-- База данных с адресами пользователей
CREATE TABLE `address` (
  `ID` char(36) NOT NULL,
  `UserID` char(36) NOT NULL,
  `Address` varchar(800) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `CreationDate` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Address_User_ID_fk` (`UserID`),
  CONSTRAINT `Address_User_ID_fk` FOREIGN KEY (`UserID`) REFERENCES `user` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
