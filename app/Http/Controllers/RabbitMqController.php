<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RabbitMqController extends BaseController
{
    public function saveSysLog(Request $request) {

        $params = $request->input();
        Log::channel('mq-consume')->info(sprintf('saveSysLog消费了:【%s】', json_encode($params)));
    }

}
