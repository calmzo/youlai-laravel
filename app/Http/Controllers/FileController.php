<?php

namespace App\Http\Controllers;

use App\Services\Oss\AliyunOssService;
use Illuminate\Http\Request;

class FileController extends BaseController
{


    /**
     * 文件上传
     *
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     * @author 2024/6/22 15:10
     */
    public function uploadFile(Request $request)
    {
        $file = $request->file('file');
        $fileInfo = AliyunOssService::getInstance()->uploadFile($file);
        return $this->success($fileInfo);
    }

    /**
     * 删除文件
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @throws \OSS\Core\OssException
     * @throws \OSS\Http\RequestCore_Exception
     * @author 2024/6/22 15:39
     */
    public function deleteFile()
    {
        $filePath = $this->verifyString('filePath');
        $fileInfo = AliyunOssService::getInstance()->deleteFile($filePath);
        return $this->success($fileInfo);
    }

}
