INSERT IGNORE INTO schemaversion (versionnumber) values ("ocr-patch");
-- Spark! Summer 2023 - Spring 2025

-- Create ocr_results table
DROP TABLE IF EXISTS `ocr_results`;
CREATE TABLE `ocr_results` (
  `imgid` int(10) unsigned NOT NULL,
  `batchID` int(11) NOT NULL,
  `collID` int(11) NOT NULL,
  `results` JSON NOT NULL,
  `processed_date` timestamp NOT NULL,
  PRIMARY KEY (`imgid`,`batchID`),
  KEY `FK_ocr_results_img` (`imgid`),
  KEY `FK_ocr_results_batch` (`batchID`),
  CONSTRAINT `FK_ocr_results_img` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ocr_results_batch` FOREIGN KEY (`batchID`) REFERENCES `batch` (`batchID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;