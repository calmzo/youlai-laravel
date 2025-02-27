<?php

namespace App\Lib\Excel\Import;

use App\Models\Customer;
use App\Models\JzdShuweibanRecord;
use App\Models\System\SysRole;
use App\Models\System\SysUser;
use App\Services\UserListExternalService;
use App\Utils\Constant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class UserImport extends BaseImport implements BaseImportInterface
{
    private $deptId = 0;
    private $invalidCount = 0;
    private $validCount = 0;
    private $messageList = [];

    public function import($data)
    {
        $this->deptId = $data['deptId'] ?? 0;
        $file   = $data['file'];

        //获取文件内容
        // 从storage获取文件内容
        $reader        = IOFactory::createReader('Xlsx');
        $spreadsheet   = $reader->load($file); ////载入excel表格
        $worksheet     = $spreadsheet->getActiveSheet();
        $highestRow    = $worksheet->getHighestRow(); // 总行数
        $highestColumn = $worksheet->getHighestColumn(); // 总列数
        $lines         = $highestRow - 2;
        if ($lines <= 0) {
            Log::error('Excel表格中没有数据');
            return false;
        }
        $invalidCount = 0;
        $validCount = 0;
        // 循环每个工作表并获取数据
        for ($row = 3; $row <= $highestRow; $row++) {
            $this->invoke($worksheet, $row);
        }
        Log::info(sprintf('导入用户数据完成，成功导入%s条，失败%s条',$this->validCount, $this->invalidCount));
        Log::info(sprintf('导入用户数据完成，msg:%s', implode('</br>', $this->messageList)));
        return $this->messageList;
    }

    private function invoke(Worksheet $worksheet, $row)
    {
        $username  = $worksheet->getCell($this->columns[0] . $row)->getValue();
        $nickname  = $worksheet->getCell($this->columns[1] . $row)->getValue();
        $gender    = $worksheet->getCell($this->columns[2] . $row)->getValue() == '女' ? 2 : 1; //性别
        $mobile    = $worksheet->getCell($this->columns[3] . $row)->getValue();
        $email     = $worksheet->getCell($this->columns[4] . $row)->getValue(); //邮箱
        $roleCodes = $worksheet->getCell($this->columns[5] . $row)->getValue(); //角色

        Log::info(sprintf('解析到一条用户数据:%s', json_encode([
            '用户名' => $username,
            '昵称' => $nickname,
            '手机号' => $mobile,
            '性别' => $gender,
            '邮箱' => $email,
            '角色' => $roleCodes,
        ], JSON_UNESCAPED_UNICODE)));

        $validation = true;
        $errorMsg = sprintf("第%s行数据校验失败;", $row);
        if (empty($username)) {
            $errorMsg .= '用户名为空；';
            $validation = false;
        } else {
            $count = SysUser::query()->where('username', $username)->withTrashed()->count();
            if ($count > 0) {
                $errorMsg .= '用户名已存在；';
                $validation = false;
            }
        }
        if (empty($nickname)) {
            $errorMsg .= '用户昵称为空；';
            $validation = false;
        }
        if (empty($mobile)) {
            $errorMsg .= '手机号码为空；';
            $validation = false;
        }
        if ($validation) {

            $roleIds   = null;
            if (!empty($roleCodes)) {
                $roleIds = SysRole::query()->whereIn('code', explode(',', $roleCodes))->where('status', 1)->pluck('id');
            }


            //校验通过，持久化至数据库
            $sysUser           = SysUser::new();
            $sysUser->username  = $username;
            $sysUser->nickname  = $nickname;
            $sysUser->dept_id  = $this->deptId;
            $sysUser->password = Hash::make(Constant::DEFAULT_PASSWORD);
            $sysUser->gender   = $gender;
            $sysUser->email   = $email;
            $sysUser->mobile   = $mobile;
            $sysUser->avatar   = 'http://cdnwm.yuluojishu.com/c5ca2fb3e92a726d4dec641fc87c2f98.jpg';
            $saveResult        = $sysUser->save();
            if ($saveResult) {
                $this->validCount ++;
                //保存用户关联角色
                if ($roleIds) {
                    $sysUser->roles()->attach($roleIds);
                }
            } else {
                $errorMsg .= sprintf("第%s行数据保存失败;", $row) . '</br>';
                array_push($this->messageList, $errorMsg);
            }

        } else {
            $this->invalidCount ++;
            array_push($this->messageList, $errorMsg);

        }
    }

}
