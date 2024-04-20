-- База данных с ценами товаров
CREATE TABLE `productprice` (
  `ID` char(36) NOT NULL,
  `ProductID` char(36) NOT NULL,
  `Name` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Description` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Price` decimal(30,2) NOT NULL,
  `Status` bit(1) NOT NULL,
  `CreationDate` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ProductPrice_Product_ID_fk` (`ProductID`),
  CONSTRAINT `ProductPrice_Product_ID_fk` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci