<?php
$installer = Mage::getResourceModel('sales/setup', 'default_setup');
$installer->startSetup();

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
