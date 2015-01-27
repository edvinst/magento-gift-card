<?php
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer = Mage::getResourceModel('sales/setup', 'default_setup');

$installer->startSetup();

$installer->addAttribute('order', 'forced_can_creditmemo', array(
    'label' => 'Forced Can Creditmemo',
    'type'  => 'boolean',
));

$installer->addAttribute('order', 'gift_card_refunded', array(
    'label' => 'Gift Card Refunded',
    'type'  => 'boolean',
));

$installer->addAttribute('order', 'mt_gift_card_description', array(
    'label' => 'Gift Card Description',
    'type'  => 'text',
));

$installer->endSetup();
