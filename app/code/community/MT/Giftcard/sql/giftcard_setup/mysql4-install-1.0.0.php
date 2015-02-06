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
	`store_id` SMALLINT(5) UNSIGNED NOT NULL,
	`series_id` INT(5) UNSIGNED NULL DEFAULT NULL,
	`template_id` INT(5) UNSIGNED NULL DEFAULT NULL,
	`currency` VARCHAR(4) NOT NULL,
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
	`giftcard_price` FLOAT UNSIGNED NULL DEFAULT NULL,
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
	`store_id` SMALLINT(5) UNSIGNED NOT NULL,
	`template_id` INT(5) UNSIGNED NOT NULL,
	`name` VARCHAR(50) NOT NULL,
	`currency` VARCHAR(4) NOT NULL,
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
CREATE TABLE IF NOT EXISTS `{$installer->getTable('giftcard/template')}` (
	`entity_id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	`store_id` INT(5) UNSIGNED NOT NULL,
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
INSERT IGNORE INTO `{$giftCardOptionTable}` (`entity_id`, `name`, `title`, `type`, `source_model`, `is_required`) VALUES (2, 'giftcard_is_real', 'Get Gift Card by Post', 'checbox', '', 0);

INSERT IGNORE INTO `{$giftCardTemplateTable}` (`entity_id`, `store_id`, `is_active`, `name`, `description`, `design`, `title`, `title2`, `title3`, `title_size`, `title2_size`, `title3_size`, `title_y`, `title2_y`, `title3_y`, `note`, `color1`, `color2`, `color3`, `color4`, `color5`, `text1`, `text2`, `image`, `created_at`) VALUES (1, 0, 1, 'Air Balloons', 'Bo Balloons gift card', 'one', 'Gift Card', 'Bo Balloons', '', 0, 0, 0, 0, 0, 0, 'This gift card can be used|for any of our store. |Visit: www.example.com', '#0089bf', '#C4ECF7', '#F25100', '#FFFFFF', '#1C1C1C', '', '', 'background1.jpg', '2015-01-06 13:33:12');
INSERT IGNORE INTO `{$giftCardTemplateTable}` (`entity_id`, `store_id`, `is_active`, `name`, `description`, `design`, `title`, `title2`, `title3`, `title_size`, `title2_size`, `title3_size`, `title_y`, `title2_y`, `title3_y`, `note`, `color1`, `color2`, `color3`, `color4`, `color5`, `text1`, `text2`, `image`, `created_at`) VALUES (2, 0, 1, 'Air Balloon two', '', 'two', 'Gift Card', 'Bo Balloons', '', 0, 0, 0, 0, 0, 0, 'Note: This gift card can be used for any of our Store. Visit: www.example.com', '#0089bf', '#C4ECF7', '#F25100', '#FFFFFF', '#1C1C1C', '', '', 'background2.jpg', '2015-01-06 15:14:38');
INSERT IGNORE INTO `{$giftCardTemplateTable}` (`entity_id`, `store_id`, `is_active`, `name`, `description`, `design`, `title`, `title2`, `title3`, `title_size`, `title2_size`, `title3_size`, `title_y`, `title2_y`, `title3_y`, `note`, `color1`, `color2`, `color3`, `color4`, `color5`, `text1`, `text2`, `image`, `created_at`) VALUES (3, 0, 1, 'Shoes gift card', '', 'one', 'Gift Card', 'Shoes & More', '', 0, 0, 0, 0, 0, 0, 'This gift card can be used|for any of our Store. |Visit: www.example.com', '5979aa', 'dfbe6d', '', '', '#383838', '', '', 'background3.jpg', '2015-01-06 14:12:42');
INSERT IGNORE INTO `{$giftCardTemplateTable}` (`entity_id`, `store_id`, `is_active`, `name`, `description`, `design`, `title`, `title2`, `title3`, `title_size`, `title2_size`, `title3_size`, `title_y`, `title2_y`, `title3_y`, `note`, `color1`, `color2`, `color3`, `color4`, `color5`, `text1`, `text2`, `image`, `created_at`) VALUES (4, 0, 1, 'Bags & More', '', 'two', 'Gift Card', 'Bags & More', '', 0, 0, 0, 0, 0, 0, 'Note: This gift card can be used for any of our Store. Visit: www.example.com', 'c21b35', '#FCFCFC', '0371c6', '', '', '', '', 'background4.jpg', '2015-01-06 14:30:50');

");

$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->loadByCode('catalog_product','gift_card_type');

if (!$attribute->getId()) {
    $installer->addAttribute('catalog_product', "gift_card_type", array(
        'apply_to' => 'giftcard',
        'type'       => 'varchar',
        'input'      => 'select',
        'is_configurable' => 0,
        'label'      => 'Gift Card Type',
        'sort_order' => 1,
        'required'   => false,
        'user_defined'   => true,
        'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'source' => 'giftcard/adminhtml_system_config_source_giftcard_type',
        // 'note' => 'Template is using to generate .pdf and send giftcard email'
    ));
}

$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->loadByCode('catalog_product','gift_card_cancel_real');

if (!$attribute->getId()) {
    $installer->addAttribute('catalog_product', "gift_card_cancel_real", array(
        'apply_to' => 'giftcard',
        'type'       => 'int',
        'input'      => 'boolean',
        'is_configurable' => 0,
        'label'      => 'Allow Refuse Real Gift Card',
        'sort_order' => 1,
        'required'   => false,
        'user_defined'   => true,
        'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'source' => 'adminhtml/system_config_source_yesno',
        'note' => 'Allow for mixed type gift cards<br/>'
    ));
}

$modelGroup = Mage::getModel('eav/entity_attribute_group');
$modelGroup->setAttributeGroupName('Gift Cards Options')
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'default'))
    ->setSortOrder(2);

if (!$modelGroup->itemExists())
    $modelGroup->save();


$attributesToSet = array(

    array(
        'attribute_code' => 'gift_card_type',
        'group' => 'Gift Cards Options',
        'attribute_set' => 'default',
        'order' => '0'
    ),

    array(
        'attribute_code' => 'gift_card_cancel_real',
        'group' => 'Gift Cards Options',
        'attribute_set' => 'default',
        'order' => '4'
    ),

);

foreach ($attributesToSet as $attribute) {

    $attributeSetId = $installer->getAttributeSetId('catalog_product', $attribute['attribute_set']);
    $attributeGroupId = $installer->getAttributeGroupId('catalog_product', $attributeSetId, $attribute['group']);
    $attributeId = $installer->getAttributeId('catalog_product', $attribute['attribute_code']);
    $installer->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId, $attribute['order']);
}

//add price attributes

$attributes = array(
    'price',
    'special_price',
    'special_from_date',
    'special_to_date',
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
