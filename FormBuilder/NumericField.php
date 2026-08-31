<?php
/**
 * Created by PhpStorm.
 * User: pkaluza
 * Date: 2/16/16
 * Time: 1:36 PM
 */

class NumericField
{
    const DEFAULT_VALUE ='0';

    function __construct($name, $label, $value='')
    {
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;

    }

    function render()
    {

        $value = (!empty($this->value)) ? $this->value : self::DEFAULT_VALUE;
        $html = <<<HTML
        <tr>
        <td> {$this->label}</td>
        <td> <input type="text" name="{$this->name}" value='{$value}'></td>
        </tr>
HTML;

        return $html;

    }


}
