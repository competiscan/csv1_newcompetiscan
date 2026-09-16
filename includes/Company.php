<?php

class Company
{
    /**
     * @param databaseReadWrite $drw Database class
     * @param string $drw_main
     */
    public function __construct(databaseReadWrite $drw, $drw_main)
    {
        $this->db = $drw;
        $this->db_main = $drw_main;
        $this->company = false;
    }

    /**
     * Load up a single company
     *
     * @param integer $id company id
     * @return object
     */
    public function get_company($id)
    {
        $sql = "select * from cscan_company where companyID='$id'";
        $result = $this->db->query($sql, $this->db_main);

        if ($this->db->num_rows($result)) {
            $row = $this->db->fetch_assoc($result);

            foreach ($row as $key => $value) {
                $this->company->$key = $value;
            }
        }

        return $this->company;
    }

    /**
     * Fetch all companies and create an array of them, using company id as index key
     *
     * @return array collection of all company items
     */
    public function get_all()
    {
        $all_companies = array();
        $sql = "select * from cscan_company";
        $result = $this->db->query($sql, $this->db_main);

        if ($this->db->num_rows($result)) {
            while ($row = $this->db->fetch_assoc($result)) {
                foreach ($row as $key => $value) {
                    $all_companies[$row['companyID']]->$key = $value;
                }
            }
        }

        return $all_companies;
    }

    /**
     * Load up a related company image
     *
     * @param integer $id company id
     * @return object
     */
    public function get_image($id)
    {
        $image = null;
        $sql = "select * from cscan_img_company where companyID='$id'";
        $result = $this->db->query($sql, $this->db_main);

        if ($this->db->num_rows($result)) {
            $row = $this->db->fetch_assoc($result);

            foreach ($row as $key => $value) {
                $image->$key = $value;
            }
        }

        return $image;
    }

    /**
     * Copy image from product
     *
     * @param object $image_details product image details
     */
    public function copy_product_image($image_details)
    {
        $root_path = $_SERVER['DOCUMENT_ROOT'];
        $image_filename = str_replace('thumb'.$image_details->productID, $this->company->companyID, $image_details->img_filename);
        $image_path = str_replace('productImages', 'coImages', $image_details->img_path);
        $image_path = str_replace($image_details->productID.'/', '', $image_path);

        $sql = "delete from cscan_img_company where companyID='".$this->company->companyID."'";
        $this->db->query($sql, $this->db_main);

        if (!is_dir($root_path.$image_path)) {
            mkdir($root_path.$image_path, 02755);
        }

        if (is_file($root_path.$image_details->img_path.$image_details->img_filename) &&
            copy($root_path.$image_details->img_path.$image_details->img_filename, $root_path.$image_path.$image_filename)) {

            $sql = "insert into cscan_img_company (companyID, img_co_createddate, img_co_content_type, img_co_size_byte, img_co_path, img_co_filename)
                values ('".$this->company->companyID."', '".$image_details->createddate."', '".$image_details->img_content_type."', '".$image_details->img_size_byte."',
                 '".$image_path."', '".$image_filename."')";
            $this->db->query($sql, $this->db_main);
        }
    }

}
