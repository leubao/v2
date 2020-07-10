<?php
// +----------------------------------------------------------------------
// | LubTMP 报表导出
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19
// +----------------------------------------------------------------------
namespace Libs\Service;
class Exports{
	/**
	 * 一般导出导出方法
	 * Enter description here ...
	 * @param $fileName 导出文件名称
	 * @param $headArr 列表标题
	 * @param $data 表格数据
	 */
	function getExcelss($fileName,$headArr,$data){
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        import("Libs.Org.PHPExcel");
        import("Libs.Org.PHPExcel.Writer.Excel5");
        import("Libs.Org.PHPExcel.IOFactory.php");

        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();

        //设置表头
        $key = ord("A");
        //print_r($headArr);exit;
        foreach($headArr as $v){
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();

        //print_r($data);exit;
        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j.$column, $value);
                $span++;
            }
            $column++;
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        //$objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }
    /**
     * 一般导出导出方法
     * Enter description here ...
     * @param $fileName 导出文件名称
     * @param $headArr 列表标题
     * @param $data 表格数据
     */
    function getExcel($fileName,$headArr,$data){
        Vendor('PHPExcel.PHPExcel');
        $date = date("m_d");
        $fileName .= "_{$date}.xls";
        //创建PHPExcel对象，注意，不能少了\ 新建一个execl
        $objPHPExcel = new \PHPExcel();
        //获取当前活动sheet操作对象
        $objSheet = $objPHPExcel->getActiveSheet();
        //给当前sheet命名
        $objSheet->setTitle($fileName);
        //设置表头
        $key = ord("A");
        foreach($headArr as $v){
            $colum = chr($key);
            $objSheet->setCellValue($colum.'1', $v);
            $objSheet ->setCellValue($colum.'1', $v);
            $key += 1;
        }

        $column = 2;
        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);
                $objSheet->setCellValue($j.$column, $value);
                $span++;
            }
            $column++;
        }
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }
    /*
    *按模板导出
    *@param $type int 报表类型
    */
    function templateExecl($data,$templateName,$header,$type = null){
        Vendor('PHPExcel.PHPExcel');
        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        $temExcel= $objReader->load(SITE_PATH.'d/execl/'.$templateName.".xls");
        //获取当前活动的表
        $objActSheet = $temExcel->getActiveSheet();
        $a = 'A';$b = 'B';$c = 'C';$d = 'D';$e = 'E';$f = 'F';$g = 'G';$h = 'H';$i = 'I';$j = 'J';$k = 'K';$l = 'L';$m = 'M';
        //***********************画出单元格边框*****************************  
        $styleArray = array(  
            'borders' => array(  
                'allborders' => array(   
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,//细边框  
                    'color' => array('argb' => '000000'),  
                ),  
            ),  
        );
        switch ($type) {
            case '1':
                /* 渠道汇总表 */
                $objActSheet->setTitle ("渠道销售报表");
                $objActSheet->setCellValue ('A1',"渠道商销售(票型)汇总报表");
                $objActSheet->setCellValue ('B2',$header['datetime']);
                $objActSheet->setCellValue ('I2',date('Y-m-d H:i:s'));
                /* excel文件内容 */
                $zz = 5;
                //循环数据
                foreach ($data as $ks=>$va){
                    $objActSheet->setCellValue ($a.$zz, crmName($va['channel_id'],1));
                    $objActSheet->setCellValue ($i.$zz, $va['number']);
                    $objActSheet->setCellValue ($j.$zz, $va['money']);
                    $objActSheet->setCellValue ($k.$zz, $va['moneys']);
                    $objActSheet->setCellValue ($l.$zz, $va['rebate'] );
                    //设置合并的行数
                    $ss = $va['tic_num'] == '1' ? $zz : $zz+$va['tic_num']-1;
                    if($va['tic_num'] > '1'){
                        //合并单元格
                        $objActSheet->mergeCells($a.$zz.':'.$a.$ss);
                        $objActSheet->mergeCells($i.$zz.':'.$i.$ss);
                        $objActSheet->mergeCells($j.$zz.':'.$j.$ss);
                        $objActSheet->mergeCells($k.$zz.':'.$k.$ss);
                        $objActSheet->mergeCells($l.$zz.':'.$l.$ss);
                        $objActSheet->mergeCells($m.$zz.':'.$m.$ss);
                    }
                    //计算合计
                    $number += $va['number'];
                    $money += $va['money'];
                    $moneys += $va['moneys'];
                    $rebate += $va['rebate'];
                    foreach ($va['price'] as $ke => $da) {
                        /*详细 s*/
                        $ticketname = ticketName($da['price_id'],1);
                        $objActSheet->setCellValue ($b.$zz, $ticketname);
                        $objActSheet->setCellValue ($c.$zz, $da['price']);
                        $objActSheet->setCellValue ($d.$zz, $da['discount'] );
                        $objActSheet->setCellValue ($e.$zz, $da['number'] );
                        $objActSheet->setCellValue ($f.$zz, $da['money'] );
                        $objActSheet->setCellValue ($g.$zz, $da['moneys'] );
                        $objActSheet->setCellValue ($h.$zz, $da['rebate'] );
                        $objActSheet->getStyle($a.$zz.':'.$m.$zz)->applyFromArray($styleArray);
                        $zz += 1;   
                    }
                    $objActSheet->getStyle($a.$zz.':'.$m.$zz)->applyFromArray($styleArray);
                }
                $objActSheet->setCellValue ($h.$zz, '合计:' );
                $objActSheet->setCellValue ($i.$zz, $number);
                $objActSheet->setCellValue ($j.$zz, $money);
                $objActSheet->setCellValue ($k.$zz, $moneys);
                $objActSheet->setCellValue ($l.$zz, $rebate );
                $filename = "Lub_channel_sum_".time();
                break;
            case '2':
                $objActSheet->setTitle ("景区日报表");
                $objActSheet->setCellValue ('A1',"景区日报表");
                $objActSheet->setCellValue ('B2',$header['starttime']);
                $objActSheet->setCellValue ('F2',date('Y-m-d H:i:s'));
                /* excel文件内容 */
                $zz = 3;
                //循环数据
                foreach ($data as $ke=>$ve){
                    $objActSheet->setCellValue($a.$zz, "销售计划:");
                    $objActSheet->setCellValue($b.$zz, planShows($ke,1));
                    $objActSheet->mergeCells($b.$zz.':'.$h.$zz);
                    $objActSheet->getStyle($a.$zz.':'.$h.$zz)->applyFromArray($styleArray);
                    $zz = $zz+1;
                    $objActSheet->setCellValue($a.$zz, "票型名称");
                    $objActSheet->setCellValue($b.$zz, "票面单价");
                    $objActSheet->setCellValue($c.$zz, "结算单价");
                    $objActSheet->setCellValue($d.$zz, "数量");
                    $objActSheet->setCellValue($e.$zz, "票面金额");
                    $objActSheet->setCellValue($f.$zz, "结算金额");
                    $objActSheet->setCellValue($g.$zz, "差额");
                    $objActSheet->setCellValue($h.$zz, "备注");
                    //设置单元格背景色
                    $objActSheet->getStyle($a.$zz.':'.$h.$zz)->getFill()->getStartColor()->setARGB('EEEEEE');
                    //设置边框线
                    $objActSheet->getStyle($a.$zz.':'.$h.$zz)->applyFromArray($styleArray);
                    $zz = $zz+1;
                    foreach ($ve['price'] as $k=>$da){
                        if(!empty($da['price_id'])){
                            $ticketname = ticketName($da['price_id'],1);
                            $objActSheet->setCellValue ($a.$zz, $ticketname);
                            $objActSheet->setCellValue ($b.$zz, $da['price']);
                            $objActSheet->setCellValue ($c.$zz, $da['discount'] );
                            $objActSheet->setCellValue ($d.$zz, $da['number'] );
                            $objActSheet->setCellValue ($e.$zz, $da['money'] );
                            $objActSheet->setCellValue ($f.$zz, $da['moneys'] );
                            $objActSheet->setCellValue ($g.$zz, $da['rebate'] );
                            $objActSheet->getStyle($a.$zz.':'.$h.$zz)->applyFromArray($styleArray);
                            $zz=$zz+1;
                        }
                    }
                    $objActSheet->setCellValue ($c.$zz, '小计:' );
                    $objActSheet->setCellValue ($d.$zz, $ve['number']);
                    $objActSheet->setCellValue ($e.$zz, $ve['money']);
                    $objActSheet->setCellValue ($f.$zz, $ve['moneys']);
                    $objActSheet->setCellValue ($g.$zz, $ve['rebate'] );
                    //计算合计
                    $number += $ve['number'];
                    $money += $ve['money'];
                    $moneys += $ve['moneys'];
                    $rebate += $ve['rebate'];

                    $objActSheet->getStyle($a.$zz.':'.$h.$zz)->applyFromArray($styleArray);
                    $zz=$zz+1;
                }
                $objActSheet->setCellValue ($c.$zz, '合计:' );
                $objActSheet->setCellValue ($d.$zz, $number);
                $objActSheet->setCellValue ($e.$zz, $money);
                $objActSheet->setCellValue ($f.$zz, $moneys);
                $objActSheet->setCellValue ($g.$zz, $rebate );
                $objActSheet->getStyle($a.$zz.':'.$h.$zz)->applyFromArray($styleArray);
                $filename = "Lubtoday_scenic_".time();
                break;
            case '3':
            //渠道报表导出
                $objActSheet->setTitle ("渠道商统计表");
                $objActSheet->setCellValue ('A1',"渠道商销售(票型)统计(场次)明细报表");
                $objActSheet->setCellValue ('B2',$header['datetime']);
                $objActSheet->setCellValue ('I2',date('Y-m-d H:i:s'));
                /* excel文件内容 */
                $zz = 5;//起始行
                //循环数据
                foreach ($data as $ks=>$vs){
                    $ii = '0';
                    $number = 0;
                    $money = 0;
                    $moneys = 0;
                    $rebate = 0;
                    //设置渠道商
                    foreach ($vs['plan'] as $ke => $va) {
                        if($ii == '0'){
                            $objActSheet->setCellValue($a.$zz, "渠道商:".crmName($va['channel_id'],1));
                            $objActSheet->mergeCells($a.$zz.':'.$m.$zz); 
                        }
                        $zz += 1;
                        $iii = 0;
                        $ticNum = $va['tic_num'];
                        foreach ($va['price'] as $key => $da) {
                            //设置合并的行数
                            $ss = (int)$va['tic_num'] === 1 ? $zz : ($zz + $va['tic_num'] - 1);
                            if($iii == 0){
                                $objActSheet->setCellValue($a.$zz, planShows($va['plan'],1));
                                if((int)$va['tic_num'] > 1){
                                    //合并单元格
                                    $objActSheet->mergeCells($a.$zz.':'.$a.$ss);
                                }
                            }
                            //
                            $objActSheet->setCellValue ($i.$zz, $va['number']);
                            $objActSheet->setCellValue ($j.$zz, $va['money']);
                            $objActSheet->setCellValue ($k.$zz, $va['moneys']);
                            $objActSheet->setCellValue ($l.$zz, $va['rebate'] );
                            if($iii == 0 && (int)$va['tic_num'] > 1){
                               // var_dump($zz,$ss);
                                //合并单元格
                                $objActSheet->mergeCells($i.$zz.':'.$i.$ss);
                                $objActSheet->mergeCells($j.$zz.':'.$j.$ss);
                                $objActSheet->mergeCells($k.$zz.':'.$k.$ss);
                                $objActSheet->mergeCells($l.$zz.':'.$l.$ss);
                                $objActSheet->mergeCells($m.$zz.':'.$m.$ss);
                            }
                            /*详细 s*/
                            $ticketname = ticketName($da['price_id'],1);
                            $objActSheet->setCellValue ($b.$zz, $ticketname);
                            $objActSheet->setCellValue ($c.$zz, $da['price']);
                            $objActSheet->setCellValue ($d.$zz, $da['discount'] );
                            $objActSheet->setCellValue ($e.$zz, $da['number'] );
                            $objActSheet->setCellValue ($f.$zz, $da['money'] );
                            $objActSheet->setCellValue ($g.$zz, $da['moneys'] );
                            $objActSheet->setCellValue ($h.$zz, $da['rebate'] );
                            /*详细 e*/

                            $objActSheet->getStyle($a.$zz.':'.$m.$zz)->applyFromArray($styleArray);
                            if((int)$va['tic_num'] > 1 && ($ticNum - 1) > 0){
                                $zz += 1;
                            }
                            //var_dump($zz);
                            $ii++;
                            $iii++;
                            $ticNum--;
                        }
                        //计算合计
                        $number += $va['number'];
                        $money += $va['money'];
                        $moneys += $va['moneys'];
                        $rebate += $va['rebate'];
                        //计算总计
                        $Tnumber += $va['number'];
                        $Tmoney += $va['money'];
                        $Tmoneys += $va['moneys'];
                        $Trebate += $va['rebate'];
                        $objActSheet->getStyle($a.$zz.':'.$m.$zz)->applyFromArray($styleArray);
                        $ii++;
                    }
                    $zz+=1;
                    //单渠道商合计
                    $objActSheet->setCellValue ($h.$zz, '合计:' );
                    $objActSheet->setCellValue ($i.$zz, $number);
                    $objActSheet->setCellValue ($j.$zz, $money);
                    $objActSheet->setCellValue ($k.$zz, $moneys);
                    $objActSheet->setCellValue ($l.$zz, $rebate );
                    $objActSheet->getStyle($a.$zz.':'.$m.$zz)->applyFromArray($styleArray);
                    $zz++;
                }
                $objActSheet->setCellValue ($h.$zz, '总计:' );
                $objActSheet->setCellValue ($i.$zz, $Tnumber);
                $objActSheet->setCellValue ($j.$zz, $Tmoney);
                $objActSheet->setCellValue ($k.$zz, $Tmoneys);
                $objActSheet->setCellValue ($l.$zz, $Trebate );
                $objActSheet->getStyle($a.$zz.':'.$m.$zz)->applyFromArray($styleArray);
                $filename = "Lub_channl_detail_".time();
                break;
            case '4':
                /* 售票员日报表 */
                $objActSheet->setTitle ("售票员日报表");
                $objActSheet->setCellValue ('A1',"售票员日报表");
                $objActSheet->setCellValue ('B2',$header['datetime']);
                $objActSheet->setCellValue ('F2',userName($header['user'],1,1));
                $objActSheet->setCellValue ('I2',date('Y-m-d H:i:s'));
                /* excel文件内容 */
                $zz = 5;
                //循环数据
                foreach ($data as $ks=>$va){
                    $objActSheet->setCellValue ($a.$zz, planShows($va['plan']));
                    $objActSheet->setCellValue ($i.$zz, $va['number']);
                    $objActSheet->setCellValue ($j.$zz, $va['money']);
                    $objActSheet->setCellValue ($k.$zz, $va['moneys']);
                    $objActSheet->setCellValue ($l.$zz, $va['rebate'] );
                    //设置合并的行数
                    $ss = $va['tic_num'] == '1' ? $zz : $zz+$va['tic_num']-1;
                    if($va['tic_num'] > '1'){
                        //合并单元格
                        $objActSheet->mergeCells($a.$zz.':'.$a.$ss);
                        $objActSheet->mergeCells($i.$zz.':'.$i.$ss);
                        $objActSheet->mergeCells($j.$zz.':'.$j.$ss);
                        $objActSheet->mergeCells($k.$zz.':'.$k.$ss);
                        $objActSheet->mergeCells($l.$zz.':'.$l.$ss);
                        $objActSheet->mergeCells($m.$zz.':'.$m.$ss);
                    }

                    //计算合计
                    $number += $va['number'];
                    $money += $va['money'];
                    $moneys += $va['moneys'];
                    $rebate += $va['rebate'];
                    foreach ($va['price'] as $ke => $da) {
                        /*详细 s*/
                        $ticketname = ticketName($da['price_id'],1);
                        $objActSheet->setCellValue ($b.$zz, $ticketname);
                        $objActSheet->setCellValue ($c.$zz, $da['price']);
                        $objActSheet->setCellValue ($d.$zz, $da['discount'] );
                        $objActSheet->setCellValue ($e.$zz, $da['number'] );
                        $objActSheet->setCellValue ($f.$zz, $da['money'] );
                        $objActSheet->setCellValue ($g.$zz, $da['moneys'] );
                        $objActSheet->setCellValue ($h.$zz, $da['rebate'] );
                        $objActSheet->getStyle($a.$zz.':'.$m.$zz)->applyFromArray($styleArray);  
                        $zz += 1; 
                        
                    }
                    $objActSheet->getStyle($a.$zz.':'.$m.$zz)->applyFromArray($styleArray);
                }
                $objActSheet->setCellValue ($h.$zz, '合计:' );
                $objActSheet->setCellValue ($i.$zz, $number);
                $objActSheet->setCellValue ($j.$zz, $money);
                $objActSheet->setCellValue ($k.$zz, $moneys);
                $objActSheet->setCellValue ($l.$zz, $rebate );
                $filename = "Lub_operator_".time();
                break;
            case '5':
                //票型销售统计
                $objActSheet->setTitle ("票型销售统计");
                $objActSheet->setCellValue ('B2',$header['datetime']);
                $objActSheet->setCellValue ('G2',date('Y-m-d H:i:s'));
                $zz = 4;
                foreach ($data['price'] as $ke => $da) {
                    $objActSheet->setCellValue ($a.$zz, $da['name']);
                    $objActSheet->setCellValue ($b.$zz, $da['price']);
                    $objActSheet->setCellValue ($c.$zz, $da['discount'] );
                    $objActSheet->setCellValue ($d.$zz, $da['number'] );
                    $objActSheet->setCellValue ($e.$zz, $da['money'] );
                    $objActSheet->setCellValue ($f.$zz, $da['moneys'] );
                    $objActSheet->setCellValue ($g.$zz, $da['rebate'] );
                    $objActSheet->getStyle($a.$zz.':'.$h.$zz)->applyFromArray($styleArray);
                    $zz += 1;

                }
                $objActSheet->setCellValue ($c.$zz, '合计:' );
                $objActSheet->setCellValue ($d.$zz, $data['info']['number'] );
                $objActSheet->setCellValue ($e.$zz, $data['info']['money'] );
                $objActSheet->setCellValue ($f.$zz, $data['info']['moneys'] );
                $objActSheet->setCellValue ($g.$zz, $data['info']['rebate'] );
                $zz += 1;
                $objActSheet->setCellValue ($c.$zz, '累计场次:' );
                $objActSheet->setCellValue ($d.$zz, $data['info']['games'] );
                $objActSheet->getStyle($a.$zz.':'.$h.$zz)->applyFromArray($styleArray);
                $filename = "Lub_tickets_".time();
                break;
            case '6':
                //票型销售统计场次统计
                break;
            case '7':
                /* excel文件内容 */
                $zz = 4;
                $objActSheet->setCellValue ('A2', $data['starttime'] );
                $objActSheet->setCellValue ('C2', date('Y-m-d H:s',$data['createtime']));
                $info = unserialize($data['info']);
                foreach ($info as $ke=>$ve){
                    $objActSheet->setCellValue ($a.$zz, $ve['name']);
                    $objActSheet->setCellValue ($b.$zz, $ve['money']);
                    $zz=$zz+1;
                }
                $filename = "Lubtoday_credit_".time();
                break;
            case '8':
                /* 渠道月度汇总表 */
                $objActSheet->setTitle ("渠道商销售统计报表");
                $objActSheet->setCellValue ('A1',"渠道商销售(票型)统计(月度)报表");
                $objActSheet->setCellValue ('B2',$header['datetime']);
                $objActSheet->setCellValue ('I2',date('Y-m-d H:i:s'));
                /* excel文件内容 */
                $zz = 5;
                //循环数据
                foreach ($data as $ks=>$va){
                    $objActSheet->setCellValue ($a.$zz, crmName($va['channel_id'],1));
                    $objActSheet->setCellValue ($i.$zz, $va['number']);
                    $objActSheet->setCellValue ($j.$zz, $va['money']);
                    $objActSheet->setCellValue ($k.$zz, $va['moneys']);
                    $objActSheet->setCellValue ($l.$zz, $va['rebate'] );
                    //设置合并的行数
                    $ss = $va['tic_num'] == '1' ? $zz : $zz+$va['tic_num']-1;
                    if($va['tic_num'] > '1'){
                        //合并单元格
                        $objActSheet->mergeCells($a.$zz.':'.$a.$ss);
                        $objActSheet->mergeCells($i.$zz.':'.$i.$ss);
                        $objActSheet->mergeCells($j.$zz.':'.$j.$ss);
                        $objActSheet->mergeCells($k.$zz.':'.$k.$ss);
                        $objActSheet->mergeCells($l.$zz.':'.$l.$ss);
                        $objActSheet->mergeCells($m.$zz.':'.$m.$ss);
                    }
                    //计算合计
                    $number += $va['number'];
                    $money += $va['money'];
                    $moneys += $va['moneys'];
                    $rebate += $va['rebate'];
                    foreach ($va['price'] as $ke => $da) {
                        /*详细 s*/
                        $ticketname = ticketName($da['price_id'],1);
                        $objActSheet->setCellValue ($b.$zz, $ticketname);
                        $objActSheet->setCellValue ($c.$zz, $da['price']);
                        $objActSheet->setCellValue ($d.$zz, $da['discount'] );
                        $objActSheet->setCellValue ($e.$zz, $da['number'] );
                        $objActSheet->setCellValue ($f.$zz, $da['money'] );
                        $objActSheet->setCellValue ($g.$zz, $da['moneys'] );
                        $objActSheet->setCellValue ($h.$zz, $da['rebate'] );
                        $objActSheet->getStyle($a.$zz.':'.$m.$zz)->applyFromArray($styleArray);
                        $zz += 1;   
                    }
                    $objActSheet->getStyle($a.$zz.':'.$m.$zz)->applyFromArray($styleArray);
                }
                $objActSheet->setCellValue ($h.$zz, '合计:' );
                $objActSheet->setCellValue ($i.$zz, $number);
                $objActSheet->setCellValue ($j.$zz, $money);
                $objActSheet->setCellValue ($k.$zz, $moneys);
                $objActSheet->setCellValue ($l.$zz, $rebate );
                $filename = "Lub_channel_sum_".time();
                break;
            default:
                # code...
                break;
        }  
        header('Content-Type: application/vnd.ms-excel' );
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"' );
        header('Cache-Control: max-age=0' );
        $objWriter = \PHPExcel_IOFactory::createWriter ($temExcel, 'Excel5' );
        $objWriter->save ('php://output');
    }
	
}