<?php

namespace HS;

class Export
{
    /**
     * Columns which should be converted into Yes/No format
     *
     * @var array
     */
    protected $yes_no = array(
        'category_limited',
        'window_fixed_date',
        'category_limited_2',
        'window_fixed_date_2',
        'category_limited_3',
        'window_fixed_date_3',
    );

    /**
     * Sectors from cscan_sector table mapped to row id
     *
     * @var array
     */
    protected $sectors = array(
        'Banking' => 87,
        'Credit Cards' => 90,
    );

    public function __construct()
    {
    }

    /**
     * Incentive fields should only be visible when the sectors
     * banking or credit card are included in the record.
     *
     * @param array $sector Sectors selected in the search results
     * @return bool
     */
    public function showIncentiveFields($sector)
    {
        return (in_array($this->sectors['Banking'], $sector) || in_array($this->sectors['Credit Cards'], $sector));
    }

    /**
     * Convert fieldset into header appropriate array
     *
     * @param array $fields
     * @return array
     */
    public function convertToHeaders($fields)
    {
        foreach ($fields as $field_name => $field_spec) {
            $headers[$field_name] = $field_spec['display'];
        }

        return $headers;
    }

    /**
     * Convert certain values into Yes/No instead of showing as boolean or 1 or 0
     *
     * @param string $fieldname
     * @param string $value
     * @return string
     */
    public function convertToYesNo($fieldname, $value)
    {
        if (in_array($fieldname, $this->yes_no)) {
            return $value ? 'Yes' : 'No';
        } else {
            return $value;
        }
    }
}
