<?php

namespace HS;

// Generic class to help build HTML form elements
class Form
{
    public function __construct()
    {
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $options Associative array of 'display' => 'value' pairs
     * @param string $class
     * @return string
     */
    public function dropdown($name, $value, $options, $class = 'input_box')
    {
        $html = '<select name="'.$name.'" class="'.$class.'">';
        $html .= '<option></option>';

        foreach ($options as $display => $select_value) {
            $selected = ($value == $select_value) ? 'selected="selected"' : '';
            $html .= '<option value="'.$select_value.'" '.$selected.'>'.$display.'</option>';
        }

        $html .= '</select>';
        return $html;
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $type
     * @param string $class
     * @return string
     */
    public function input($name, $value, $type = 'text', $class = 'input_box')
    {
        if ($value == '0') {
            $value = '';
        }

        return '<input name="'.$name.'" type="'.$type.'" class="'.$class.'" value="'.$value.'">';
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $options Associative array of 'display' => 'value' pairs
     * @param string $class
     * @return string
     */
    public function radio($name, $value = '', $options, $class = '')
    {
        foreach ($options as $display => $input_value) {
            $radio_selected = ($value === $input_value) ? 'checked="checked"' : '';
            $fields[] = '<label><input type="radio" name="'.$name.'" value="'.$input_value.'" '.$radio_selected.' class="'.$class.'">'.$display.'</label>';
        }

        return implode('&nbsp;&nbsp;&nbsp;', $fields);
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $class
     * @return string
     */
    public function text($name, $value = '', $class = 'input_box')
    {
        return '<textarea name="'.$name.'" rows="5" cols="60" class="'.$class.'">'.htmlspecialchars($value, ENT_QUOTES).'</textarea>';
    }
}
