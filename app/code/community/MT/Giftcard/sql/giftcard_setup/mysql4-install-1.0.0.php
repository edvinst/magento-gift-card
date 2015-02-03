<?php
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$giftCardTable = $installer->getTable('giftcard/giftcard');
$giftCardSeriesTable = $installer->getTable('giftcard/series');
$giftCardTemplateTable = $installer->getTable('giftcard/template');
$giftCardOptionTable = $installer->getTable('giftcard/option');
$giftCardSeriesProductTable = $installer->getTable('giftcard/series_product');

$installer->run("
CREATE TABLE IF NOT EXISTS `{$giftCardTable}` (
	`entity_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'unique id',
	`series_id` INT(5) UNSIGNED NULL DEFAULT NULL,
	`template_id` INT(5) UNSIGNED NULL DEFAULT NULL,
	`code` VARCHAR(50) NOT NULL COMMENT 'gift card code',
	`value` DECIMAL(12,4) NOT NULL,
	`balance` DECIMAL(12,4) NOT NULL,
	`status` VARCHAR(20) NOT NULL COMMENT 'gift card status: pre_created, active, used',
	`state` VARCHAR(50) NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'gift card creation date',
	`lifetime` INT(4) UNSIGNED NOT NULL,
	`expired_at` TIMESTAMP NULL DEFAULT NULL,
	`order_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	`order_item_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	PRIMARY KEY (`entity_id`)
)
COMMENT='gift cards'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0;
");

$installer->run("
CREATE TABLE IF NOT EXISTS `{$giftCardSeriesProductTable}` (
	`giftcard_series_id` INT(11) NULL DEFAULT NULL,
	`product_id` INT(11) NULL DEFAULT NULL,
	`position` INT(5) UNSIGNED NULL DEFAULT NULL
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;
");

$installer->run("
CREATE TABLE IF NOT EXISTS `{$giftCardOptionTable}` (
	`entity_id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NULL DEFAULT NULL,
	`title` VARCHAR(255) NULL DEFAULT NULL,
	`type` VARCHAR(20) NULL DEFAULT NULL,
	`source_model` VARCHAR(255) NULL DEFAULT NULL,
	`is_required` TINYINT(1) UNSIGNED NULL DEFAULT NULL,
	PRIMARY KEY (`entity_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0;
");
$installer->run("
CREATE TABLE IF NOT EXISTS `{$giftCardSeriesTable}` (
	`entity_id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	`template_id` INT(5) UNSIGNED NOT NULL,
	`name` VARCHAR(50) NOT NULL,
	`description` TEXT NULL,
	`value` DECIMAL(12,4) NOT NULL,
	`lifetime` INT(4) UNSIGNED NOT NULL,
	`expired_at` VARCHAR(19) NULL DEFAULT NULL,
	`default_length` TINYINT(2) NULL DEFAULT NULL,
	`default_format` VARCHAR(15) NULL DEFAULT NULL,
	`default_prefix` VARCHAR(10) NULL DEFAULT NULL,
	`default_suffix` VARCHAR(10) NULL DEFAULT NULL,
	`default_dash` TINYINT(2) NULL DEFAULT NULL,
	PRIMARY KEY (`entity_id`)
)
COMMENT='Gift cards series'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0;
");
$installer->run("
CREATE TABLE IF NOT EXISTS `{$giftCardTemplateTable}` (
	`entity_id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	`is_active` INT(1) UNSIGNED NOT NULL DEFAULT '0',
	`name` VARCHAR(50) NOT NULL,
	`description` TEXT NOT NULL,
	`design` VARCHAR(20) NOT NULL,
	`title` TEXT NOT NULL,
	`title2` TEXT NOT NULL,
	`title3` TEXT NOT NULL,
	`title_size` INT(4) UNSIGNED NOT NULL,
	`title2_size` INT(4) NOT NULL,
	`title3_size` INT(4) NOT NULL,
	`title_y` INT(4) NOT NULL,
	`title2_y` INT(4) NOT NULL,
	`title3_y` INT(4) NOT NULL,
	`note` TEXT NOT NULL,
	`color1` VARCHAR(7) NOT NULL,
	`color2` VARCHAR(7) NOT NULL,
	`color3` VARCHAR(7) NOT NULL,
	`color4` VARCHAR(7) NOT NULL,
	`color5` VARCHAR(7) NOT NULL,
	`text1` MEDIUMTEXT NOT NULL,
	`text2` MEDIUMTEXT NOT NULL,
	`image` VARCHAR(255) NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`entity_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0;
");

$installer->run("
INSERT IGNORE INTO `{$giftCardOptionTable}` (`entity_id`, `name`, `title`, `type`, `source_model`, `is_required`) VALUES (1, 'giftcard_value', 'Gift Card Value', 'select', 'giftcard/option_source_value', 1);

INSERT IGNORE INTO `{$giftCardTemplateTable}` (`entity_id`, `is_active`, `name`, `description`, `design`, `title`, `title2`, `title3`, `title_size`, `title2_size`, `title3_size`, `title_y`, `title2_y`, `title3_y`, `note`, `color1`, `color2`, `color3`, `color4`, `color5`, `text1`, `text2`, `image`, `created_at`) VALUES (2, 1, 'Air Balloon two', '', 'default', 'Gift Card', 'Bo Balloons', '', 0, 0, 0, 0, 0, 0, 'Note: This gift card can be used for any of our Store. Visit: www.example.com', '#0089bf', '#C4ECF7', '#F25100', '#FFFFFF', '#1C1C1C', '', '', 'background2.jpg', '2015-01-06 15:14:38');
INSERT IGNORE INTO `{$giftCardTemplateTable}` (`entity_id`, `is_active`, `name`, `description`, `design`, `title`, `title2`, `title3`, `title_size`, `title2_size`, `title3_size`, `title_y`, `title2_y`, `title3_y`, `note`, `color1`, `color2`, `color3`, `color4`, `color5`, `text1`, `text2`, `image`, `created_at`) VALUES (4, 1, 'Bags & More', '', 'default', 'Gift Card', 'Bags & More', '', 0, 0, 0, 0, 0, 0, 'Note: This gift card can be used for any of our Store. Visit: www.example.com', 'c21b35', '#FCFCFC', '0371c6', '', '', '', '', 'background4.jpg', '2015-01-06 14:30:50');

");

$attributes = array(
    'price',
    'minimal_price',
    'tax_class_id',
    'group_price'
);

foreach ($attributes as $attributeCode) {
    $applyTo = explode(
        ',',
        $installer->getAttribute(
            Mage_Catalog_Model_Product::ENTITY,
            $attributeCode,
            'apply_to'
        )
    );

    if (!in_array('giftcard', $applyTo)) {
        $applyTo[] = 'giftcard';
        $installer->updateAttribute(
            Mage_Catalog_Model_Product::ENTITY,
            $attributeCode,
            'apply_to',
            join(',', $applyTo)
        );
    }
}

if (!is_dir(Mage::getBaseDir('tmp')))
   @mkdir(Mage::getBaseDir('tmp'), 0777);

$requiredDir = Mage::getBaseDir('media').DS.'mt'.DS.'giftcard'.DS.'template'.DS;
if (!is_dir($requiredDir))
    @mkdir($requiredDir, 0777);


$installer->endSetup();
