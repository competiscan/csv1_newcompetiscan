<?php

namespace HS;

class User
{
    /**
     * @param \databaseReadWrite $DRW
     * @param string $databaseToRead
     */
    public function __construct(\databaseReadWrite $DRW, $databaseToRead)
    {
        $this->DRW = $DRW;
        $this->databaseToRead = $databaseToRead;
    }

    /**
     * Get the list of email addresses from active users
     *
     * @return array
     */
    public function getEmailList()
    {
        $email_list = array();

        $query = "SELECT userID, emailAddress, is_public_user
            FROM cscan_users WHERE active='y'";

        $result = $this->DRW->query($query, $this->databaseToRead);

        while ($row = $this->DRW->fetch_assoc($result)) {
            $email_list[$row['userID']] = $row;
        }

        return $email_list;
    }
}
