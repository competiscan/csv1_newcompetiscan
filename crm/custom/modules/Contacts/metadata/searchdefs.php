<?php
//changed by arvind
$searchdefs ['Contacts'] = 
array (
  'layout' => 
  array (
    'basic_search' => 
    array (
      'first_name' => 
      array (
        'name' => 'first_name',
        'label' => 'LBL_FIRST_NAME',
        'default' => true,
      ),
      'last_name' => 
      array (
        'name' => 'last_name',
        'label' => 'LBL_LAST_NAME',
        'default' => true,
      ),
      'department' => 
      array (
        'width' => '10%',
        'label' => 'LBL_DEPARTMENT',
        'default' => true,
        'name' => 'department',
      ),
      'contact_type_m_c' => 
      array (
        'width' => '10%',
        'label' => 'contact_type_m_c',
        'default' => true,
        'name' => 'contact_type_m_c',
      ),
      'current_user_only' => 
      array (
        'name' => 'current_user_only',
        'label' => 'LBL_CURRENT_USER_FILTER',
        'type' => 'bool',
        'default' => true,
      ),
    ),
    
    	
    
  
    'advanced_search' => 
    array (
      'department' => 
      array (
        'name' => 'department',
        'label' => 'LBL_DEPARTMENT',
        'default' => true,
      ),
      'first_name' => 
      array (
        'name' => 'first_name',
        'label' => 'LBL_FIRST_NAME',
        'default' => true,
      ),
     'last_name' => 
      array (
        'name' => 'last_name',
        'label' => 'LBL_LAST_NAME',
        'default' => true,
      ),
      'phone_work' => 
      array (
        'name' => 'phone_work',
        'label' => 'LBL_OFFICE_PHONE',
        'default' => true,
      ),
      'email' => 
      array (
        'name' => 'email',
        'label' => 'LBL_ANY_EMAIL',
        'type' => 'name',
        'default' => true,
        'width' => '10%',
      ),
       'contact_type_m_c' => 
      array (
        'name' => 'contact_type_m_c',
        'label' => 'contact_type_m_c',
        'default' => true,
      ),
    'email_opt_out' => 
      array (
        'name' => 'email_opt_out',
        'label' => 'LBL_EMAIL_OPT_OUT',
        'default' => true,
      ),
  /*   'assigned_user_id' => 
      array (
        'name' => 'assigned_user_id',
        'type' => 'enum',
        'label' => 'LBL_ASSIGNED_TO',
        'function' => 
        array (
          'name' => 'get_user_array',
          'params' => 
          array (
            0 => false,
          ),
        ),
        'default' => true,
      ),
      
      */
   'assigned_user_id'=>  
  array (
    'name' => 'assigned_user_id',
    'type' => 'enum',
    'label' => 'LBL_ASSIGNED_TO',
    'default' => true,

  ),
      
      
      'sent_date_c' => 
      array (
        'name' => 'sent_date_c',
        'label' => 'sent_date_c',
        'default' => true,
      ),
      'ownbiz_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_OWNBIZ',
        'default' => true,
        'name' => 'ownbiz_c',
      ),
      'birthdate' => 
      array (
        'width' => '10%',
        'label' => 'LBL_BIRTHDATE',
        'default' => true,
        'name' => 'birthdate',
      ),
      'address_city' => 
      array (
        'name' => 'address_city',
        'label' => 'LBL_CITY',
        'type' => 'name',
        'default' => true,
        'width' => '10%',
      ),
      'address_state' => 
      array (
        'name' => 'address_state',
        'label' => 'LBL_STATE',
        'type' => 'name',
        'default' => true,
        'width' => '10%',
      ),
      'address_postalcode' => 
      array (
        'name' => 'address_postalcode',
        'label' => 'LBL_POSTAL_CODE',
        'type' => 'name',
        'default' => true,
        'width' => '10%',
      ),
      'hear_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_HEAR',
        'default' => true,
        'name' => 'hear_c',
      ),
      'vision_critical_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_VISION_CRITICAL',
        'default' => true,
        'name' => 'vision_critical_c',
      ),
      'sub_panelist_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_SUB_PANELIST',
        'default' => true,
        'name' => 'sub_panelist_c',
      ),
      'dma_code_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_DMA_CODE',
        'default' => true,
        'name' => 'dma_code_c',
      ),
      'phone_other' => 
      array (
        'width' => '10%',
        'label' => 'LBL_OTHER_PHONE',
        'default' => true,
        'name' => 'phone_other',
      ),
      

    ),
    
  ),
  'templateMeta' => 
  array (
    'maxColumns' => '3',
    'widths' => 
    array (
      'label' => '10',
      'field' => '30',
    ),
  ),
);
?>
