-- База данных с историей заказов
CREATE TABLE `userorder` (
  `ID` char(36) NOT NULL,
  `UserID` char(36) NOT NULL,
  `Price` decimal(30,2) NOT NULL,
  `CreationDate` datetime NOT NULL,
  `Status` int NOT NULL DEFAULT '0',
  `Driver` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Address` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `UserOrder_User_ID_fk` (`UserID`),
  CONSTRAINT `UserOrder_User_ID_fk` FOREIGN KEY (`UserID`) REFERENCES `user` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci