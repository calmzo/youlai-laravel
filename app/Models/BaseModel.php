<?php

namespace App\Models;

use App\Tools\Logs;
use Carbon\Carbon;
use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Support\Facades\DB;


class BaseModel extends Model
{
    use HasFactory, BooleanSoftDeletes;

    public const CREATED_AT = 'create_time';
    public const UPDATED_AT = 'update_time';
    public const DELETED_AT = 'is_deleted';

    public static $instance = null;

    protected $hidden = [
        'is_deleted'
    ];

    // 覆盖asJson方法
    protected function asJson($value)
    {
        return json_encode($value, 320);
    }

    public function getOriginal($key = null, $default = null)
    {
        $original = parent::getOriginal($key, $default);
        if (!is_null($key) && $key === 'update_time') {
            $original = is_numeric($original) ? $original : strtotime($original);
        }

        return $original;
    }
//    /**
//     * 自动维护更新时间
//     *
//     * @param mixed $value
//     * @return false|int|string|null
//     * @author 2023/12/8 16:06
//     */
//    public function fromDateTime($value)
//    {
//        return strtotime(parent::fromDateTime($value));
//    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
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
     * 数据转换
     * @var string[]
     */
//    public $defaultCasts = ['delete_time' => 'boolean'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
//        parent::mergeCasts($this->defaultCasts);
    }

    /**
     * 类初始化
     * @return $this
     */
    public static function new()
    {
        return new static();
    }

    /**
     * 转驼峰
     * @return array
     */
    public function toArray($studly = 1)
    {
        $items  = parent::toArray();
        if (!$studly) {
            return $items;
        }
//        $items  = array_filter($items, function ($item) {
//            return !is_null($item);
//        });
        $keys   = array_keys($items);
        $keys   = array_map(function ($item) {
            return lcfirst(Str::studly($item));
        }, $keys);
        $values = array_values($items);
        return array_combine($keys, $values);
    }

    /**
     * @return bool|int
     * @throws Throwable
     * 乐观锁的实现，修改数据之前先比较一下 compare and save
     */
    public function cas()
    {
        //当数据不存在时，禁止更新操作
        throw_if(!$this->exists, Exception::class, 'the data is not exist');

        //当内存中更新数据为空时，禁止更新操作
        $dirty = $this->getDirty(); //内存中修改的值
        if (empty($dirty)) {
            return 0;
        }
        //当模型开启自动更新时间字段时，附上更新的时间字段
        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
            $dirty = $this->getDirty();
        }

        $diff = array_diff(array_keys($dirty), array_keys($this->getOriginal()));

        if ($this->fireModelEvent('casing') === false) {
            return 0;
        }

        throw_if(!empty($diff), Exception::class, 'key [ ' . implode(',', $diff) . ' ] is not exist');

        //使用newModelQuery 更新的时候不用带上 deleted = 0 的条件
        $query = self::newModelQuery()->where($this->getKeyName(), $this->getKey());

        foreach ($dirty as $k => $v) {
            $query = $query->where($k, $this->getOriginal($k));  //判断一下更新的字段值是否有改动
        }
        $row = $query->update($dirty);
        if ($row > 0) {
            $this->syncChanges();
            $this->fireModelEvent('cased', false);
            $this->syncOriginal();
        }
        return $row;
    }

    /**
     * @param $callback
     */
    public static function casing($callback)
    {
        static::registerModelEvent('casing', $callback);
    }

    /**
     * @param $callback
     */
    public static function cased($callback)
    {
        static::registerModelEvent('cased', $callback);
    }


    /**
     * 批量更新
     *
     * @param array $multipleData
     * @param string $key
     * @return false|int
     * @author 2023/12/13 20:07
     */
    public function updateBatch($multipleData = [], $key = 'id')
    {
        try {
            if (empty($multipleData)) {
                throw new Exception("数据不能为空");
            }
            $tableName = DB::getConfig('prefix') . $this->table; // 表名

            $firstRow = current($multipleData);
            if (isset($firstRow[$key])) {
                $referenceColumn = $key;
                unset($firstRow[$key]);
                $updateColumn = array_keys($firstRow);
            } else {
                throw new Exception('索引条件不存在: the key is not existent!');
            }
//            unset($updateColumn[0]);

            $whereIn = "";
            $q       = "UPDATE " . $tableName . " SET ";
            foreach ($updateColumn as $uColumn) {
                $q .= $uColumn . " = CASE ";
                foreach ($multipleData as $data) {
                    $q .= "WHEN " . $referenceColumn . " = " . $data[$referenceColumn] . " THEN '" . $data[$uColumn] . "' ";
                }
                $q .= "ELSE " . $uColumn . " END, ";
            }
            foreach ($multipleData as $data) {
                $whereIn .= "'" . $data[$referenceColumn] . "', ";
            }
            $q = rtrim($q, ", ") . " WHERE " . $referenceColumn . " IN (" . rtrim($whereIn, ', ') . ")";
            return DB::update(DB::raw($q));
        } catch (Exception $e) {
            Logs::info('db_error', $e->getMessage(), 'sql_update');
            return false;
        }
    }

}
