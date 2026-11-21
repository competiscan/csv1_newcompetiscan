<?php

namespace HS;

class Search
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
     * Get the list of users who are to be notified about new search results
     *
     * @return array
     */
    public function getNotificationList()
    {
        $notifyList = array();

        $query = "SELECT SQL_NO_CACHE DISTINCT ID, searchName, userID, notify,
            lastSentDate, queryDate, sendTo, mail_format, addedToDatabase, weekday, is_public
            FROM cscan_search
            WHERE emailAlert='1' AND
                ((notify='daily' AND LEFT(lastSentDate,10)<=DATE_SUB(CURDATE(),INTERVAL 1 DAY)) OR
                    (notify='weekly' AND LEFT(lastSentDate,10)<=DATE_SUB(CURDATE(),INTERVAL 7 DAY)) OR
                    (notify='monthly' AND LEFT(lastSentDate,10)<=DATE_SUB(CURDATE(),INTERVAL 1 MONTH)))";

        $result = $this->DRW->query($query, $this->databaseToRead);

        while ($row = $this->DRW->fetch_assoc($result)) {
            $notifyList[] = $row;
        }

        return $notifyList;
    }
}
