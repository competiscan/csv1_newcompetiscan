<?php
/**
 * Created by PhpStorm.
 * User: pkaluza
 * Date: 2/16/16
 * Time: 1:53 PM
 */

class DropdownField {

    function __construct($name, $label, $value, $options = array()) {


        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
        $this->options = $options;


    }
    function render() {

        $option_html = $this->buildOptions();

        $html = <<<HTML
        <tr>
        <td> {$this->label} </td>
        <td> <select name="{$this->name}"> $option_html </select></td> </tr>

HTML;

        return $html;
    }


    private function buildOptions() {

        $options_html = '';
        foreach ($this->options as $val => $label ) {

            $selected = ($this->value == $val) ? "selected" : "";
            $options_html.= "<option value='$val' $selected>$label</option>";


        }
        return $options_html;
    }
}