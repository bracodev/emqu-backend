<?php

namespace App\Helpers;

abstract class PingIp
{
    static private function os()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === (chr(87) . chr(73) . chr(78)))
            return true;
        return false;
    }

    static public function exec($ipv4, $count = 3)
    {
        if (self::os()) {
            return exec("ping -n 1 -w 1 $ipv4 2>NUL > NUL && (echo 0) || (echo 1)");
        } else {
            exec("ping -c$count $ipv4",  $output);

            $statPosition = count($output) - 2;
            $_stat = explode(',', $output[$statPosition]);
            $stat = [];

            preg_match("/\d+/", $_stat[0], $transmitted);
            $stat['transmitted'] = $transmitted[0];

            preg_match("/\d+/", $_stat[1], $received);
            $stat['received'] = $received[0];

            preg_match("/\d+\%/", $_stat[2], $loss);
            $stat['loss'] = $loss[0];

            preg_match("/\d+(ms)/", $_stat[3], $time);
            $stat['time'] = $time[0];

            $stat['status'] = $stat['received'] < 1 ? false : true;

            return [
                'count' => $count,
                'result' => $output,
                'statistics' => $stat
            ];
        }
    }
}
