<?php

use CFPropertyList\CFPropertyList;
use munkireport\processors\Processor;

class Installhistory_processor extends Processor
{
    public function run($plist)
    {
        $save_array = [];
        
        // Delete old data
        Installhistory_model::where('serial_number', $this->serial_number)->delete();
        
         // Check if we're passed a plist (10.6 and higher)
        if (strpos($plist, '<?xml version="1.0" encoding="UTF-8"?>') === 0) {
            // Strip invalid xml chars
            $plist = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '�', $plist);
            
            $parser = new CFPropertyList();
            $parser->parse($plist, CFPropertyList::FORMAT_XML);
            $mylist = $parser->toArray();
    
            foreach ($mylist as $item) {
                // PackageIdentifiers is an array, so we only retain one
                // packageidentifier so we can differentiate between
                // Apple and third party tools
                if (array_key_exists('packageIdentifiers', $item)) {
                    $packageIdentifiers = array_pop($item['packageIdentifiers']);
                }else{
                    $packageIdentifiers = '';
                }
                $item['serial_number'] = $this->serial_number;
                $save_array[] = [
                    'serial_number' => $this->serial_number,
                    'date' => $item['date'] ? $item['date'] : 0,
                    'displayName' => $item['displayName'] ? $item['displayName'] : '',
                    'displayVersion' => $item['displayVersion'] ? $item['displayVersion'] : '',
                    'packageIdentifiers' => $packageIdentifiers,
                    'processName' => $item['processName'] ? $item['processName'] : '',
                ];          
            }
        } else // 10.5 Software Update Log
        {
            //2007-12-14 12:40:47 +0100: Installed "GarageBand Update" (4.1.1)
            $pattern = '/^(.*): .*"(.+)"\s+\((.+)\)/m';
            if (preg_match_all($pattern, $plist, $result, PREG_SET_ORDER)) {
                $item = [
                    'packageIdentifiers' => 'com.apple.fake',
                    'processName' => 'installer',
                ];
    
                foreach ($result as $row) {
                    $item['date'] = strtotime($row[1]);
                    $item['displayName'] = $row[2];
                    $item['displayVersion'] = $row[3];
                    $item['serial_number'] = $this->serial_number;
                    $save_array[] = $item;
                }
            }
        }

        // Bulk insert
        Installhistory_model::insertChunked($save_array);
    }
}
