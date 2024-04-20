-- База данных с изображениями товаров
CREATE TABLE `productpicture` (
  `ID` char(36) NOT NULL,
  `ProductID` char(36) NOT NULL,
  `ImageData` longblob NOT NULL,
  `First` bit(1) NOT NULL DEFAULT b'0',
  `Status` bit(1) NOT NULL,
  `CreationDate` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ProductPicture_Product_ID_fk` (`ProductID`),
  CONSTRAINT `ProductPicture_Product_ID_fk` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci