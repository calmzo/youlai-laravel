<?php

namespace App\Lib\Excel\Export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class UserExport extends BaseExport implements BaseExportInterface
{

    public function export($list)
    {

        $title = [
            'id'           => 'ID',
            'username'     => '用户名',
            'nickname'     => '用户昵称',
            'dept_name'    => '部门',
            'gender_label' => '性别',
            'mobile'       => '手机号码',
            'email'        => '邮箱',
            'create_time'  => '创建时间',
        ];
        $data  = [];
        foreach ($list as $key => $item) {
            $data[$key]['id']           = $item->id ?? '';
            $data[$key]['username']     = $item->username ?? '';
            $data[$key]['nickname']     = $item->nickname ?? '';
            $data[$key]['dept_name']    = $item->dept_name ?? '';
            $data[$key]['gender_label'] = $item->gender_label ?? '';
            $data[$key]['mobile']       = $item->mobile ?? '';
            $data[$key]['email']        = $item->email ?? '';
            $data[$key]['create_time']  = $item->create_time ?? '';
        }
        $fileName = "用户列表";
        $sheet     = [
            'fileName' => $fileName,
            'list'     => $data,
            'title'    => $title,
        ];

        $this->assembly($sheet);
    }


    public function downLoad()
    {
        $title = [
            'username'     => '用户名',
            'nickname'     => '用户昵称',
            'gender_label' => '性别',
            'mobile'       => '手机号码',
            'email'        => '邮箱',
            'role'         => '角色',
        ];

        $data  = [];
        $fileName = "用户导入模板";
        $sheet     = [
            'fileName' => $fileName,
            'list'     => $data,
            'title'    => $title,
        ];
        $this->assembly($sheet);
        return $sheet;
    }

    /**
     * 组装并下载
     *
     * @param $sheet
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @author 2024/6/24 14:10
     */
    private function assembly($sheet)
    {
        $spreadsheet = new Spreadsheet();  //创建一个新的excel文档
        $fileName    = $sheet['fileName'] ?? 'xxx表格';
        $spreadsheet->getProperties()
            ->setCreator("Yuluo")    //作者
            ->setLastModifiedBy("Yuluo") //最后修改者
            ->setTitle($fileName)  //标题
            ->setSubject($fileName) //副标题
            ->setDescription($fileName)  //描述
            ->setKeywords($fileName) //关键字
            ->setCategory($fileName); //分类
        $objSheet = $spreadsheet->getActiveSheet();  //获取当前操作sheet的对象
        $objSheet->setTitle($fileName);  //设置当前sheet的标题

        $title      = $sheet['title'];
        $list       = $sheet['list'];
        $titleCount = count($title) - 1;
        $listCount  = count($list) - 1;
        //设置所有字体
        $spreadsheet->getDefaultStyle()->getFont()->setName('微软雅黑')->setSize(12);
        //默认行高 15
        $spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        //默认列宽 4
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);


        //合并单元格 表头
        $spreadsheet->getActiveSheet()->mergeCells('A1:' . $this->columns[$titleCount] . '1');
        //标题行高
        $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(40);
        //设置标题栏名称
        $spreadsheet->getActiveSheet()->setCellValue('A1', $fileName);
        //设置标题栏背景颜色
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFB9');
        //设置标题栏字体 加粗 字号
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setName('黑体')->setSize(22);


        //标题栏
        $titleKey = 0;
        foreach ($title as $titleVal) {
            //表头字体 加粗 字号
            $spreadsheet->getActiveSheet()->getStyle($this->columns[$titleKey] . '2')->getFont()->setName('黑体')->setSize(14)->setBold(true);
            //表头居中
            $spreadsheet->getActiveSheet()->getStyle($this->columns[$titleKey] . '2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            //表头背景
            $spreadsheet->getActiveSheet()->getStyle($this->columns[$titleKey] . '2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFB9');
            $objSheet->setCellValueByColumnAndRow($titleKey + 1, 2, $titleVal);
            $titleKey++;
        }

        $row = 3;
        foreach ($list as $item) {
            $column = 1;
            foreach ($title as $key => $value) {
                // 单元格内容写入
                $objSheet->setCellValueByColumnAndRow($column, $row, $item[$key]);
                $column++;
            }
            $row++;
        }


        //锁定表头
        $spreadsheet->getSheet(0)->freezePane('A3');

        //边框
        $styleArray = [
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN //细边框
                ],
                'outline'    => array(
                    'style' => Alignment::HORIZONTAL_CENTER, //居中
                )
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, //文字居中
                'vertical'   => Alignment::VERTICAL_CENTER //垂直居中
            ],

        ];
        $spreadsheet->getSheet(0)->getStyle('A1:' . "{$this->columns[$titleKey]}{$row}")->applyFromArray($styleArray);
        //下载
        $this->downloadExcel($spreadsheet, $fileName, 'Xlsx');
    }

}
