<?php

class MT_Giftcard_Model_Catalog_Product_Option
    extends Mage_Catalog_Model_Product_Option
{
    const OPTION_GROUP_GIFT_CARD      = 'gift_card';
    const OPTION_TYPE_GIFT_CARD_VALUE   = 'gift_card_value';

    protected $_giftCardValues = null;

    public function getGroupByType($type = null)
    {
        if (is_null($type)) {
            $type = $this->getType();
        }

        $group = parent::getGroupByType($type);
        if( $group === '' && $type = self::OPTION_TYPE_GIFT_CARD_VALUE ){
            $group = self::OPTION_GROUP_GIFT_CARD;
        }
        return $group;
    }

    public function groupFactory($type)
    {
        //if (!Mage::helper('giftcard')->isActive())
        //    return parent::groupFactory($type);

        if($type === self::OPTION_TYPE_GIFT_CARD_VALUE ){
            return Mage::getModel('giftcard/catalog_product_option_type_giftcardvalue');
        }

        return parent::groupFactory($type);
    }
}