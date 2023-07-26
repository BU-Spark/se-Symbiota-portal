-- This update patch is used to fix the bugs after running the 1.3 patch after updating symbiota version to 3.09
-- For production it is highly recommended to combine this patch with the 1.3 patch

-- Undo the changes to the omoccurrences table due to the addition of dropdown tables
ALTER TABLE `omoccurrences`
    DROP FOREIGN KEY `FK_omoccurrences_filedUnder`,
    DROP FOREIGN KEY `FK_omoccurrences_currName`,
    DROP FOREIGN KEY `FK_omoccurrences_identifiedBy`,
    DROP FOREIGN KEY `FK_omoccurrences_recordedBy`,
    DROP FOREIGN KEY `FK_omoccurrences_container`,
    DROP FOREIGN KEY `FK_omoccurrences_collTrip`,
    DROP FOREIGN KEY `FK_omoccurrences_geoWithin`,
    DROP FOREIGN KEY `FK_omoccurrences_highGeo`,
    DROP FOREIGN KEY `FK_omoccurrences_prepMethod`,
    DROP FOREIGN KEY `FK_omoccurrences_format`;

ALTER TABLE `omoccurrences`
    CHANGE `filedUnder` `filedUnder` varchar(255) DEFAULT NULL,
    CHANGE `currName` `currName` varchar(255) DEFAULT NULL,
    CHANGE `identifiedBy` `identifiedBy` varchar(255) DEFAULT NULL,
    CHANGE `recordedBy` `recordedBy` varchar(255) DEFAULT NULL COMMENT 'Collector(s)',
    CHANGE `container` `container` varchar(255) DEFAULT NULL,
    CHANGE `collTrip` `collTrip` varchar(255) DEFAULT NULL,
    CHANGE `geoWithin` `geoWithin` text DEFAULT NULL,
    CHANGE `highGeo` `highGeo` text DEFAULT NULL,
    CHANGE `prepMethod` `prepMethod` varchar(64) DEFAULT NULL,
    CHANGE `format` `format` varchar(64) DEFAULT NULL;