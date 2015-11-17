<?php

$arrReqData = array() ;
        
try
{
    if (!isset($_GET['name']))
    {
        throw new Exception("Missing Name") ;
    }
    $strName = $_GET['name'] ;
    $arrFiles = $_FILES[$strName] ;
    
    foreach ($arrFiles as $field => $row)
    {
        for ($i=0; $i<count($row); $i++)
        {
            if (!isset($arrReqData[$i]))
            {
                $objReqData = new stdClass() ;
                $objReqData->$field = $row[$i] ;
                $arrReqData[] = $objReqData ;
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
    $strUploadDir = $_SERVER['DOCUMENT_ROOT'].'/phone/image' ;
    $datetime     = date('YmdHis') ;
    $count        = 0 ;
    $arrData      = array() ;
    
    foreach ($arrReqData as $row)
    {
        if ($row->error != UPLOAD_ERR_OK)
        {
            continue ;
        }
        $arrFileName = explode('.', $row->name) ;
        $type = $arrFileName[1] ;
        $tmp_datetime = date('YmdHis') ;
        if ($tmp_datetime != $datetime)
        {
            $count = 0 ;
            $datetime = $tmp_datetime ;
            $name = $datetime.'-'.$count ;
        }
        else
        {
            $count++ ;
            $name = $datetime.'-'.$count ;
        }
        
        $rs = move_uploaded_file($row->tmp_name, "$strUploadDir/$name.$type") ;
        $objData = new stdClass() ;
        if ($rs == true)
        {
            $objData->message = 'Success' ;
            $objData->url = getenv('HTTP_CLIENT_IP')."/phone/image/$name.$type" ;
        }
        else
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
