-- База данных со всеми товарами
CREATE TABLE `product` (
  `ID` char(36) NOT NULL,
  `CatalogID` char(36) NOT NULL,
  `Name` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Description` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Ingredients` varchar(4000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `NutritionalValue` varchar(800) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `CreationDate` datetime NOT NULL,
  `Status` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`ID`),
  KEY `Product_Catalog_ID_fk` (`CatalogID`),
  CONSTRAINT `Product_Catalog_ID_fk` FOREIGN KEY (`CatalogID`) REFERENCES `catalog` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci