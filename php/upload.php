<?php

$arrReqData = array() ;
        
try
{
    if (!isset($_GET['name']))
    {
        throw new Exception("Missing Name") ;
    }
    $strName  = $_GET['name'] ;
    $arrFiles = $_FILES[$strName] ;
    
    foreach ($arrFiles as $field => $row) //調整$_FILES格式為JSON物件的格式
    {
        for ($i=0; $i<count($row); $i++)
        {
            if (!isset($arrReqData[$i]))
            {
                $objReqData         = new stdClass() ;
                $objReqData->$field = $row[$i] ;
                $arrReqData[]       = $objReqData ;
            }
            else
            {
                $arrReqData[$i]->$field = $row[$i] ;
            }
        }
    }
}
catch (Exception $e)
{
    $this->http->halt(400) ;
    exit(0) ;
}

try
{
    $strUploadDir = '/image' ; //設定儲存圖片的路徑(目錄)
    $datetime     = date('YmdHis') ;
    $count        = 0 ;
    $arrData      = array() ;
    
    foreach ($arrReqData as $row) //上傳每張圖片並且為檔案重新命名
    {
        if ($row->error != UPLOAD_ERR_OK)
        {
            continue ;
        }
        $arrFileName  = explode('.', $row->name) ;
        $type         = $arrFileName[1] ;
        $tmp_datetime = date('YmdHis') ;
        if ($tmp_datetime != $datetime)
        {
            $count = 0 ;
            $datetime = $tmp_datetime ;
            $name     = $datetime.'-'.$count ;
        }
        else
        {
            $count++ ;
            $name = $datetime.'-'.$count ;
        }
        
        $rs = move_uploaded_file($row->tmp_name, "$strUploadDir/$name.$type") ; //上傳檔案
        $objData = new stdClass() ;
        if ($rs == true) //上傳成功則回傳成功訊息並且加上圖檔URL
        {
            $objData->message = 'Success' ;
            $objData->url     = "/image/$name.$type" ;
        }
        else  // 上傳失敗則回傳失敗訊息
        {
            $objData->message = 'Fail' ;
        }
        $arrData[] = $objData ;
    }
    echo json_encode($arrData) ;
}
catch(Exception $e)
{
    $this->http->halt(400) ;
    exit(0) ;
}
