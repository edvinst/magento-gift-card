<?php

if (@class_exists('Ebizmarts_Mandrill_Model_Email_Template')) {
    class MT_Giftcard_Model_Core_Email_Template_Init extends Ebizmarts_Mandrill_Model_Email_Template
    {}
} else if (@class_exists('Aschroder_SMTPPro_Model_Email_Template')) {
    class MT_Giftcard_Model_Core_Email_Template_Init extends Aschroder_SMTPPro_Model_Email_Template
    {}
} else {
    class MT_Giftcard_Model_Core_Email_Template_Init extends Mage_Core_Model_Email_Template
    {}
}
