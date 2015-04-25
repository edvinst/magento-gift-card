<?php
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('giftcard/giftcard');

$giftCardOrderProductTable = $installer->getTable('giftcard/order');
$giftCardQuoteProductTable = $installer->getTable('giftcard/quote');

$installer->run("
CREATE TABLE IF NOT EXISTS `{$giftCardOrderProductTable}` (
	`entity_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`order_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	`gift_card_code` VARCHAR(50) NULL DEFAULT NULL,
	`discount` DECIMAL(12,4) NULL DEFAULT NULL,
	`base_discount` DECIMAL(12,4) NULL DEFAULT NULL,
	`refund` DECIMAL(12,4) NULL DEFAULT NULL,
	`base_refund` DECIMAL(12,4) NULL DEFAULT NULL,
	PRIMARY KEY (`entity_id`),
	UNIQUE INDEX `order_id_gift_card_code` (`order_id`, `gift_card_code`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0;
");

$installer->run("
CREATE TABLE IF NOT EXISTS `{$giftCardQuoteProductTable}` (
	`entity_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`quote_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	`gift_card_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	PRIMARY KEY (`entity_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0;
");


$installer->endSetup();
