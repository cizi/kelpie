ALTER TABLE `enum_item`
    ADD `is_ake` tinyint(1) NOT NULL DEFAULT '0',
ADD `is_wcc` tinyint(1) NOT NULL DEFAULT '0' AFTER `is_ake`,
ADD `is_wcp` tinyint(1) NOT NULL DEFAULT '0' AFTER `is_wcc`;

ALTER TABLE `enum_item`
    ADD `health_group` varchar(3) NULL DEFAULT '-' COMMENT 'AKE/WCC/WCP';
