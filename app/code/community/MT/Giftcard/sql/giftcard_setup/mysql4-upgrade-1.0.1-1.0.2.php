<?php
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer = Mage::getResourceModel('sales/setup', 'default_setup');

$installer->startSetup();


$installer->addAttribute('quote_address', 'mt_gift_card_total', array(
    'label' => 'Gift Card Total',
    'type'  => 'decimal',
));

$installer->addAttribute('quote_address', 'base_mt_gift_card_total', array(
    'label' => 'Base Gift Card Total',
    'type'  => 'decimal',
));

$installer->addAttribute('order', 'base_mt_gift_card_total', array(
    'label' => 'Base Gift Card Total',
    'type'  => 'decimal',
));

$installer->addAttribute('order', 'mt_gift_card_total', array(
    'label' => 'Gift Card Total',
    'type'  => 'decimal',
));

$installer->addAttribute('invoice', 'base_mt_gift_card_total', array(
    'label' => 'Base Gift Card Total',
    'type'  => 'decimal',
));

$installer->addAttribute('invoice', 'mt_gift_card_total', array(
    'label' => 'Gift Card Total',
    'type'  => 'decimal',
));

$installer->addAttribute('creditmemo', 'base_mt_gift_card_total', array(
    'label' => 'Base Gift Card Total',
    'type'  => 'decimal',
));

$installer->addAttribute('creditmemo', 'mt_gift_card_total', array(
    'label' => 'Gift Card Total',
    'type'  => 'decimal',
));


$installer->endSetup();
