<?php
namespace App\Lib\Excel\Export;

interface BaseExportInterface
{
    //导出
    public function export($file);

    //导出下载模板
    public function downLoad();



}
