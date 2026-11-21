<?php
class FormBuilder {
    const NUMERIC='NumericField';
    const DROPDOWN='DropdownField';
    private $fields;
    function __construct($values = array()) {

        $this->values = $values;
    }
    function addField($type, $label, $name, $options = array()) {



        $value = isset($this->values[$name]) ? $this->values[$name] : null;
        $this->fields[$name] = new $type($name, $label, $value, $options);

    }

    function render () {

        $fields = '';
        foreach ($this->fields as $f) {
            $fields.=$f->render();
        }
        $html =<<< EOH

        <table width="100%">
        $fields
        </table>

EOH;

        return $html;

    }
}