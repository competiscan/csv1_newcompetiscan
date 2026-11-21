<?php

class Document
{
    /**
     * @param databaseReadWrite $drw Database class
     * @param string $drw_main
     */
    public function __construct(databaseReadWrite $drw, $drw_main)
    {
        $this->db = $drw;
        $this->db_main = $drw_main;
        $this->document = false;
    }

    /**
     * Load up document for a given product
     *
     * @param integer $product_id
     * @param boolean $original look in the original document table or not
     * @return object|boolean false if there is no document
     */
    public function get_document($product_id, $original = false)
    {
        $document_table = ($original) ? 'cscan_document_orig' : 'cscan_document';
        $sql = "select * from $document_table where productID='$product_id' limit 1";
        $result = $this->db->query($sql, $this->db_main);

        if ($this->db->num_rows($result)) {
            $row = $this->db->fetch_assoc($result);

            foreach ($row as $key => $value) {
                $this->document->$key = $value;
            }

            return $this->document;
        } else {
            return false;
        }
    }

    /**
     * Default document that can be used to copy to other products
     *
     * @return object|boolean
     */
    public function get_default($isPDF = true)
    {
        $fileExt = $isPDF ? '.pdf' : '.jpg';
        $default_doc = new stdClass();
        $default_doc->document_id = 1;
        $default_doc->document_path = '/assets/';
        $default_doc->document_filename = 'Competiscan_default'.$fileExt;
        $default_doc->document_createdby = '0';
        $default_doc->document_placement = '';
        $default_doc = $this->retrieve_file_info($default_doc);

        return $default_doc;
    }

    /**
     * Load document object with relevant file information
     *
     * @param object $document should contain at least document_path and document_filename
     * @return object|boolean false if there is no document in the filesystem
     */
    public function retrieve_file_info($document) {
        $file_location = $_SERVER['DOCUMENT_ROOT'].$document->document_path.$document->document_filename;
        if (!file_exists($file_location)) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $document->document_content_type = finfo_file($finfo, $file_location);
        $document->document_createddate = date("Y-m-d H:i:s", filemtime($file_location));
        $document->document_size_byte = filesize($file_location);
        finfo_close($finfo);

        return $document;
    }

    /**
     * Save document details for a given product
     *
     * @param object $document details of document, including product ID
     * @param boolean $original look in the original document table or not
     */
    public function set_document($document, $original = false)
    {
        $document_table = ($original) ? 'cscan_document_orig' : 'cscan_document';
        $sql = "insert into $document_table
            (productID, document_id, document_filename, document_createddate, document_createdby, document_content_type, document_size_byte, document_path, document_placement)
            values
            ('$document->productID', '$document->document_id', '$document->document_filename', '$document->document_createddate', '$document->document_createdby', '$document->document_content_type', '$document->document_size_byte', '$document->document_path', '$document->document_placement')
            on duplicate key update
            productID='$document->productID', document_id='$document->document_id', document_filename='$document->document_filename', document_createddate='$document->document_createddate',
             document_createdby='$document->document_createdby', document_content_type='$document->document_content_type', document_size_byte='$document->document_size_byte', document_path='$document->document_path', document_placement='$document->document_placement'";

        $this->db->query($sql, $this->db_main);
    }

    /**
     * Save image document details for a given product
     *
     * @param object $document details of document, including product ID
     */
    public function set_image_document($document) {
        $img_document_sort_ins = 1;
        $img_document_default = 1;

        $sql = "REPLACE INTO cscan_img_document
            (productID, document_id, img_document_sort, img_document_filename, img_document_createddate,img_document_content_type, img_document_size_byte, img_document_createdby, img_document_default, img_document_path)
            VALUES
            ('$document->productID', '$document->document_id', '$img_document_sort_ins', '$document->document_filename', NOW(), 'image/jpeg', '$document->document_size_byte', '$document->document_createdby', '$img_document_default', '$document->document_path')";

        $this->db->query($sql, $this->db_main);
    }
}
