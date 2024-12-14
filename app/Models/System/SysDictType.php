<?php

namespace App\Models\System;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SysDictType extends Model
{

    public $fillable = [];

    public const CREATED_AT = 'create_time';
    public const UPDATED_AT = 'update_time';
    public $timestamps = true;
    protected $dateFormat = 'U';


    /**
     * 类初始化
     * @return $this
     */
    public static function new()
    {
        return new static();
    }

    /**
     * 表名约定
     * @return string
     */
    public function getTable()
    {
        return $this->table ?? Str::snake(class_basename($this));
    }

    /**
     * 转驼峰
     * @return array
     */
    public function toArray()
    {
        $items  = parent::toArray();
        $items  = array_filter($items, function ($item) {
            return !is_null($item);
        });
        $keys   = array_keys($items);
        $keys   = array_map(function ($item) {
            return lcfirst(Str::studly($item));
        }, $keys);
        $values = array_values($items);
        return array_combine($keys, $values);
    }


}
