<?php

class Product
{
    private $product;
    private $db;
    private $db_main;
    private $adminLog;

    /**
     * Create new product, relies on database connection
     *
     * @param databaseReadWrite $drw Database class
     * @param string $drw_main
     */
    public function __construct(databaseReadWrite $drw, $drw_main, AdminLog $adminLog)
    {
        $this->db = $drw;
        $this->db_main = $drw_main;
        $this->product = false;
        $this->adminLog = $adminLog;
    }

    /**
     * Load up a product and all its details
     *
     * @param integer $product_id
     * @return object
     */
    public function get_product($product_id)
    {
        $sql = "select * from cscan_product_detail where productId='$product_id'";
        $result = $this->db->query($sql, $this->db_main);

        if ($this->db->num_rows($result)) {
            $row = $this->db->fetch_assoc($result);

            foreach ($row as $key => $value) {
                $this->product->$key = $value;
            }
        }

        $this->product->linked->image = $this->get_image();
        $this->product->linked->company = $this->get_company();
        $this->product->linked->category = $this->get_sector_and_category();

        return $this->product;
    }

    /**
     * Load up a product's image details
     *
     * @return object
     */
    public function get_image()
    {
        $sql = "select * from cscan_img where productID='".$this->product->productID."'";
        $result = $this->db->query($sql, $this->db_main);

        if ($this->db->num_rows($result)) {
            $row = $this->db->fetch_assoc($result);

            foreach ($row as $key => $value) {
                $image->$key = $value;
            }

            return $image;
        }
    }

    /**
     * Load related company details against product
     *
     * @return array
     */
    public function get_company()
    {
        $sql = "select * from cscan_company_product where productID='".$this->product->productID."'";
        $result = $this->db->query($sql, $this->db_main);

        if ($this->db->num_rows($result) > 0) {
            while ($row = $this->db->fetch_assoc($result)) {
                foreach ($row as $key => $value) {
                    $linked_company[$row['companyID']]->$key = $value;
                }
            }

            return $linked_company;
        }
    }

    /**
     * Load related sector and category details against product
     *
     * @return array
     */
    public function get_sector_and_category()
    {
        $sql = "select * from cscan_scsc_product where productID='".$this->product->productID."'";
        $result = $this->db->query($sql, $this->db_main);

        if ($this->db->num_rows($result) > 0) {
            while ($row = $this->db->fetch_assoc($result)) {
                foreach ($row as $key => $value) {
                    $linked_category[$row['scsc_sectorID']]->$key = $value;
                }
            }

            return $linked_category;
        }
    }

    /**
     * Set admin_userID, which is displayed to the user as "Last User", the last person to touch that product.
     * @param $adminUserID - currently logged in user
     */
    public function set_admin_userID($adminUserID)
    {
        $this->product->admin_userID = $adminUserID;
    }

    /**
     * Run through all object variables and save accordingly back to database
     *
     * @return void
     */
    public function save()
    {
        global $AUTH_DATA;
        $currentUser = $AUTH_DATA['userID'];
        $this->set_admin_userID($currentUser);
        $this->adminLog->addAdminLogForProduct($this->product->productID, $currentUser, $this->product->productStatus);

        foreach ($this->product as $column => $value) {
            if (!is_object($value)) {
                $update_column[] = "$column='".$this->db->real_escape_string($value)."'";
            }
        }

        $sql = 'update cscan_product_detail set ';
        $sql .= implode(', ', $update_column);
        $sql .= " where productId='".$this->product->productID."'";
        $this->db->query($sql, $this->db_main);     
    }
    
    #################### for track mass update data ##############
    public function trackmassupdate()
    {
        global $AUTH_DATA;
        $currentUser = $AUTH_DATA['userID'];
        $this->set_admin_userID($currentUser);
        foreach ($this->product as $column => $value) {
            if (!is_object($value)) {
                $update_column[] = "$column='".$this->db->real_escape_string($value)."'";
            }
        }

        $sql = 'update cscan_product_detail set ';
        $sql .= implode(', ', $update_column);
        $sql .= " where productId='".$this->product->productID."'";
        return $sql;
        exit;
        
        
    }
    #################### end for track mass update data ##############
    
    
    
    /**
     * Copy across image from another product
     *
     * @param object $image_details product with image upload details
     * @param boolean $from_company if we are instead copying image from company
     */
    public function copy_image($image_details, $from_company = false)
    {
        $root_path = $_SERVER['DOCUMENT_ROOT'];

        if ($from_company) {
            foreach ($image_details as $key => $value) {
                $product_image_key = str_replace('co_', '', $key);
                $image_details->$product_image_key = $value;
            }

            $img_filename = str_replace($image_details->companyID, $this->product->productID, $image_details->img_filename);
            $img_path = str_replace('coImages', 'productImages', $image_details->img_path).$this->product->productID.'/';
            $image_details->img_companyID = $image_details->companyID;
            $image_details->img_createddate = $image_details->img_co_createddate;
            $image_details->img_content_type = $image_details->img_co_content_type;
            $image_details->img_size_byte = $image_details->img_co_size_byte;
            $image_details->img_id = 1; // This seems to be the default in cscan_img table
            $image_details->img_createdby = 0; // There is creator ID showing for company images
        } else {
            $img_filename = str_replace($image_details->productID, $this->product->productID, $image_details->img_filename);
            $img_path = str_replace($image_details->productID, $this->product->productID, $image_details->img_path);
        }

        $sql = "delete from cscan_img where productId='".$this->product->productID."'";
        $this->db->query($sql, $this->db_main);

        if (!is_dir($root_path.$img_path)) {
            mkdir($root_path.$img_path, 02755, true);
        }

        if (is_file($root_path.$image_details->img_path.$image_details->img_filename) &&
            copy($root_path.$image_details->img_path.$image_details->img_filename, $root_path.$img_path.$img_filename)) {

            $sql = "insert into cscan_img (productID, img_id, img_filename, img_createddate, img_createdby, img_content_type, img_size_byte, img_path, img_companyID)
                values ('".$this->product->productID."', '".$image_details->img_id."', '".$img_filename."', '".$image_details->img_createddate."', '".$image_details->img_createdby."',
                    '".$image_details->img_content_type."', '".$image_details->img_size_byte."', '".$img_path."', '".$image_details->img_companyID."')";
            $this->db->query($sql, $this->db_main);

            $this->product->linked->image = $this->get_image();
        }
    }

    /**
     * Update the relationship between product and sector/category
     *
     * @param array $sector_category_ids
     */
    public function update_sectors_and_categories($sector_category_ids)
    {
        $sql = "delete from cscan_scsc_product where productID='".$this->product->productID."'";
        $this->db->query($sql, $this->db_main);

        for ($i = 0, $n = count($sector_category_ids); $i < $n; $i++) {
            $all_sector_category_ids['sector'][] = $sector_category_ids[$i]['sector'];
            $all_sector_category_ids['category'][] = $sector_category_ids[$i]['category'];
            $all_sector_category_ids['subcategory'][] = $sector_category_ids[$i]['subcategory'];
            $all_sector_category_ids['subsubcategory'][] = $sector_category_ids[$i]['subsubcategory'];
        }

        for ($i = 0, $n = count($all_sector_category_ids['sector']); $i < $n; $i++) {
            $sort_order = $i + 1;
            $sql_update[] = '('.$this->product->productID.', '.$all_sector_category_ids['sector'][$i].', '.$all_sector_category_ids['category'][$i].',
                '.$all_sector_category_ids['subcategory'][$i].', '.$all_sector_category_ids['subsubcategory'][$i].', '.$sort_order.')';
        }

        $sql = "insert into cscan_scsc_product (productID, scsc_sectorID, scsc_categoryID, scsc_subCategoryID, scsc_subSubCategoryID, scsc_sort) values ".implode(',', $sql_update);
        $this->db->query($sql, $this->db_main);
        $this->product->linked->category = $this->get_sector_and_category();

        $this->product->sectorID = implode(',', $all_sector_category_ids['sector']);
        $this->product->categoryID = implode(',', $all_sector_category_ids['category']);
        $this->product->subCategoryID = implode(',', $all_sector_category_ids['subcategory']);
        $this->product->subSubCategoryID = implode(',', $all_sector_category_ids['subsubcategory']);
        $this->save();
    }

    /**
     * Update the relationship between product and companies, update main product table as well
     *
     * @param array $company_ids
     * @param array $companies list of companies
     */
    public function update_companies($company_ids, $companies)
    {
        $sql = "delete from cscan_company_product where productId=".$this->product->productID."";
        $this->db->query($sql, $this->db_main);

        for ($i = 0, $n = count($company_ids); $i < $n; $i++) {
            $sort_order = $i + 1;
            $sql_update[] = "(".$company_ids[$i].", ".$this->product->productID.", $sort_order)";

            $company_name = $companies[$company_ids[$i]]->companyName;

            if ($i == 0) {
                $this->product->company = $company_name;
            } else {
                $secondary_companies[] = $company_name;
            }
        }

        $sql = "insert into cscan_company_product (companyID, productID, primary_co) values ".implode(',', $sql_update);
        $this->db->query($sql, $this->db_main);
        $this->product->linked->company = $this->get_company();

        if (isset($secondary_companies)) {
            $this->product->secondCompany = implode('; ', $secondary_companies);
        }

        $this->save();
    }
}
