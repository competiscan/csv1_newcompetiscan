<?php

class AdminLog {

    /**
     * Create new product, relies on database connection
     *
     * @param databaseReadWrite $drw Database class
     * @param string $drw_main
     */
    public function __construct(databaseReadWrite $drw, $drw_main)
    {
        $this->db = $drw;
        $this->db_main = $drw_main;
    }

    /**
     * Adds new row to the admin log with information about the last person who edited product information.
     * Note that this will have to be implemented slightly differently for Temp Products.
     *
     * @param int $productID - id of product changed
     * @param int $adminUserID - id of currently logged in user
     * @param int $productStatus - status code corresponding to approved, unapproved, reprocessed, etc
     */
    public function addAdminLogForProduct($productID, $adminUserID, $productStatus)
    {
        $sql = "INSERT IGNORE INTO `cscan_admin_log` SET userID=$adminUserID,logDate=NOW(),productID=$productID,productStatus=$productStatus";
        $this->db->query($sql, $this->db_main);
    }
}