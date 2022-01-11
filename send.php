<?php

// Ini Set [Turn off Display errors and reports]
ini_set('error_reporting', 'off');
ini_set('display_errors', 'off');
ini_set('display_startup_errors', 'off');

// SQL Info
$servername = "localhost";
$username = "user";
$password = "3472004jabab";
$dbname = "configsuz";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {

    echo "Connection Error: " . $conn->connect_error;

    exit();

}

// Time Zone
date_default_timezone_set("asia/Tashkent");
$time = date('H:i');

// Table ID
$insert_id = file_get_contents("insert_id");

// Get Informations from DB
$row = $conn->query("SELECT * FROM send WHERE ID = '$insert_id'")->fetch_assoc();

$token = $row['Token'];
$admin_id = $row['Admin_ID'];

$sended = $row['Send'];
$nosended = $row['NoSend'];

$channel_id = $row['Channel_ID'];
$post_id = $row['Post_ID'];
$b_name = $row['B_Name'];
$b_link = $row['B_Link'];

$status = $row['Status'];
$last_id = $row['Last_ID'];
$from_id = $row['From_ID'];
$sort_num = 100;

if($insert_id !== null){

    if($status == "Waiting"){

        $keyb = json_encode([
            'inline_keyboard'=>[
                [['text'=>"$b_name",'url'=>"$b_link"]],
            ],
        ]);

        $sort = $conn->query("SELECT * FROM users ORDER BY ID LIMIT $from_id, $sort_num");

        if($sort->num_rows > 0){

            $i = 0;

            while($sort_row = $sort->fetch_assoc()){

                $i++;

                $user_id = $sort_row['user_id'];

                if($last_id == $user_id){

                    $send_bot = file_get_contents("https://api.telegram.org/bot" . $token . "/copyMessage?from_chat_id=" . $channel_id . "&chat_id=" . $user_id . "&message_id=" . $post_id . "&reply_markup=" . $keyb);
                    $json = json_decode($send_bot);

                    if($json->ok == true){

                        $sended = $conn->query("SELECT * FROM send WHERE ID = '$insert_id'")->fetch_assoc()['Send'];
                        $sended++;
                        $conn->query("UPDATE Send SET send = '$sended' WHERE ID = '$insert_id'");

                    }else{

                        $nosended = $conn->query("SELECT * FROM send WHERE ID = '$insert_id'")->fetch_assoc()['NoSend'];
                        $nosended++;
                        $conn->query("UPDATE send SET NoSend = '$nosended' WHERE ID = '$insert_id'");

                    }

                    $txt = "<b>Yuborish yakunlandi!</b> <i>To'liq ma'lumot:</i> /sendinfo_$insert_id";
                    file_get_contents("https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $admin_id . "&text=" . $txt . "&parse_mode=html");

                    $conn->query("UPDATE send SET End_Time = '$date $time' WHERE ID = '$insert_id'");
                    $conn->query("UPDATE send SET Status = 'Successfully' WHERE ID = '$insert_id'");

                    unlink("insert_id");

                }else{

                    $send_bot = file_get_contents("https://api.telegram.org/bot" . $token . "/copyMessage?from_chat_id=" . $channel_id . "&chat_id=" . $user_id . "&message_id=" . $post_id . "&reply_markup=" . $keyb);
                    $json = json_decode($send_bot);

                    if($json->ok == true){

                        $sended = $conn->query("SELECT * FROM send WHERE ID = '$insert_id'")->fetch_assoc()['Send'];
                        $sended++;
                        $conn->query("UPDATE send SET Send = '$sended' WHERE ID = '$insert_id'");

                    }else{

                        $nosended = $conn->query("SELECT * FROM send WHERE ID = '$insert_id'")->fetch_assoc()['NoSend'];
                        $nosended++;
                        $conn->query("UPDATE send SET NoSend = '$nosended' WHERE ID = '$insert_id'");

                    }

                }

            }

            $txt = "Yuborildi: <b>$i ta</b>";
            file_get_contents("https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $admin_id . "&text=" . $txt . "&parse_mode=html");

            $from_id = $from_id + $sort_num;
            $conn->query("UPDATE Send SET From_ID = '$from_id' WHERE ID = '$insert_id'");

        }

    }else{

        exit();

    }

}else{

    exit();

}

?>