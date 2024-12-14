<?php

namespace App\Inputs;

use App\Exceptions\BusinessException;
use App\Utils\CodeResponse;
use App\ValidateRequest;
use Illuminate\Support\Facades\Validator;

class Input
{
    use ValidateRequest;

    /**
     * @param null|array $data
     * @return $this
     * @throws BusinessException
     */
    public function fill($data = null)
    {
        if (is_null($data)) {
            $data = request()->input();
        }
        $message = $this->message();
        //过滤为null数据 todo
//        $data = array_filter($data);
        $data = array_filter($data, function ($value) {
            // 对0进行特殊处理
            if ($value === 0) {
                // 返回true，0将被保留在结果中
                return true;
            }
            // 过滤掉null值
            return !is_null($value);
        });
        $validator = Validator::make($data, $this->rule(), $message);
        if ($validator->fails()) {
//            throw new BusinessException(CodeResponse::PARAM_NOT_EMPTY, $validator->errors()->first());
            throw new BusinessException(CodeResponse::PARAM_ERROR, $validator->errors()->first());
        }
        //只接收子类定义的值
        $map = get_object_vars($this);
        $keys = array_keys($map);
        collect($data)->map(function ($v, $k) use ($keys) {
            if (in_array($k, $keys)) {
                $this->$k = $v;
            }
        });
        return $this;
    }

    /**
     * @return array
     */
    public function rule()
    {
        return [];
    }

    /**
     * @return array
     */
    public function message()
    {
        return [];
    }

    /**
     * @param null|array $data
     * @return Input
     * @throws BusinessException
     */
    public static function new($data = null)
    {
        return (new static())->fill($data);
    }

}
