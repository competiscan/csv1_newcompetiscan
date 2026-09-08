<?php

namespace HS;

class Age
{
    /**
     * @var bool|int
     */
    private $tmpAge = false;

    /**
     * @var bool|array|int
     */
    private $tmpGroups = false;

    /**
     * @var array
     */
    private $ages = array();

    /**
     * Age constructor.
     * @param \databaseReadWrite $DRW
     */
    public function __construct(\databaseReadWrite $DRW)
    {
        $this->DRW = $DRW;
        $this->cacheAll();
    }

    /**
     * Cache all the ages.
     */
    private function cacheAll()
    {
        $result = $this->DRW->query("SELECT * FROM cscan_age_product");

        while ($row = $this->DRW->fetch_assoc($result)) {
            $this->ages[] = $row;
        }
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->ages;
    }

    /**
     * @param $age
     */
    public function setAge($age)
    {
        $this->tmpAge = $age;
        $this->tmpGroups = $this->getGroupIdsByAge($age);
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->tmpAge;
    }

    /**
     * @return string
     */
    public function getGroupsAsCommaDelimitedString()
    {
        if (is_array($this->tmpGroups)) {
            return "'" . implode(',', $this->tmpGroups) . "'";
        } elseif (is_int($this->tmpGroups)) {
            return $this->tmpGroups;
        } else {
            return "''";
        }
    }

    /**
     * @param $int
     * @return array|bool|int
     */
    public function getGroupIdsByAge($int)
    {
        $out = array();

        foreach ($this->ages as $age) {
            if ($age['age_pmin'] <= $int && $int <= $age['age_pmax']) {
                $out[] = $age['age_pID'];
            }
        }

        if (empty($out)) {
            return false;
        } else if (count($out) == 1) {
            return (int) $out[0];
        } else {
            return $out;
        }
    }
}
