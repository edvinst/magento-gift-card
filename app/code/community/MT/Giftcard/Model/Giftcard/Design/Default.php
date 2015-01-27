<?php

class MT_Giftcard_Model_Giftcard_Design_Default
    extends  MT_Giftcard_Model_Giftcard_Design_Abstract
{
    const DESIGN_NAME = 'default';

    protected $_imgWidth = 2625;

    protected $_imgHeight = 1125;

    protected $_qrW = 0;

    protected $_qrH = 0;

    protected $_titleW = 0;

    protected $_titleH = 0;

    protected $_title2H = 68;

    protected $_bgH = 685;

    protected $_codeLabelW = 645;

    protected $_contentStartY = 728;

    protected $_contentStartX = 156;

    protected $_defaultTitleSize = 134;

    protected $_contentLine1Size = 136;

    public function getDesignName()
    {
        return self::DESIGN_NAME;
    }

    public function init()
    {
        parent::init();
        $giftCardTemplate = $this->getTemplate();
        $helper = Mage::helper('giftcard');

        //set colors
        $color1 = $helper->hex2rgb($giftCardTemplate->getColor1()!=''?$giftCardTemplate->getColor1():'ee3432');
        $color2 = $helper->hex2rgb($giftCardTemplate->getColor2()!=''?$giftCardTemplate->getColor2():'e3b492');
        $color3 = $helper->hex2rgb($giftCardTemplate->getColor3()!=''?$giftCardTemplate->getColor3():'49494a');
        $color4 = $helper->hex2rgb($giftCardTemplate->getColor4()!=''?$giftCardTemplate->getColor4():'ffffff');

        $this->addColor('color1', $color1[0], $color1[1],  $color1[2]);
        $this->addColor('color2', $color2[0], $color2[1],  $color2[2]);
        $this->addColor('color3', $color3[0], $color3[1],  $color3[2]);
        $this->addColor('color4', $color4[0], $color4[1],  $color4[2]);

        $this->addFont('font1', $this->getFontPath('Roboto-Light.ttf'));
        $this->addFont('font2', $this->getFontPath('Roboto-Medium.ttf'));
        $this->addFont('font3', $this->getFontPath('Roboto-Black.ttf'));
        $this->addFont('font4', $this->getFontPath('myriad-web-pro.ttf'));

    }

    public function draw()
    {
        parent::draw();
        $this->drawBackground();
        $this->drawQR();
        $this->drawContent();
    }

    public function drawBackground()
    {
        $template = $this->getTemplate();
        $helper = Mage::helper('giftcard');
        if ($template->getImage() != '') {
            $backgroundImage = Mage::helper('giftcard')->getGiftCardBackgroundDir().$template->getImage();
            if ($backgroundImage == '')
                $backgroundImage = $this->getImgPath('bg.jpg');

            if (file_exists($backgroundImage)) {
                $imgWidth = $this->getImgWidth($backgroundImage, true);
                $imgHeight = $this->getImgHeight($backgroundImage, true);
                imagecopy($this->getImg(), imagecreatefromjpeg($backgroundImage), 0, 0, 0, 0, $imgWidth, $imgHeight);
            }
        }

        $contentPanel = imagecreatetruecolor($this->_imgWidth, 440);

        $color = Mage::helper('giftcard')->hex2rgb($template->getColor2()!=''?$template->getColor2():'e3b492');
        $leftSideColour = imagecolorallocatealpha($contentPanel, $color[0],$color[1],$color[2],10);
        imagefill($contentPanel,0,0,$leftSideColour);
        imagecopy($this->getImg(), $contentPanel, 0, 685, 0, 0, $this->_imgWidth, 440);

        //small line
        imagefilledrectangle ($this->getImg() , 0 , $this->_bgH+18 , $this->_imgWidth ,  $this->_bgH+22 , $this->getColor('color4'));

        //price label
        $priceLabel = 'price2.png';
        imagefilledellipse ( $this->getImg() , 387 , 384 , 460 , 460 , $this->getColor('color1') );
        imagecopy($this->getImg(), imagecreatefrompng($this->getImgPath($priceLabel)), 156, 153, 0, 0, $this->getImgWidth($priceLabel), $this->getImgHeight($priceLabel));

    }

    public function drawQR()
    {
        if (!$qr = $this->getQR())
            return false;

        $this->_qrW = imagesx($qr);
        $this->_qrH = imagesy($qr);
        imagecolortransparent($qr, imagecolorexact($qr, 255, 255, 255));
        imagecopy($this->getImg(), $qr, $this->_contentStartX, $this->_contentStartY, 0, 0, $this->_qrW, $this->_qrH);
        //space after qr
        $this->_qrW +=71;

        return true;
    }

    public function drawContent()
    {
        $giftCard = $this->getGiftCard();
        $giftCardTemplate = $this->getTemplate();;
        $this->drawPrice($giftCard);
        $this->drawTitle($giftCardTemplate);
        $this->drawCode($giftCard);
        $this->drawTitle2($giftCardTemplate);
        $this->drawNote($giftCardTemplate);
    }

    protected function drawPrice()
    {
        $giftCard = $this->getGiftCard();
        $value = $giftCard->getFormatedValue();
        $startX = $this->_contentStartX+40;
        $startY = 385;

        // help line imageline ( $this->getImg() , 0 , $startY , 9999 , $startY , $this->getColor('color1') );
        $font = $this->getFont('font3');
        $fontSize = 130;
        $step = 3;
        $maxWidth = $this->getTextWidth($font,$fontSize, '$$$$')-10;
        $maxHeight = 200;

        //find font size
        for ($i = 0; $i < 30; $i++) {
            $nextW = $this->getTextWidth($font,$fontSize+$step, $value);
            $prevW = $this->getTextWidth($font,$fontSize-$step, $value);

            if ($nextW < $maxWidth )
                $fontSize+=$step;
            elseif ($prevW > $maxWidth)
                $fontSize-=$step;
            else
                break;
        }

        $currentW = $this->getTextWidth($font,$fontSize, $value);
        if ($maxWidth < $currentW) {
            $fontSize-=$step;
            $currentW = $this->getTextWidth($font,$fontSize, $value);
        }

        if (($maxWidth - $currentW)/2 > 0) {
            $startX = $startX + ($maxWidth - $currentW)/2;
        }

        $currentH = $this->getTextHeight($font,$fontSize, $value);
        if ($currentH > $maxHeight) {
            for ($i = 0; $i < 30; $i++) {
                $prevH = $this->getTextHeight($font,$fontSize-$step, $value);
                if ($prevH < $maxHeight)
                    break;
                $fontSize-=$step;
            }

        }

        imagettftext($this->getImg(), $fontSize, 0, $startX , $startY+$fontSize/2 , $this->getColor('color4'), $font, $value);
    }

    protected  function drawTitle($template)
    {
        if (!$title = $template->getTitle())
            return;

        $fontSize = $template->getTitleSize()?$template->getTitleSize():$this->_defaultTitleSize;
        $font = $this->getFont('font3');
        $textH = $this->getTextHeight($font, $fontSize, $title);
        $this->_titleW = $this->getTextWidth($font, $fontSize, $title)+78;
        $this->_titleH = $this->getTextHeight($font, $fontSize, $title);
        imagettftext($this->getImg(), $fontSize, 0, $this->_contentStartX+$this->_qrW, $this->_contentStartY+$fontSize, $this->getColor('color1'), $font, $title);

    }

    protected function drawNote()
    {
        $template = $this->getTemplate();
        $note = $template->getNote();
        $startX = $this->_contentStartX+$this->_qrW;
        $startY = $this->_contentStartY+$this->_contentLine1Size+31;
        $height = 62;
        //v-align with QR bottom
        if ($this->_qrH != 0) {
            if ($this->_qrH <= $this->_contentLine1Size+31+$height+50) {
                $startY = $this->_contentStartY + $this->_qrH - $height;
            }
        }

        $giftCard = $this->getGiftCard();
        $expiredAt = $giftCard->getExpiredAt();
        $expiredAtText = '';
        if (!empty($expiredAt)) {
            $date = Mage::helper('core')->formatDate($expiredAt, 'medium', false);
            $expiredAtText = $template->getText2()?$template->getText2():Mage::helper('giftcard')->__('Valid:');
            $expiredAtText = $expiredAtText . ' ' . $date;
        }

        imagefilledrectangle ($this->getImg(), $startX, $startY, $width = $this->_imgWidth, $startY+$height , $this->getColor('color1'));
        imagettftext($this->getImg(), 30, 0, $startX+40 , $startY+47 , $this->getColor('color4'), $this->getFont('font2'), $expiredAtText. '  ' . str_replace("|","",$note));
    }

    protected function drawCode($giftCard)
    {
        $code = $giftCard->getCode();
        $template = $this->getTemplate();
        $date = Mage::helper('core')->formatDate($giftCard->getExpiredAt(), 'medium', false);
        $font = $this->getFont('font3');
        $fontSize = 40;
        $maxWidth = 500;

        if ($this->getTextWidth($font,$fontSize, $code) > $maxWidth) {
            $step = 3;
            for ($i = 0; $i < 30; $i++) {
                $nextW = $this->getTextWidth($font,$fontSize+$step, $code);
                $prevW = $this->getTextWidth($font,$fontSize-$step, $code);

                if ($nextW < $maxWidth )
                    $fontSize+=$step;
                elseif ($prevW > $maxWidth)
                    $fontSize-=$step;
                else
                    break;
            }

            $currentW = $this->getTextWidth($font,$fontSize, $code);
            if ($maxWidth < $currentW) {
                $fontSize-=$step;
            }
        }

        //gift card code rectangle
        $startX = $this->_contentStartX+$this->_qrW+$this->_titleW;
        $width = $startX+$this->_codeLabelW;
        $this->_codeLabelW +=69;
        $height = $this->_contentLine1Size;
        imagefilledrectangle ($this->getImg() , $startX , $this->_contentStartY, $width,  $this->_contentStartY+$height , $this->getColor('color1'));
        imagettftext($this->getImg(), 28, 0, $startX+30, $this->_contentStartY+54, $this->getColor('color4'), $this->getFont('font2'), $template->getText1()?$template->getText1():Mage::helper('giftcard')->__('Gift Card Code:'));
        imagettftext($this->getImg(), $fontSize, 0, $startX+30, $this->_contentStartY+110, $this->getColor('color4'), $font, $code);
    }

    public function drawTitle2($template)
    {
        if (!$title2 = $template->getTitle2())
            return;

        $fontSize = $this->_title2H;
        $title2FirstWord = explode(' ', $title2);
        if (isset($title2FirstWord[0]))
            $title2FirstWord = $title2FirstWord[0];
        $font = $this->getFont('font4');

        $startX = $this->_contentStartX+$this->_qrW+$this->_titleW+$this->_codeLabelW;
        $startY = $this->_contentStartY + $fontSize + (($this->_contentLine1Size-$fontSize) /2);

        imagettftext($this->getImg(), $fontSize, 0, $startX , $startY , $this->getColor('color3'), $font, $title2);
        imagettftext($this->getImg(), $fontSize, 0, $startX , $startY , $this->getColor('color1'), $font, $title2FirstWord);
    }

    public function getFormFields()
    {
        $helper = Mage::helper('giftcard');
        $fields = array();

        $fields[] = array('title', 'text', array(
            'label' => $helper->__('Title'),
            'name' => 'title',
            'class' => 'gift_card_design_value',
            'after_element_html' => '<br/><small>'.$helper->__('Default:').' Gift Card</small>',
        ), 'Gift Card');

        $fields[] = array('title2', 'text', array(
            'label' => $helper->__('Title 2'),
            'name' => 'title2',
            'class' => 'gift_card_design_value',
        ), 'Gift Cards for All');

        $fields[] = array('note', 'textarea', array(
            'label' => $helper->__('Note'),
            'name' => 'note',
            'class' => 'gift_card_design_value',
        ), 'Conditions: This voucher can be|used for any of our Store|Voucher cannot be redeemed|for cash.');

        $fields[] = array('color1', 'text', array(
            'label' => $helper->__('Color 1'),
            'name' => 'color1',
            'class' => 'gift_card_design_value color {required:false, adjust:false, hash:true}',
            'after_element_html' => '<br/><small>'.$helper->__('Default:').' #ee3432</small>',
        ), '#ba318b');

        $fields[] = array('color2', 'text', array(
            'label' => $helper->__('Color 2'),
            'name' => 'color2',
            'class' => 'gift_card_design_value color {required:false, adjust:false, hash:true}',
            'after_element_html' => '<br/><small>'.$helper->__('Default:').' #e3b492</small>',
        ), '#49494a');

        $fields[] = array('color3', 'text', array(
            'label' => $helper->__('Color 3'),
            'name' => 'color3',
            'class' => 'gift_card_design_value color {required:false, adjust:false, hash:true}',
            'after_element_html' => '<br/><small>'.$helper->__('Default:').' #49494a</small>',
        ), '#faa21e');

        $fields[] = array('color4', 'text', array(
            'label' => $helper->__('Color 4'),
            'name' => 'color4',
            'class' => 'gift_card_design_value color {required:false, adjust:false, hash:true}',
            'after_element_html' => '<br/><small>'.$helper->__('Default:').' #ffffff</small>',
        ), '#ffffff');

        $fields[] = array('text1', 'text', array(
            'label' => $helper->__('Gift Card Code (translate)'),
            'after_element_html' => '<br/><small>'.$helper->__('Default:').' Gift Card Code</small>',
            'name' => 'text1',
            'class' => 'gift_card_design_value',
        ));

        $fields[] = array('text2', 'text', array(
            'label' => $helper->__('Valid: (translate)'),
            'name' => 'text2',
            'class' => 'gift_card_design_value',
            'after_element_html' => '<br/><small>'.$helper->__('Default:').' Valid</small>',
        ));

        $fields[] = array('background', 'file', array(
            'label' => Mage::helper('giftcard')->__('Background Image'),
            'name' => 'background',
            'after_element_html' => '<br/><small>'.$helper->__('Required size:').'2625 x 1125. Format: .jpg</small>',

        ));

        return $fields;
    }

}