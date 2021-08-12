<?php

namespace App\Utils;

class ControllerUtils
{
    public static function getDataLink($endpoint,string $method="GET",string $params=null) : array
    {
        $link = [
            "href"=>"api/v1/" . $endpoint,
            "method"=>$method,
        ];
        if($params != null)
            $link['params'] = $params;
        return $link;
    }

    public static function mapDataToResponse(array $data,string $msg) : array
    {
        $key = array_keys($data)[0];
        return [
            'msg' => $msg,
            $key => $data[$key]
        ];
    }
}
