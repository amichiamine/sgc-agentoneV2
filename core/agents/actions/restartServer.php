<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class RestartServer {
    public function execute() {
        $stop = new StopServer();
        $stopResult = $stop->execute();
        
        if (!$stopResult['success']) {
            return $stopResult;
        }
        
        $start = new StartServer();
        return $start->execute();
    }
}
