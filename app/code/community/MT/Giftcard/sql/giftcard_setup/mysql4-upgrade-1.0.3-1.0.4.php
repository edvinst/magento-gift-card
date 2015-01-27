<?php
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer = $this;
$installer->startSetup();
/*
$attribute = $installer->getAttribute('catalog_product', 'options_container');
if (!empty($attribute['attribute_id'])) {
    $this->run("
        UPDATE {$this->getTable('catalog_product_entity_varchar')}
        SET value = 'container1'
        WHERE
            entity_id IN (SELECT entity_id FROM {$this->getTable('catalog_product_entity')} WHERE type_id='giftcard')
            AND attribute_id={$attribute['attribute_id']}
    ");
}
*/
$installer->endSetup();