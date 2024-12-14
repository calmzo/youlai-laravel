<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Utils\CodeResponse;
use App\ValidateRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    use ValidateRequest;

    /**
     * WxController constructor.
     */
    public function __construct()
    {

    }

    protected function codeReturn(array $codeResponse, $data = null, $info = '')
    {
        list($errno, $errmsg) = $codeResponse;
        $ret = ['code' => $errno];
        if (!is_null($data)) {
            $ret['data'] = $data;
        }
        $ret['msg'] = $info ?: $errmsg;
        return response()->json($ret);
    }

    protected function success($data = null)
    {
        return $this->codeReturn(CodeResponse::SUCCESS, $data);
    }

    protected function fail(array $codeResponse = CodeResponse::SYSTEM_EXECUTION_ERROR, $info = '')
    {
        return $this->codeReturn($codeResponse, null, $info);
    }

    protected function successPaginate($page)
    {
        return $this->success($this->paginate($page));
    }

    protected function paginate($page, $list = null)
    {
        if ($page instanceof LengthAwarePaginator) {
            return [
                'total' => $page->total(),
                'page' => $page->total() == 0 ? 0 : $page->currentPage(),
                'limit' => $page->perPage(),
                'pages' => $page->total() == 0 ? 0 : $page->lastPage(),
                'list' => $list ?? $page->items()
            ];
        }
        if ($page instanceof Collection) {
            $page = $page->toArray();
        }
        if (!is_array($page)) {
            return $page;
        }
        $total = count($page);
        return [
            'total' => $total,
            'page' => 1,
            'limit' => $total,
            'pages' => 1,
            'list' => $page
        ];

        return $page;

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 401
     */
    protected function badArgument()
    {
        return $this->fail(CodeResponse::PARAM_ERROR);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 402
     */
    protected function badArgumentValue()
    {
        return $this->fail(CodeResponse::PARAM_NOT_EMPTY);
    }

}
