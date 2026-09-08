<?php

namespace HS;

class Mintel
{
    /**
     * Input fields and their type
     *
     * @var array
     */
    protected $mintel_fields = array(
        'incentive_type' => array('display' => 'Sign-on Incentive Type #1', 'type' => 'dropdown'),
        'incentive_value' => array('display' => 'Sign-on Incentive Value #1', 'type' => 'integer'),
        'accelerator_per' => array('display' => 'Sign-on Accelerator Per #1', 'type' => 'integer'),
        'accelerator_type' => array('display' => 'Sign-on Accelerator Type #1', 'type' => 'dropdown'),
        'max_award' => array('display' => 'Sign-on Max award #1', 'type' => 'integer'),
        'max_spend' => array('display' => 'Sign-on Incentive Maximum Spend #1', 'type' => 'integer'),
        'min_spend' => array('display' => 'Sign-on Incentive Minimum Spend #1', 'type' => 'integer'),
        'window' => array('display' => 'Sign-on Incentive Window (months) #1', 'type' => 'integer'),
        'category_limited' => array('display' => 'Sign-on Limited to Specific Category #1', 'type' => 'boolean'),
        'window_fixed_date' => array('display' => 'Sign-on Fixed date #1', 'type' => 'boolean'),
    );

    /**
     * Input fields and their type
     *
     * @var array
     */
    protected $incentive_set_2 = array(
        'incentive_signon_2' => array('display' => 'Sign-on Incentive #2', 'type' => 'text'),
        'incentive_type_2' => array('display' => 'Sign-on Incentive Type #2', 'type' => 'dropdown'),
        'incentive_value_2' => array('display' => 'Sign-on Incentive Value #2', 'type' => 'integer'),
        'accelerator_per_2' => array('display' => 'Sign-on Accelerator Per #2', 'type' => 'integer'),
        'accelerator_type_2' => array('display' => 'Sign-on Accelerator Type #2', 'type' => 'dropdown'),
        'max_award_2' => array('display' => 'Sign-on Max award #2', 'type' => 'integer'),
        'max_spend_2' => array('display' => 'Sign-on Incentive Maximum Spend #2', 'type' => 'integer'),
        'min_spend_2' => array('display' => 'Sign-on Incentive Minimum Spend #2', 'type' => 'integer'),
        'window_2' => array('display' => 'Sign-on Incentive Window (months) #2', 'type' => 'integer'),
        'category_limited_2' => array('display' => 'Sign-on Limited to Specific Category #2', 'type' => 'boolean'),
        'window_fixed_date_2' => array('display' => 'Sign-on Fixed date #2', 'type' => 'boolean'),
    );

    /**
     * Input fields and their type
     *
     * @var array
     */
    protected $incentive_set_3 = array(
        'incentive_signon_3' => array('display' => 'Sign-on Incentive #3', 'type' => 'text'),
        'incentive_type_3' => array('display' => 'Sign-on Incentive Type #3', 'type' => 'dropdown'),
        'incentive_value_3' => array('display' => 'Sign-on Incentive Value #3', 'type' => 'integer'),
        'accelerator_per_3' => array('display' => 'Sign-on Accelerator Per #3', 'type' => 'integer'),
        'accelerator_type_3' => array('display' => 'Sign-on Accelerator Type #3', 'type' => 'dropdown'),
        'max_award_3' => array('display' => 'Sign-on Max award #3', 'type' => 'integer'),
        'max_spend_3' => array('display' => 'Sign-on Incentive Maximum Spend #3', 'type' => 'integer'),
        'min_spend_3' => array('display' => 'Sign-on Incentive Minimum Spend #3', 'type' => 'integer'),
        'window_3' => array('display' => 'Sign-on Incentive Window (months) #3', 'type' => 'integer'),
        'category_limited_3' => array('display' => 'Sign-on Limited to Specific Category #3', 'type' => 'boolean'),
        'window_fixed_date_3' => array('display' => 'Sign-on Fixed date #3', 'type' => 'boolean'),
    );

    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->mintel_fields;
    }

    /**
     * @param string $field_set Set of incentive fields
     * @return array
     */
    public function getFieldSet($field_set = 'incentive_set_2')
    {
        return $this->{$field_set};
    }

    /**
     * @return array
     */
    public function getDropdown($name)
    {
        $options = array();

        switch ($name) {
            case 'accelerator_type':
            case 'accelerator_type_2':
            case 'accelerator_type_3':
                $options = array(
                    'Multiplier' => 'Multiplier',
                    'Percentage' => 'Percentage',
                );
                break;
            case 'incentive_type':
            case 'incentive_type_2':
            case 'incentive_type_3':
                $options = array(
                    'Cash Back - Fixed' => 'Cash Back - Fixed',
                    'Cash Back - Multiplier' => 'Cash Back - Multiplier',
                    'Points - Fixed' => 'Points - Fixed',
                    'Points - Multiplier' => 'Points - Multiplier',
                    'Miles - Fixed' => 'Miles - Fixed',
                    'Miles - Multiplier' => 'Miles - Multiplier',
                    'Merchandise' => 'Merchandise',
                    'Gift Card' => 'Gift Card',
                    'Certificate' => 'Certificate',
                    'Coupon/Discount' => 'Coupon/Discount',
                    'Fee Waiver/Fee Reduction' => 'Fee Waiver/Fee Reduction',
                    'Status/Early Access' => 'Status/Early Access',
                    'Sweepstakes Entry' => 'Sweepstakes Entry',
                );
                break;
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getYesNo()
    {
        return array(
            'Yes' => 1,
            'No' => 0,
        );
    }
}
