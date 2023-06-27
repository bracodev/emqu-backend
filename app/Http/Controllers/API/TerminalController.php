<?php

namespace App\Http\Controllers\API;

use Acamposm\Ping\Ping;
use App\Helpers\PingIp;
use App\Models\Terminal;
use Illuminate\Http\Response;
use App\Models\TerminalStadistic;
use Illuminate\Support\Facades\Auth;
use Acamposm\Ping\PingCommandBuilder;
use App\Actions\PingTerminalAction;
use App\Http\Requests\StoreTerminalRequest;
use App\Http\Requests\UpdateTerminalRequest;
use App\Http\Controllers\API\BaseAPIController;

class TerminalController extends BaseAPIController
{
    /**
     * Get all data
     *
     * @return void
     */
    public function index()
    {
        $data = Terminal::with('user')->orderBy('created_at')->get();
        return $this->sendResponse($data, 'User login successfully.');
    }

    /**
     * Store new resouce
     *
     * @param  \App\Http\Requests\StoreTerminalRequest $request
     * @return void
     */
    public function store(StoreTerminalRequest $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data = Terminal::create($data);
        return $this->sendResponse($data, 'El equipo ha sido guardado');
    }

    /**
     * Update respurce
     *
     * @param  \App\Http\Requests\UpdateTerminalRequest $request
     * @param  integer                                  $id
     * @return void
     */
    public function update(UpdateTerminalRequest $request, int $id)
    {
        $model = Terminal::find($id);

        if ($model === null) {
            return response()->json(
                ['message' => "User with id {$id} not found"],
                Response::HTTP_NOT_FOUND
            );
        }

        if ($model === null) {
            return response()->json(
                ['message' => "User with id {$id} not found"],
                Response::HTTP_NOT_FOUND
            );
        }

        if ($model->update($request->all()) === false) {
            return response()->json(
                ['message' => "Couldn't update the user with id {$request->id}"],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->sendResponse($model, 'Los cambios han sido guardados');
    }

    /**
     * Delete resource
     *
     * @param  integer $id
     * @return void
     */
    public function destroy(int $id)
    {
        $model = TerminalStadistic::where(['terminal_id' => $id])->first();
        if ($model){
            return response()->json(
                ['message' => "No es posible eliminar el registro ya que tiene registros de pruebas"],
                412
            );
        }
        Terminal::destroy($id);
        return $this->sendResponse([], 'El registro ha sido eliminado');
    }

    /**
     * Undocumented function
     *
     * @param  integer $id
     * @return void
     */
    public function pingTerminalLogs(int $id)
    {
        $model = TerminalStadistic::where(['terminal_id' => $id])->orderByDesc('created_at')->get();
        return $this->sendResponse($model, 'Logs de terminal');
    }

    /**
     * Undocumented function
     *
     * @param  integer                         $id
     * @param  \App\Actions\PingTerminalAction $action
     * @return void
     */
    public function pingTerminal(int $id, PingTerminalAction $action)
    {
        $data = $action->handle($id);

        return $this->sendResponse($data, 'Ping realizado');
    }

    /**
     * Undocumented function
     *
     * @param  \App\Actions\PingTerminalAction $action
     * @return void
     */
    public function pingAllTerminals(PingTerminalAction $action)
    {
        $data = Terminal::where(['enabled' => true])->get();
        $response = [];

        $transmitted = 0;
        $received = 0;
        $online = 0;
        $time = 0;

        foreach ($data as $value) {
            $ping =  $action->handle($value->id);

            $transmitted += $ping['transmitted'];
            $received += $ping['received'];
            $online += $ping['status'] ? 1 : 0;

            $time += str_replace('ms', '', $ping['time']);

            $response['logs'][] = $ping;
        }

        $response['stats']['terminals'] = $data->count();
        $response['stats']['online'] = $online;
        $response['stats']['offline'] = $data->count() - $online;
        $response['stats']['time'] = $time / 1000;

        $response['stats']['packages'] = [
            'transmitted' => $transmitted,
            'received' => $received,
            'loss' => ($transmitted - $received),
            'loss_percent' => ($transmitted - $received) * 100 / $transmitted
        ];

        return $this->sendResponse($response, 'Pings realizados');
    }
}
