<?php
require_once('includes/globalSession.php');
// allow to display on home page with no login
// if(!isset($_SESSION['public_admin_access'])){
//     require_once('includes/checklogin.php');
// }
require_once 'HTTP/Download.php';

$productID    = isset($_REQUEST['id'])  ? (int)$_REQUEST['id']  : 0;
$img_companyID = isset($_REQUEST['cid']) ? (int)$_REQUEST['cid'] : 0;
$iid          = isset($_REQUEST['iid']) ? (int)$_REQUEST['iid'] : 1;

// FIX: Validate that $s3 and $bucket_name are available (should be set in globalSession.php)
if (!isset($s3) || !isset($bucket_name)) {
    error_log('getimage.php: $s3 or $bucket_name not initialized');
    serveAltImage();
    exit;
}

$isS3        = false;
$s3Keyname   = '';
$img_id      = 0;
$img_filename     = '';
$img_content_type = '';
$img_size_byte    = 0;
$img_createddate  = 0;
$img_path         = '';

if ($productID !== 0 || $img_companyID !== 0) {

    // --- Product image lookup ---
    if ($img_companyID === 0) {
        $query2 = "SELECT img_id, img_filename, img_content_type, img_size_byte,
                          UNIX_TIMESTAMP(img_createddate), img_path, img_companyID
                   FROM cscan_img
                   WHERE productID = $productID AND img_id = $iid";
        $query_result2 = $DRW->query($query2, $DRW_read);
        $data2         = $DRW->fetch_row($query_result2);
        $img_id        = (float)$data2[0];

        if (!empty($img_id)) {
            $img_filename     = $data2[1];
            $img_content_type = $data2[2];
            $img_size_byte    = $data2[3];
            $img_createddate  = $data2[4];
            $img_path         = $data2[5];
            $img_companyID    = $data2[6];  // may still be needed below
            $DRW->free_result($query_result2);

            // FIX: Only set S3 key here; do NOT let the company block overwrite it
            $s3Keyname = strstr($img_path, 'productImages') . $img_filename;
            $isS3      = true;
        } else {
            // No product image — fall back to the primary company logo
            $DRW->free_result($query_result2);
            $query2        = "SELECT companyID FROM cscan_company_product
                              WHERE productID = $productID AND primary_co = 1";
            $query_result2 = $DRW->query($query2, $DRW_read);
            $data2         = $DRW->fetch_row($query_result2);
            $img_companyID = (int)$data2[0];
            $DRW->free_result($query_result2);
        }
    }

    // --- Company logo lookup (only when no product image was resolved) ---
    // FIX: Guard with !$isS3 so a found product image is never overwritten
    if ($img_companyID !== 0 && !$isS3) {
        $query2 = "SELECT img_co_content_type, img_co_size_byte,
                          UNIX_TIMESTAMP(img_co_createddate), img_co_path, img_co_filename
                   FROM cscan_img_company
                   WHERE companyID = $img_companyID";
        $query_result2    = $DRW->query($query2, $DRW_read);
        $data2            = $DRW->fetch_row($query_result2);
        $img_content_type = $data2[0];
        $img_size_byte    = $data2[1];
        $img_createddate  = $data2[2];
        $img_path         = $data2[3];
        $img_filename     = $data2[4];
        $DRW->free_result($query_result2);

        $s3Keyname = strstr($img_path, 'coImages') . $img_filename;
        $isS3      = true;
    }

    $src = dirname(__FILE__) . $img_path . $img_filename;

    // --- Serve from S3 ---
    if ($img_filename !== '' && $isS3) {
        // FIX: Wrap the S3 call in try/catch — SDK throws on missing key, not empty result
        try {
            $results = $s3->getObject([
                'Bucket' => $bucket_name,
                'Key'    => $s3Keyname,
            ]);

	    if (!empty($results)) {
		    echo "<pre>";
		    print_r($results);
		    echo "</pre>"; die;
                header('Content-Type: ' . $results['ContentType']);
                echo $results['Body'];
                exit;
            }
        } catch (\Aws\Exception\AwsException $e) {
            error_log('getimage.php S3 error: ' . $e->getMessage());
        }

        // S3 fetch failed — fall through to alt image

    // --- Serve from local filesystem ---
    } elseif ($img_filename !== '' && is_file($src)) {
        // FIX: Clean ob_end_clean error suppression — check first
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $dl = new HTTP_Download();
        $dl->setFile($src);
        $dl->setLastModified($img_createddate);
        $dl->setContentType($img_content_type);
        $dl->setCacheControl('public');
        $dl->setCache(true);
        $dl->setContentDisposition(HTTP_DOWNLOAD_INLINE, $img_filename);
        $dl->send();
        exit;
    }
}

// --- Fallback: serve placeholder image ---
serveAltImage();

/**
 * Sends the "not available" placeholder GIF and exits.
 */
function serveAltImage(): void
{
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    $altfile = dirname(__FILE__) . '/images/thumbNA.gif';
    makeCacheable(filemtime($altfile));
    header('Content-Type: image/gif');
    readfile($altfile);
    exit;
}
