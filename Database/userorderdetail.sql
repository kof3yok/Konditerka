-- База данных с деталями историй заказов пользователей
CREATE TABLE `userorderdetail` (
  `ID` char(36) NOT NULL,
  `ProductID` char(36) NOT NULL,
  `OrderID` char(36) DEFAULT NULL,
  `Quantity` int NOT NULL,
  `Price` decimal(30,2) NOT NULL,
  `CreationDate` datetime NOT NULL,
  `PriceID` char(36) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `UserOrder_Product_ID_fk` (`ProductID`),
  KEY `userorderdetail_userorder_ID_fk` (`OrderID`),
  KEY `userorderdetail_productprice_ID_fk` (`PriceID`),
  CONSTRAINT `UserOrder_Product_ID_fk` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ID`),
  CONSTRAINT `userorderdetail_productprice_ID_fk` FOREIGN KEY (`PriceID`) REFERENCES `productprice` (`ID`),
  CONSTRAINT `userorderdetail_userorder_ID_fk` FOREIGN KEY (`OrderID`) REFERENCES `userorder` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci