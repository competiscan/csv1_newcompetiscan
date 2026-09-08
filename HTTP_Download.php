<?php

/**
 * HTTP_Download — PHP 8.2 Compatible Replacement
 *
 * Drop-in replacement for PEAR's HTTP/Download.php
 * Fixes: "syntax error, unexpected token 'new'" and all other PHP 5.x incompatibilities.
 *
 * Usage:
 *   require_once 'HTTP/Download.php';
 *   $dl = new HTTP_Download();
 *   $dl->setFile('/path/to/file.pdf');
 *   $dl->send();
 *
 * Or static:
 *   HTTP_Download::staticSend(['file' => '/path/to/file.pdf', 'filename' => 'report.pdf']);
 */

// ---------------------------------------------------------------------------
// Constants (mirrors original PEAR HTTP_Download constants)
// ---------------------------------------------------------------------------
define('HTTP_DOWNLOAD_ATTACHMENT', 'attachment');
define('HTTP_DOWNLOAD_INLINE',     'inline');

define('HTTP_DOWNLOAD_DATA',   'data');
define('HTTP_DOWNLOAD_FILE',   'file');
define('HTTP_DOWNLOAD_STREAM', 'stream');

// ---------------------------------------------------------------------------
// Minimal PEAR error shim — stops "Class PEAR not found" errors
// ---------------------------------------------------------------------------
if (!class_exists('PEAR_Error')) {
    class PEAR_Error
    {
        public string $message;
        public int    $code;

        public function __construct(string $message = '', int $code = 0)
        {
            $this->message = $message;
            $this->code    = $code;
        }

        public function getMessage(): string { return $this->message; }
        public function getCode(): int       { return $this->code;    }
    }
}

if (!class_exists('PEAR')) {
    class PEAR
    {
        public static function isError(mixed $data): bool
        {
            return $data instanceof PEAR_Error;
        }

        public static function raiseError(string $message = '', int $code = 0): PEAR_Error
        {
            return new PEAR_Error($message, $code);
        }
    }
}

// ---------------------------------------------------------------------------
// Main class
// ---------------------------------------------------------------------------
class HTTP_Download
{
    // -----------------------------------------------------------------------
    // Properties
    // -----------------------------------------------------------------------
    private string  $file        = '';
    private string  $data        = '';
    private mixed   $stream      = null;
    private string  $contentType = 'application/octet-stream';
    private string  $filename    = '';
    private string  $disposition = HTTP_DOWNLOAD_ATTACHMENT;
    private int     $bufferSize  = 8192;
    private bool    $gzip        = false;
    private bool    $cache       = true;
    private ?int    $lastModified = null;
    private string  $etag        = '';
    private ?int    $contentLength = null;
    private array   $headers     = [];
    private string  $sourceType  = HTTP_DOWNLOAD_FILE;

    // -----------------------------------------------------------------------
    // Constructor  (PHP 8.x style — was the cause of "unexpected token 'new'")
    // -----------------------------------------------------------------------
    public function __construct(array $params = [])
    {
        if (!empty($params)) {
            $this->setParams($params);
        }
    }

    // -----------------------------------------------------------------------
    // Static send — mirrors HTTP_Download::staticSend($params, $guess)
    // -----------------------------------------------------------------------
    public static function staticSend(array $params, bool $guess = false): true|PEAR_Error
    {
        $dl = new self($params);
        return $dl->send($guess);
    }

    // -----------------------------------------------------------------------
    // Setters
    // -----------------------------------------------------------------------

    /** Set all params at once from an associative array */
    public function setParams(array $params): void
    {
        foreach ($params as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /** Path to the file to send */
    public function setFile(string $file): true|PEAR_Error
    {
        if (!file_exists($file)) {
            return PEAR::raiseError("File not found: $file", 404);
        }
        if (!is_readable($file)) {
            return PEAR::raiseError("File not readable: $file", 403);
        }
        $this->file       = $file;
        $this->sourceType = HTTP_DOWNLOAD_FILE;

        // Auto-detect content type if not already set
        if ($this->contentType === 'application/octet-stream') {
            $this->contentType = $this->detectMime($file);
        }

        // Auto filename
        if ($this->filename === '') {
            $this->filename = basename($file);
        }

        return true;
    }

    /** Raw string data to send instead of a file */
    public function setData(string $data): void
    {
        $this->data       = $data;
        $this->sourceType = HTTP_DOWNLOAD_DATA;
    }

    /** Open resource/stream to send */
    public function setStream(mixed $stream): true|PEAR_Error
    {
        if (!is_resource($stream)) {
            return PEAR::raiseError('Not a valid stream resource.', 400);
        }
        $this->stream     = $stream;
        $this->sourceType = HTTP_DOWNLOAD_STREAM;
        return true;
    }

    /** MIME content type */
    public function setContentType(string $type): void
    {
        $this->contentType = $type;
    }

    /** Download filename presented to the browser */
    public function setFilename(string $name): void
    {
        $this->filename = $name;
    }

    /**
     * Content-Disposition: 'attachment' (download) or 'inline' (display in browser)
     * Accepts HTTP_DOWNLOAD_ATTACHMENT or HTTP_DOWNLOAD_INLINE constants.
     */
    public function setContentDisposition(string $disposition = HTTP_DOWNLOAD_ATTACHMENT, string $filename = ''): void
    {
        $this->disposition = $disposition;
        if ($filename !== '') {
            $this->filename = $filename;
        }
    }

    /** Buffer size in bytes for streaming (default 8 KB) */
    public function setBufferSize(int $bytes): void
    {
        $this->bufferSize = max(512, $bytes);
    }

    /** Whether to send cache headers (ETag / Last-Modified) */
    public function setCache(bool $cache): void
    {
        $this->cache = $cache;
    }

    /** Manually set Last-Modified timestamp */
    public function setLastModified(int $timestamp): void
    {
        $this->lastModified = $timestamp;
    }

    /** Manually set ETag */
    public function setETag(string $etag): void
    {
        $this->etag = $etag;
    }

    /** Enable gzip output (requires zlib) */
    public function setGzip(bool $gzip): void
    {
        $this->gzip = $gzip && extension_loaded('zlib');
    }

    /** Set an arbitrary response header */
    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    /** Set Cache-Control header */
    public function setCacheControl(string $cacheControl): void
    {
        $this->setHeader('Cache-Control', $cacheControl);
    }

    // -----------------------------------------------------------------------
    // Getters
    // -----------------------------------------------------------------------
    public function getContentType(): string  { return $this->contentType; }
    public function getFilename(): string     { return $this->filename;    }
    public function getDisposition(): string  { return $this->disposition; }
    public function getBufferSize(): int      { return $this->bufferSize;  }

    // -----------------------------------------------------------------------
    // Send
    // -----------------------------------------------------------------------

    /**
     * Send the file/data to the client.
     *
     * @param bool $guessContentType  Auto-detect MIME from file extension
     */
    public function send(bool $guessContentType = false): true|PEAR_Error
    {
        // Validate we have something to send
        if ($this->sourceType === HTTP_DOWNLOAD_FILE && $this->file === '') {
            return PEAR::raiseError('No file specified.', 400);
        }

        // Auto-detect MIME
        if ($guessContentType && $this->file !== '') {
            $this->contentType = $this->detectMime($this->file);
        }

        // Resolve content length
        $contentLength = $this->resolveContentLength();

        // Clean any prior output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Handle conditional GET (304 Not Modified)
        if ($this->cache && $this->isNotModified()) {
            http_response_code(304);
            return true;
        }

        // Resolve range
        [$start, $end, $isRange] = $this->resolveRange($contentLength);

        // --- Send headers ---
        $this->sendHeaders($start, $end, $contentLength, $isRange);

        // --- Send body ---
        $result = match ($this->sourceType) {
            HTTP_DOWNLOAD_FILE   => $this->sendFile($start, $end),
            HTTP_DOWNLOAD_DATA   => $this->sendData($start, $end),
            HTTP_DOWNLOAD_STREAM => $this->sendStream($start, $end),
            default              => PEAR::raiseError('Unknown source type.', 500),
        };

        return $result;
    }

    // -----------------------------------------------------------------------
    // Internal helpers
    // -----------------------------------------------------------------------

    private function resolveContentLength(): int
    {
        if ($this->contentLength !== null) {
            return $this->contentLength;
        }
        return match ($this->sourceType) {
            HTTP_DOWNLOAD_FILE   => filesize($this->file),
            HTTP_DOWNLOAD_DATA   => strlen($this->data),
            HTTP_DOWNLOAD_STREAM => -1,   // unknown for streams
            default              => 0,
        };
    }

    /** Returns [start, end, isRange] */
    private function resolveRange(int $totalSize): array
    {
        if ($totalSize < 0 || !isset($_SERVER['HTTP_RANGE'])) {
            return [0, max(0, $totalSize - 1), false];
        }

        preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $m);
        $start = isset($m[1]) && $m[1] !== '' ? (int) $m[1] : 0;
        $end   = isset($m[2]) && $m[2] !== '' ? (int) $m[2] : $totalSize - 1;

        $end = min($end, $totalSize - 1);
        return [$start, $end, true];
    }

    private function sendHeaders(int $start, int $end, int $totalSize, bool $isRange): void
    {
        // Custom headers first
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        header('Content-Type: ' . $this->contentType);

        $fname = addslashes($this->filename);
        header("Content-Disposition: {$this->disposition}; filename=\"$fname\"");

        header('Accept-Ranges: bytes');

        if ($totalSize >= 0) {
            $sendLength = $isRange ? ($end - $start + 1) : $totalSize;
            header('Content-Length: ' . $sendLength);
        }

        if ($isRange && $totalSize >= 0) {
            http_response_code(206);
            header("Content-Range: bytes $start-$end/$totalSize");
        } else {
            http_response_code(200);
        }

        if ($this->cache) {
            $lastMod = $this->lastModified
                ?? ($this->file !== '' ? filemtime($this->file) : time());

            $etag = $this->etag !== ''
                ? $this->etag
                : md5($this->file . $lastMod . $totalSize);

            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastMod) . ' GMT');
            header('ETag: "' . $etag . '"');
            header('Cache-Control: private, must-revalidate');
            header('Pragma: public');
        } else {
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        }

        if ($this->gzip) {
            header('Content-Encoding: gzip');
        }
    }

    private function sendFile(int $start, int $end): true|PEAR_Error
    {
        $fp = @fopen($this->file, 'rb');
        if ($fp === false) {
            return PEAR::raiseError("Cannot open file: {$this->file}", 500);
        }

        if ($start > 0) {
            fseek($fp, $start);
        }

        $remaining = $end - $start + 1;
        while (!feof($fp) && $remaining > 0) {
            $chunk = min($this->bufferSize, $remaining);
            $data  = fread($fp, $chunk);
            if ($this->gzip) {
                echo gzencode($data);
            } else {
                echo $data;
            }
            $remaining -= strlen($data);
            flush();
        }

        fclose($fp);
        return true;
    }

    private function sendData(int $start, int $end): true
    {
        $slice = substr($this->data, $start, $end - $start + 1);
        echo $this->gzip ? gzencode($slice) : $slice;
        flush();
        return true;
    }

    private function sendStream(int $start, int $end): true|PEAR_Error
    {
        if (!is_resource($this->stream)) {
            return PEAR::raiseError('Stream resource is invalid.', 500);
        }

        if ($start > 0 && stream_get_meta_data($this->stream)['seekable']) {
            fseek($this->stream, $start);
        }

        $remaining = ($end >= $start) ? ($end - $start + 1) : PHP_INT_MAX;

        while (!feof($this->stream) && $remaining > 0) {
            $chunk = min($this->bufferSize, $remaining);
            $data  = fread($this->stream, $chunk);
            if ($data === false) break;
            echo $this->gzip ? gzencode($data) : $data;
            $remaining -= strlen($data);
            flush();
        }

        return true;
    }

    private function isNotModified(): bool
    {
        $lastMod = $this->lastModified
            ?? ($this->file !== '' ? filemtime($this->file) : null);

        // Check If-None-Match (ETag)
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $this->etag !== '') {
            return trim($_SERVER['HTTP_IF_NONE_MATCH'], '"') === $this->etag;
        }

        // Check If-Modified-Since
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $lastMod !== null) {
            $since = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            return $since !== false && $lastMod <= $since;
        }

        return false;
    }

    private function detectMime(string $file): string
    {
        // Try finfo first (most accurate)
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file);
            finfo_close($finfo);
            if ($mime !== false && $mime !== '') {
                return $mime;
            }
        }

        // Fallback: extension map
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return self::MIME_MAP[$ext] ?? 'application/octet-stream';
    }

    // Common MIME type map (fallback when finfo is unavailable)
    private const MIME_MAP = [
        'pdf'  => 'application/pdf',
        'zip'  => 'application/zip',
        'gz'   => 'application/gzip',
        'tar'  => 'application/x-tar',
        'rar'  => 'application/x-rar-compressed',
        '7z'   => 'application/x-7z-compressed',
        'xls'  => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'ppt'  => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'txt'  => 'text/plain',
        'csv'  => 'text/csv',
        'html' => 'text/html',
        'htm'  => 'text/html',
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'mp3'  => 'audio/mpeg',
        'ogg'  => 'audio/ogg',
        'wav'  => 'audio/wav',
        'mp4'  => 'video/mp4',
        'webm' => 'video/webm',
        'avi'  => 'video/x-msvideo',
        'mov'  => 'video/quicktime',
    ];
}
