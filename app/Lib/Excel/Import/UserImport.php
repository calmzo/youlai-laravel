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


class UserImport extends BaseImport implements BaseImportInterface
{

    public function import($data)
    {
        $deptId = $data['deptId'];
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
        $msg = '';
        // 循环每个工作表并获取数据
        for ($row = 3; $row <= $highestRow; $row++) {
            $validationMsg = '';
            $username      = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            $nickname = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
            $gender    = $worksheet->getCellByColumnAndRow(3, $row)->getValue() == '女' ? 2 : 1; //性别
            $mobile = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
            $email    = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); //邮箱
            $roleCodes = $worksheet->getCellByColumnAndRow(6, $row)->getValue(); //角色
            Log::info(sprintf('解析到一条用户数据:%s', json_encode([
                '用户名' => $username,
                '昵称' => $nickname,
                '手机号' => $mobile,
                '性别' => $gender,
                '邮箱' => $email,
                '角色' => $roleCodes,
            ], JSON_UNESCAPED_UNICODE)));
            if (empty($username)) {
//                Log::error('用户名为空；');
                $validationMsg .= '用户名为空；';
//                continue;
            } else {
                $count = SysUser::query()->where('username', $username)->withTrashed()->count();
                if ($count > 0) {
//                    Log::error('用户名已存在；');
                    $validationMsg .= '用户名已存在；';
//                    continue;
                }
            }
            if (empty($nickname)) {
                $validationMsg .= '用户昵称为空；';
//                Log::error('用户昵称为空；');
//                continue;
            }
            if (empty($mobile)) {
                $validationMsg .= '手机号码为空；';
//                Log::error('手机号码为空；');
//                continue;
            } else {
//                $count = SysUser::query()->where('username', $username)->count();
//                if ($count > 0) {
//                    Log::error('用户名已存在；');
//                    continue;
//                }
            }
            if (empty($validationMsg)) {

                $roleIds   = null;
                if (!empty($roleCodes)) {
                    $roleIds = SysRole::query()->whereIn('code', explode(',', $roleCodes))->where('status', 1)->pluck('id');
                }


                //校验通过，持久化至数据库
                $sysUser           = SysUser::new();
                $sysUser->username  = $username;
                $sysUser->nickname  = $nickname;
                $sysUser->dept_id  = $deptId;
                $sysUser->password = Hash::make(Constant::DEFAULT_PASSWORD);
                $sysUser->gender   = $gender;
                $sysUser->email   = $email;
                $sysUser->mobile   = $mobile;
                $sysUser->avatar   = 'http://cdnwm.yuluojishu.com/c5ca2fb3e92a726d4dec641fc87c2f98.jpg';
                $saveResult        = $sysUser->save();
                if ($saveResult) {
                    $validCount++;
                    //保存用户关联角色
                    if ($roleIds) {
                        $sysUser->roles()->attach($roleIds);
                    }
                } else {
                    $invalidCount++;
                    $msg .= sprintf("第%s行数据保存失败;", $validCount + $invalidCount) . '</br>';
                }

            } else {
                $invalidCount++;
                $msg .= sprintf("第%s行数据保存失败:【%s】", $validCount + $invalidCount, $validationMsg) . '</br>';

            }

        }
        Log::info(sprintf('导入用户数据完成，成功导入%s条，失败%s条', $validCount, $invalidCount));
        Log::info(sprintf('导入用户数据完成，msg:%s', $msg));
    }

}
