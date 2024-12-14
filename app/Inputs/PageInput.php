<?php

namespace App\Inputs;

use Illuminate\Validation\Rule;

class PageInput extends Input
{
    public $pageNum = 1;
    public $pageSize = 10;
    public $sort = 'create_time';
    public $order = 'desc';


    /**
     * @return array
     */
    public function rule()
    {
        return [
            'pageNum' => 'integer',
            'pageSize' => 'integer',
            'sort' => 'string',
            'order' => Rule::in(['desc', 'asc']),
        ];
    }

    /**
     * @return array|string[]
     */
    public function message()
    {
        return [
            'categoryId.required' => 'categoryId不能为空',
            'categoryId.integer' => 'categoryId必须为数字',
        ];
    }


    public static function new($data = null)
    {
        return (new static())->fill($data);
    }

}
