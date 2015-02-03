<?php

require_once Mage::getBaseDir('lib').DS.'phpqrcode'.DS.'qrlib.php';


abstract class MT_Giftcard_Model_Giftcard_Design_Abstract
{

    private $__template = null;

    private $__giftCard = null;

    private $__imgResource = null;

    protected $_imgWidth = 2625;

    protected $_imgHeight = 1125;

    protected $_colors = array();

    protected $_fonts = array();

    abstract public function drawBackground();

    abstract public function drawQR();

    abstract public function drawContent();

    abstract public function getFormFields();

   abstract public function getDesignName();

    public function setGiftCard(MT_Giftcard_Model_Giftcard $giftCard)
    {
        $this->__giftCard = $giftCard;
    }

    public function getGiftCard()
    {
        return $this->__giftCard;
    }

    public function getTemplate()
    {
        if ($this->__template == null)
            $this->__template = $this->getGiftCard()->getTemplate();
        return $this->__template;
    }

    public function setTemplate(MT_Giftcard_Model_Template $template)
    {
        $this->__template = $template;
    }

    public function getThemeDir()
    {
        return Mage::getBaseDir('skin').DS.'frontend'.DS.'base'.DS.'default'.DS.'mt'.DS.'giftcard'.DS.'design'.DS.$this->getDesignName().DS;
    }

    public function draw()
    {
        $this->init();
    }

    public function init()
    {
        $this->__imgResource = imagecreatetruecolor($this->_imgWidth, $this->_imgHeight);
        $this->addColor('black', 0, 0, 0);
        $this->addColor('white');
    }

    public function addColor($key, $r = 255, $g = 255, $b = 255)
    {
        $this->_colors[$key] = imagecolorallocate($this->getImg(), $r, $g, $b);
    }

    public function addFont($key, $fontPath)
    {
        $this->_fonts[$key] = $fontPath;
    }

    public function getColor($key)
    {
        return $this->_colors[$key];
    }

    public function getFont($key)
    {
        return $this->_fonts[$key];
    }

    public function getImg()
    {
        return $this->__imgResource;
    }

    public function getImgPath($imgName)
    {
        return $this->getThemeDir().'img'.DS.$imgName;
    }

    public function getFontPath($fileName)
    {
        return $this->getThemeDir().'fonts'.DS.$fileName;
    }

    public function getImgWidth($image, $fullPath = false)
    {
        if (!$fullPath)
            $image = $this->getImgPath($image);

        if (!file_exists($image) || is_dir($image))
            return 0;

        $img = getimagesize($image);
        return $img[0];
    }

    public function getImgHeight($image, $fullPath = false)
    {
        if (!$fullPath)
            $image = $this->getImgPath($image);
        if (!file_exists($image)  || is_dir($image))
            return 0;
        $img = getimagesize($image);
        return $img[1];
    }

    public function saveToFile()
    {
        ob_start();
        imagejpeg($this->getImg(), NULL, 100);
        $image = ob_get_contents();
        ob_end_clean();
        $image = substr_replace($image, pack("Cnn", 0x01, 300, 300), 13, 5);
        file_put_contents('var/mt/jpg/aaa.jpg', $image);

        header('Content-type:image/jpg');
        echo $image;
        exit;
    }

    public function getImageSource()
    {
        ob_start();
        imagejpeg($this->getImg(), "", 100);
        $image = ob_get_contents();
        ob_end_clean();
        $image = substr_replace($image, pack("Cnn", 0x01, 300, 300), 13, 5);
        return $image;
    }


    public function getQR()
    {
        $qrImage = Mage::getBaseDir('tmp').DS.'qr_'.md5(time().''.Mage::getBaseUrl()).'.png';
        QRcode::png(Mage::getBaseUrl(), $qrImage, 4, 10, 0);
        if (!file_exists($qrImage))
            return false;

        $qr = imagecreatefrompng($qrImage);
        unlink($qrImage);
        return $qr;
    }

    public function getTextWidth($font, $size, $text)
    {
        $arSize = imagettfbbox($size, 0, $font, $text);
        return abs($arSize[2] - $arSize[0]);
    }

    public function getTextHeight($font, $size, $text)
    {
        $arSize = imagettfbbox($size, 0, $font, $text);
        return abs($arSize[7] - $arSize[1]);

    }

    public function getImagePath()
    {
        $filePath = Mage::getBaseDir('tmp').DS.md5(''.time().rand(0, 99999).Mage::getBaseUrl()).'.jpg';
        //imagejpeg($this->getImg());

        ob_start();
        imagejpeg($this->getImg());
        $contents =  ob_get_contents();
        //Converting Image DPI to 300DPI
        $contents = substr_replace($contents, pack("cnn", 1, 300, 300), 13, 5);
        ob_end_clean();
        file_put_contents($filePath, $contents);

        if (!file_exists($filePath))
            return false;

        return $filePath;
    }
    public function displayImage()
    {
        header('Content-type:image/jpg');
        imagejpeg($this->getImg(), '', 100);
    }

    public function getAdditionalFields()
    {
        $formFields = $this->getFormFields();
        $designName = $this->getDesignName();

        if (count($formFields) == 0)
            return array();

        foreach ($formFields as $key => $field) {
            if (!isset($field[2]['name']))
                unset($formFields[$key]);

            $formFields[$key][2]['index'] = $formFields[$key][2]['name'];
            $formFields[$key][0] = $designName.'_'.$field[0];
            if ($field[0] == 'background')
                $formFields[$key][2]['name'] = $designName.'_'.$field[2]['name'];
            else
                $formFields[$key][2]['name'] = $designName.'['.$field[2]['name'].']';

        }
        return $formFields;
    }
}