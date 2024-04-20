-- База данных с корзиной пользовтеля
CREATE TABLE `cart` (
  `ID` char(36) NOT NULL,
  `PriceID` char(36) NOT NULL,
  `UserID` char(36) NOT NULL,
  `Quantity` int NOT NULL,
  `CreationDate` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Cart_User_ID_fk` (`UserID`),
  KEY `Cart_Product_ID_fk` (`PriceID`),
  CONSTRAINT `Cart_Product_ID_fk` FOREIGN KEY (`PriceID`) REFERENCES `productprice` (`ID`),
  CONSTRAINT `Cart_User_ID_fk` FOREIGN KEY (`UserID`) REFERENCES `user` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci