<?php

function bot($method,$datas=[]){

    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    
    if(curl_error($ch)){

        var_dump(curl_error($ch));

    }else{

        return json_decode($res);

    }

}

function temp($cid, $temp){

    global $conn;

    if($temp == null){

        $conn->query("UPDATE users SET temp = null WHERE user_id = '$cid'");

    }else{

        $conn->query("UPDATE users SET temp = '$temp' WHERE user_id = '$cid'");

    }

}

?>