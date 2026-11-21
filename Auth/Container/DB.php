<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4
foldmethod=marker: */

/**
 * Storage driver for use against PEAR DB
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Martin Jansen <mj@php.net>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Auth
 */

/**
 * Include Auth_Container base class
 */
require_once __DIR__ . '/../Container.php';

/**
 * Include PEAR DB
 */
require_once __DIR__ . '/../DB.php';

/**
 * Storage driver for fetching login data from a database
 *
 * This storage driver can use all databases which are supported
 * by the PEAR DB abstraction layer to fetch login data.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Martin Jansen <mj@php.net>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: @package_version@  File: $Revision$
 * @link       http://pear.php.net/package/Auth
 */
class Auth_Container_DB extends Auth_Container
{

    // {{{ properties

    /**
     * Additional options for the storage container
     * @var array
     */
    var $options = array();

    /**
     * DB object
     * @var object
     */
    var $db = null;
    var $dsn = '';

    /**
     * User that is currently selected from the DB.
     * @var string
     */
    var $activeUser = '';

    // }}}
    // {{{ Auth_Container_DB [constructor]

    /**
     * Constructor of the container class
     *
     * Save the initial options passed to the container. Initiation of the DB
     * connection is no longer performed here and is only done when needed.
     *
     * @param  string Connection data or DB object
     * @return object Returns an error object if something went wrong
     */
    function __construct($dsn)
    {
        $this->_setDefaults();

        if (is_array($dsn)) {
            $this->_parseOptions($dsn);

            if (empty($this->options['dsn'])) {
                PEAR::raiseError('No connection parameters specified!');
            }
        } else {
            $this->options['dsn'] = $dsn;
        }
    }

    function Auth_Container_DB($dsn)
    {
        $this->__construct($dsn);
    }

    // }}}
    // {{{ _connect()

    /**
     * Connect to database by using the given DSN string
     *
     * @access private
     * @param  string DSN string
     * @return mixed  Object on error, otherwise bool
     */
    function _connect($dsn)
    {
        $this->log('Auth_Container_DB::_connect() called.', AUTH_LOG_DEBUG);

        if (is_array($dsn)) {
            $this->db = new mysqli($dsn['host'], $dsn['user'], $dsn['password'], $dsn['database']);
            if ($this->db->connect_error) {
                return PEAR::raiseError('DB Error: ' . $this->db->connect_error);
            }
        } elseif (is_string($dsn)) {
            // parse DSN string if needed, but for now, assume array
            return PEAR::raiseError('DSN string not supported, use array');
        } else {
            return PEAR::raiseError('Invalid DSN');
        }

        return true;
    }

    // }}}
    // {{{ _prepare()

    /**
     * Prepare database connection
     *
     * This function checks if we have already opened a connection to
     * the database. If that's not the case, a new connection is opened.
     *
     * @access private
     * @return mixed True or a DB error object.
     */
    function _prepare()
    {
        if (!$this->db || $this->db->connect_error) {
            $res = $this->_connect($this->options);
            if (!$res) {
                return $res;
            }
        }
        $this->options['final_table'] = $this->options['table'];
        $this->options['final_usernamecol'] = $this->options['usernamecol'];
        $this->options['final_passwordcol'] = $this->options['passwordcol'];
        return true;
    }

    // }}}
    // {{{ query()

    /**
     * Prepare query to the database
     *
     * This function checks if we have already opened a connection to
     * the database. If that's not the case, a new connection is opened.
     * After that the query is passed to the database.
     *
     * @access public
     * @param  string Query string
     * @return mixed  a DB_result object or DB_OK on success, a DB
     *                or PEAR error on failure
     */
    function query($query)
    {
        $err = $this->_prepare();
        if ($err !== true) {
            return $err;
        }
        return $this->db->query($query);
    }

    // }}}
    // {{{ _setDefaults()

    /**
     * Set some default options
     *
     * @access private
     * @return void
     */
    function _setDefaults()
    {
        $this->options['table']       = 'auth';
        $this->options['usernamecol'] = 'username';
        $this->options['passwordcol'] = 'password';
        $this->options['dsn']         = '';
        $this->options['host']        = '';
        $this->options['user']        = '';
        $this->options['password']    = '';
        $this->options['database']    = '';
        $this->options['db_fields']   = '';
        $this->options['cryptType']   = 'md5';
        $this->options['db_options']  = array();
        $this->options['db_where']    = '';
        $this->options['auto_quote']  = true;
    }

    // }}}
    // {{{ _parseOptions()

    /**
     * Parse options passed to the container class
     *
     * @access private
     * @param  array
     */
    function _parseOptions($array)
    {
        foreach ($array as $key => $value) {
            if (isset($this->options[$key])) {
                $this->options[$key] = $value;
            }
        }
    }

    // }}}
    // {{{ _quoteDBFields()

    /**
     * Quote the db_fields option to avoid the possibility of SQL injection.
     *
     * @access private
     * @return string A properly quoted string that can be concatenated into a
     * SELECT clause.
     */
    function _quoteDBFields()
{
    if (empty($this->options['db_fields'])) {
        return '';
    }

    $fields = $this->options['db_fields'];
    $autoQuote = !empty($this->options['auto_quote']);

    if (is_array($fields)) {
        if ($autoQuote) {
            $quotedFields = array_map(
                fn($field) => $this->db->quoteIdentifier($field),
                $fields
            );
            return implode(', ', $quotedFields);
        }
        return implode(', ', $fields);
    }

    if (is_string($fields) && strlen($fields) > 0) {
        return $autoQuote ? $this->db->quoteIdentifier($fields) : $fields;
    }

    return '';
}

    // }}}
    // {{{ fetchData()

    /**
     * Get user information from database
     *
     * This function uses the given username to fetch
     * the corresponding login data from the database table. If an account that matches the passed username
     * and password is found, the function returns true.
     * Otherwise it returns false.
     *
     * @param   string Username
     * @param   string Password
     * @param   boolean If true password is secured using a md5 hash
     *                  the frontend and auth are responsible for making sure the container supports
     *                  challenge response password authentication
     * @return  mixed  Error object or boolean
     */
    function fetchData($username, $password, $isChallengeResponse=false)
    {
        $this->log('Auth_Container_DB::fetchData() called.', AUTH_LOG_DEBUG);
        // Prepare for a database query
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode(),
                                    null, null, $err->getUserInfo());
        }

        // Find if db_fields contains a *, if so assume all columns are selected
        if (is_string($this->options['db_fields'])
            && strstr($this->options['db_fields'], '*')) {
            $sql_from = "*";
        } else {
            $sql_from = $this->options['final_usernamecol'].
                ", ".$this->options['final_passwordcol'];

            if (strlen($fields = $this->_quoteDBFields()) > 0) {
                $sql_from .= ', '.$fields;
            }
        }

        $query = "SELECT ".$sql_from.
                " FROM ".$this->options['final_table'].
                " WHERE ".$this->options['final_usernamecol']." = '".$this->db->real_escape_string($username)."'";

        // check if there is an optional parameter db_where
        if ($this->options['db_where'] != '') {
            // there is one, so add it to the query
            $query .= " AND ".$this->options['db_where'];
        }

        $this->log('Running SQL against DB: '.$query, AUTH_LOG_DEBUG);

        $result = $this->db->query($query);
        if (!$result) {
            return PEAR::raiseError('DB Error: ' . $this->db->error);
        }

        $res = $result->fetch_assoc();

        if (!is_array($res)) {
            $this->activeUser = '';
            return false;
        }

        // Perform trimming here before the hashing
        $password = trim($password, "\r\n");
        $res[$this->options['passwordcol']] = trim($res[$this->options['passwordcol']], "\r\n");

        // If using Challenge Response md5 the pass with the secret
        if ($isChallengeResponse) {
            $res[$this->options['passwordcol']] = md5($res[$this->options['passwordcol']]
                    .$this->_auth_obj->session['loginchallenege']);
            // UGLY cannot avoid without modifying verifyPassword
            if ($this->options['cryptType'] == 'md5') {
                $res[$this->options['passwordcol']] = md5($res[$this->options['passwordcol']]);
            }
            //print " Hashed Password [{$res[$this->options['passwordcol']]}]<br/>\n";
        }

        if ($this->verifyPassword($password,
                    $res[$this->options['passwordcol']],
                    $this->options['cryptType'])) {
            // Store additional field values in the session
            foreach ($res as $key => $value) {
                if ($key == $this->options['passwordcol'] ||
                    $key == $this->options['usernamecol']) {
                    continue;
                }
                $this->log('Storing additional field: '.$key, AUTH_LOG_DEBUG);
                // Use reference to the auth object if exists
                // This is because the auth session variable can change so a
                // static call to setAuthData does not make sence
                $this->_auth_obj->setAuthData($key, $value);
            }
            $this->activeUser = $res[$this->options['usernamecol']];
            return $res;
        }
        $this->activeUser = '';
        return false;
    }

    // }}}
    // {{{ listUsers()

    /**
     * Returns a list of users from the container
     *
     * @return mixed
     * @access public
     */
    function listUsers()
    {
        $this->log('Auth_Container_DB::listUsers() called.', AUTH_LOG_DEBUG);
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode(),
                                    null, null, $err->getUserInfo());
        }

        $retVal = array();

        // Find if db_fields contains a *, if so assume all col are selected
        if (is_string($this->options['db_fields'])
            && strstr($this->options['db_fields'], '*')) {
            $sql_from = "*";
        } else {
            $sql_from = $this->options['final_usernamecol'].
                ", ".$this->options['final_passwordcol'];

            if (strlen($fields = $this->_quoteDBFields()) > 0) {
                $sql_from .= ', '.$fields;
            }
        }

        $query = sprintf("SELECT %s FROM %s",
                         $sql_from,
                         $this->options['final_table']
                         );

        // check if there is an optional parameter db_where
        if ($this->options['db_where'] != '') {
            // there is one, so add it to the query
            $query .= " WHERE ".$this->options['db_where'];
        }

        $this->log('Running SQL against DB: '.$query, AUTH_LOG_DEBUG);

        $result = $this->db->query($query);
        if (!$result) {
            return PEAR::raiseError('DB Error: ' . $this->db->error);
        }

        $res = [];
        while ($row = $result->fetch_assoc()) {
            $res[] = $row;
        }

        foreach ($res as $user) {
            $user['username'] = $user[$this->options['usernamecol']];
            $retVal[] = $user;
        }
        $this->log('Found '.count($retVal).' users.', AUTH_LOG_DEBUG);
        return $retVal;
    }

    // }}}
    // {{{ addUser()

    /**
     * Add user to the storage container
     *
     * @access public
     * @param  string Username
     * @param  string Password
     * @param  mixed  Additional information that are stored in the DB
     *
     * @return mixed True on success, otherwise error object
     */
    function addUser($username, $password, $additional = "")
    {
        $this->log('Auth_Container_DB::addUser() called.', AUTH_LOG_DEBUG);
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode(),
                                    null, null, $err->getUserInfo());
        }

        if (isset($this->options['cryptType'])
            && $this->options['cryptType'] == 'none') {
            $cryptFunction = 'strval';
        } elseif (isset($this->options['cryptType'])
                  && function_exists($this->options['cryptType'])) {
            $cryptFunction = $this->options['cryptType'];
        } else {
            $cryptFunction = 'md5';
        }

        $password = $cryptFunction($password);

        $additional_key   = '';
        $additional_value = '';

        if (is_array($additional)) {
            foreach ($additional as $key => $value) {
                $additional_key .= ', ' . $key;
                $additional_value .= ", '" . $this->db->real_escape_string($value) . "'";
            }
        }

        $query = "INSERT INTO ".$this->options['final_table']." (".$this->options['final_usernamecol'].
                 ", ".$this->options['final_passwordcol'].$additional_key.
                 ") VALUES ('".$this->db->real_escape_string($username)."', '".$this->db->real_escape_string($password)."'".$additional_value.")";

        $this->log('Running SQL against DB: '.$query, AUTH_LOG_DEBUG);

        if (!$this->db->query($query)) {
            return PEAR::raiseError('DB Error: ' . $this->db->error);
        }
        return true;
    }

    // }}}
    // {{{ removeUser()

    /**
     * Remove user from the storage container
     *
     * @access public
     * @param  string Username
     *
     * @return mixed True on success, otherwise error object
     */
    function removeUser($username)
    {
        $this->log('Auth_Container_DB::removeUser() called.', AUTH_LOG_DEBUG);

        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode(),
                                    null, null, $err->getUserInfo());
        }

        // check if there is an optional parameter db_where
        if ($this->options['db_where'] != '') {
            // there is one, so add it to the query
            $where = " AND ".$this->options['db_where'];
        } else {
            $where = '';
        }

        $query = sprintf("DELETE FROM %s WHERE %s = '%s' %s",
                         $this->options['final_table'],
                         $this->options['final_usernamecol'],
                         $this->db->real_escape_string($username),
                         $where
                         );

        $this->log('Running SQL against DB: '.$query, AUTH_LOG_DEBUG);

        if (!$this->db->query($query)) {
            return PEAR::raiseError('DB Error: ' . $this->db->error);
        }
        return true;
    }

    // }}}
    // {{{ changePassword()

    /**
     * Change password for user in the storage container
     *
     * @param string Username
     * @param string The new password (plain text)
     */
    function changePassword($username, $password)
    {
        $this->log('Auth_Container_DB::changePassword() called.', AUTH_LOG_DEBUG);
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode(),
                                    null, null, $err->getUserInfo());
        }

        if (isset($this->options['cryptType'])
            && $this->options['cryptType'] == 'none') {
            $cryptFunction = 'strval';
        } elseif (isset($this->options['cryptType'])
                  && function_exists($this->options['cryptType'])) {
            $cryptFunction = $this->options['cryptType'];
        } else {
            $cryptFunction = 'md5';
        }

        $password = $cryptFunction($password);

        // check if there is an optional parameter db_where
        if ($this->options['db_where'] != '') {
            // there is one, so add it to the query
            $where = " AND ".$this->options['db_where'];
        } else {
            $where = '';
        }

        $query = sprintf("UPDATE %s SET %s = '%s' WHERE %s = '%s' %s",
                         $this->options['final_table'],
                         $this->options['final_passwordcol'],
                         $this->db->real_escape_string($password),
                         $this->options['final_usernamecol'],
                         $this->db->real_escape_string($username),
                         $where
                         );

        $this->log('Running SQL against DB: '.$query, AUTH_LOG_DEBUG);

        if (!$this->db->query($query)) {
            return PEAR::raiseError('DB Error: ' . $this->db->error);
        }
        return true;
    }

    // }}}
    // {{{ supportsChallengeResponse()

    /**
     * Determine if this container supports
     * password authentication with challenge response
     *
     * @return bool
     * @access public
     */
    function supportsChallengeResponse()
    {
        return in_array($this->options['cryptType'], array('md5', 'none', ''));
    }

    // }}}
    // {{{ getCryptType()

    /**
      * Returns the selected crypt type for this container
      */
    function getCryptType()
    {
        return($this->options['cryptType']);
    }

    // }}}

}
?>