<?php
namespace App\Actions;

use App\Helpers\PingIp;
use App\Models\Terminal;
use App\Models\TerminalStadistic;

class PingTerminalAction
{
    public function handle(int $terminal_id)
    {
        $model = Terminal::findOrFail($terminal_id);
        $ping = PingIp::exec($model->ipv4);

        $data = [
            'terminal_id' => $terminal_id,
            'logs' =>  implode('|',  $ping['result']),
            'transmitted' => $ping['statistics']['transmitted'],
            'received' => $ping['statistics']['received'],
            'loss' => $ping['statistics']['loss'],
            'time' => $ping['statistics']['time'],
            'status' => $ping['statistics']['status'],
        ];

        TerminalStadistic::create($data);

        $data['logs'] =  $ping['result'];

        return $data;
    }
}
