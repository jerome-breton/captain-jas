<?php
/**
 * Created by PhpStorm.
 * User: jerome
 * Date: 22/11/14
 * Time: 16:31
 */

namespace CaptainJas\Utils;


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

    public function getText()
    {
        return $this->_text;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function getIcon()
    {
        return $this->_icon;
    }
} 