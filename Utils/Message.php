<?php
namespace CaptainJas\Utils;

/**
 * Class Message
 * @package CaptainJas\Utils
 */
class Message
{

    protected $_text, $_title, $_icon;

    /**
     * @param bool $text
     * @param bool $title
     * @param bool $icon
     */
    public function __construct($text = false, $title = false, $icon = false)
    {
        $this->_text = $text;
        $this->_title = $title;
        $this->_icon = $icon;
    }

    /**
     * get message text
     * @return bool
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * get message title
     * @return bool
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * get message icon
     * @return bool
     */
    public function getIcon()
    {
        return $this->_icon;
    }
}