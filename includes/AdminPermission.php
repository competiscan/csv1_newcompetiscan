<?php

require_once '../auth_inc.php';

class AdminPermission {

    public function userCanMassUpdate() {
        //45 is the permissionID for Mass Field Updates in the cscan_permission table on all environments.
        return checkGroup(45);
    }
}