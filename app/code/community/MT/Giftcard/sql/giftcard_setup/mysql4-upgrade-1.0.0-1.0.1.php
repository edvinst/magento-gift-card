<?php
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

$installer->addAttribute('order','mt_gift_card',array(
    'label' => 'Gift Cards',
    'type'  => 'text',
));

$installer->addAttribute('quote','mt_gift_card',array(
    'label' => 'Gift Cards',
    'type'  => 'text',
));

$installer->addAttribute('invoice','mt_gift_card',array(
    'label' => 'Gift Cards',
    'type'  => 'text',
));

$installer->addAttribute('creditmemo','mt_gift_card',array(
    'label' => 'Gift Cards',
    'type'  => 'text',
));


$installer->endSetup();
