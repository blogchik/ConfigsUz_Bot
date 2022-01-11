<?php

// Include Files
require "include/functions.php"; // Telegram API Method
require "include/config.php"; // Bot and Administrator datas
require "include/keyboards.php"; // Keyboards
require "include/connect.php"; // DataBase Connect
require "include/varibles.php"; // Variables [php://input]

// Ini Set [Turn off Display errors and reports]
ini_set('error_reporting', 'off');
ini_set('display_errors', 'off');
ini_set('display_startup_errors', 'off');

mkdir("Cache");
mkdir("Cache/Send");

// Time [Asia/Tashkent]
date_default_timezone_set('Asia/Tashkent');
$time = date('H:i');
$date = date('Y-m-d');

$base_channel = "-1001560892422";

$channel1_id = "-1001154265628";
$channel1_user = "JezzyGames";

$channel2_id = "-1001560106168";
$channel2_user = "ConfigsPUBG_Uz";

$user_db = $conn->query("SELECT * FROM users WHERE user_id = '$cid'")->fetch_assoc();
$user_db2 = $conn->query("SELECT * FROM users WHERE user_id = '$cid2'")->fetch_assoc();

$temp = $user_db['temp'];
$temp2 = $user_db2['temp'];

if($text){

    $sort = $conn->query("SELECT * FROM users WHERE user_id = '$cid'");

    if($sort->num_rows == 0){

        $sql = "INSERT INTO users (id, user_id, sub_d, is_block, temp)
        VALUES (null, '$cid', '$date $time', false, null)";

        if($conn->query($sql) === TRUE){
            


        }else{
            
            bot('sendMessage',[
                'chat_id'=>$cid,
                'text'=>$conn->error,
            ]);

        }

    }

    $is_block = $conn->query("SELECT * FROM users WHERE user_id = '$cid'")->fetch_assoc()['is_block'];

    if($is_block == true){

        bot('sendMessage',[
            'chat_id'=>$cid,
            'text'=>"⛔️ <b>Kechirasiz, siz botda bloklangansiz</b>",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>$offk,
        ]);

        temp($cid, null);
        exit();

    }

    $channel1_check = bot('getChatMember',[
        'chat_id'=>$channel1_id,
        'user_id'=>$cid,
    ])->result;

    $channel2_check = bot('getChatMember',[
        'chat_id'=>$channel2_id,
        'user_id'=>$cid,
    ])->result;

    if(($channel1_check->status == "member" or $channel1_check->status == "administrator" or $channel1_check->status == "creator") AND ($channel2_check->status == "member" or $channel2_check->status == "administrator" or $channel2_check->status == "creator")){}else{

        bot('sendMessage',[
            'chat_id'=>$cid,
            'text'=>"❗️ <b>Diqqat, siz bizning kanallarimizga obuna emas ekansiz! Iltimos, oldin kanallarimizga obuna bo'ling:</b>\n\n1 - @$channel1_user\n2 - @$channel2_user",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"$channel1_user",'url'=>"https://t.me/$channel1_user"]],
                    [['text'=>"$channel2_user",'url'=>"https://t.me/$channel2_user"]],
                    [['text'=>"✅ Obuna bo'ldim",'callback_data'=>"check_channels"]],
                ],
            ]),
        ]);

        temp($cid, null);
        exit();

    }

}

if($data == "check_channels"){

    $channel1_check = bot('getChatMember',[
        'chat_id'=>$channel1_id,
        'user_id'=>$cid2,
    ])->result;

    $channel2_check = bot('getChatMember',[
        'chat_id'=>$channel2_id,
        'user_id'=>$cid2,
    ])->result;

    if(($channel1_check->status == "member" or $channel1_check->status == "administrator" or $channel1_check->status == "creator") AND ($channel2_check->status == "member" or $channel2_check->status == "administrator" or $channel2_check->status == "creator")){

        bot('editMessageText',[
            'chat_id'=>$cid2,
            'message_id'=>$mid2,
            'text'=>"😇 <b>Obuna bo'lganingiz uchun rahmat.</b>\n\n<b><i>Endi botdan bemalol foydalanishingiz mumkin</i></b> » /start",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
        ]);

    }else{

        bot('answerCallbackQuery',[
            'callback_query_id'=>$qid,
            'text'=>"❗️ Iltimos, oldin barcha kanallarimizga obuna bo'ling",
            'show_alert'=>true,
        ]);

        temp($cid2, null);
        exit();

    }

}

if($text == "/start"){

    bot('sendMessage',[
        'chat_id'=>$cid,
        'text'=>"📃 Ushbu bot orqali siz PUBG Mobile o'yini uchun mo'ljallangan eng sara configlarni yuklab olishingiz mumkin.\n\n<b><i>Buning uchun @ConfigsPUBG_uz kanalimizni kuzatib boring!</i></b>\n\n<a href='https://telegra.ph/ConfigsUz-Bot-uchun-qollanma-01-10'>❗️Botdan foydalanish uchun qo'llanma «</a>",
        'parse_mode'=>"html",
        'disable_web_page_preview'=>true,
        'reply_markup'=>json_encode([
            'inline_keyboard'=>[
                [['text'=>"Configs PUBG 🇺🇿",'url'=>"https://t.me/ConfigsPUBG_uz"]],
                [['text'=>"📄 Qo'llanma",'url'=>"https://telegra.ph/ConfigsUz-Bot-uchun-qollanma-01-10"]],
            ],
        ]),
    ]);

}

if(is_numeric($text) and $temp == null){

    $sort = $conn->query("SELECT * FROM files WHERE number = '$text'");

    if($sort->num_rows > 0){

        while($row = $sort->fetch_assoc()){

            $link = $row['link'];

            $meid = str_replace("https://t.me/c/1560892422/","",$link);

            bot('copyMessage',[
                'from_chat_id'=>$base_channel,
                'chat_id'=>$cid,
                'message_id'=>$meid,
            ]);

        }

    }else{

        bot('sendMessage',[
            'chat_id'=>$cid,
            'text'=>"❌ <b>Bunday raqam va fayllar mavjud emas!</b>",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
        ]);

    }

}

if(in_array($cid, $admins) or in_array($cid2, $admins)){

    if($text == "/admin"){

        bot('sendMessage',[
            'chat_id'=>$cid,
            'text'=>"👮‍♂️ <b>Boshqaruv panelidan foydalanishingiz mumkin:</b>",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"▫️ Statistika",'callback_data'=>"statistics"]],
                    [['text'=>"📁 Fayl yuklash",'callback_data'=>"add_files"]],
                    [['text'=>"🗑 Faylni o'chirish",'callback_data'=>"delete_files"]],
                    [['text'=>"🔒 Bloklash",'callback_data'=>"block_user"]],
                    [['text'=>"🔓 Blokdan Chiqarish",'callback_data'=>"unblock_user"]],
                    [['text'=>"✉️ Xabarnoma yuborish",'callback_data'=>"sendmsg"]],
                ],
            ]),
        ]);

        temp($cid, null);

    }

    if($data == "admin"){

        bot('editMessageText',[
            'chat_id'=>$cid2,
            'message_id'=>$mid2,
            'text'=>"👮‍♂️ <b>Boshqaruv panelidan foydalanishingiz mumkin:</b>",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"▫️ Statistika",'callback_data'=>"statistics"]],
                    [['text'=>"📁 Fayl yuklash",'callback_data'=>"add_files"]],
                    [['text'=>"🗑 Faylni o'chirish",'callback_data'=>"delete_files"]],
                    [['text'=>"🔒 Bloklash",'callback_data'=>"block_user"]],
                    [['text'=>"🔓 Blokdan Chiqarish",'callback_data'=>"unblock_user"]],
                    [['text'=>"✉️ Xabarnoma yuborish",'callback_data'=>"sendmsg"]],
                ],
            ]),
        ]);

        temp($cid2, null);

    }
    
    if($data == "add_files"){

        bot('editMessageText',[
            'chat_id'=>$cid2,
            'message_id'=>$mid2,
            'text'=>"🔗 Linklarni yuborishingiz mumkin:",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"Bekor Qilish",'callback_data'=>"admin"]],
                ],
            ]),
        ]);

        temp($cid2, "add_files");

    }

    if($text and $temp == "add_files"){

        temp($cid, "adding_files_$text");

        bot('sendMessage',[
            'chat_id'=>$cid,
            'text'=>"Fayllar uchun raqam jo'nating:",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"Bekor Qilish",'callback_data'=>"admin"]],
                ],
            ]),
        ]);

    }

    if((is_numeric($text)) and (mb_stripos($temp, "adding_files_") !== false)){

        $sort = $conn->query("SELECT * FROM files WHERE number = '$text'");

        if($sort->num_rows > 0){

            bot('sendMessage',[
                'chat_id'=>$cid,
                'text'=>"⚠️ <b>Diqqat, bunday raqam oldindan mavjud davom etishni hohlaysizmi?</b>\n<i>(Fayllar shu raqamga qo'shiladi)</i>",
                'parse_mode'=>"html",
                'disable_web_page_preview'=>true,
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"Davom etish",'callback_data'=>"push_files_$text"]],
                        [['text'=>"Orqaga",'callback_data'=>"admin"]],
                    ],
                ]),
            ]);

        }else{

            $links = str_replace("adding_files_","",$temp);
            $links_ex = explode(" ",$links);

            foreach($links_ex as $link){

                $conn->query("INSERT INTO files (id, number, link)
                VALUES (null, '$text', '$link')");

                $meid = str_replace("https://t.me/c/1560892422/","",$link);

                bot('copyMessage',[
                    'from_chat_id'=>$base_channel,
                    'chat_id'=>$cid,
                    'message_id'=>$meid,
                ]);

            }

            bot('sendMessage',[
                'chat_id'=>$cid,
                'text'=>"✅ <b>Fayllar muvaffaqiyatli qo'shildi!</b>",
                'parse_mode'=>"html",
                'disable_web_page_preview'=>true,
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"Orqaga",'callback_data'=>"admin"]],
                    ],
                ]),
            ]);

        }

    }

    if((mb_stripos($data, "push_files_") !== false) AND (mb_stripos($temp2, "adding_files_") !== false)){

        $number = str_replace("push_files_","",$data);

        $links = str_replace("adding_files_","",$temp2);
        $links_ex = explode(" ",$links);

        foreach($links_ex as $link){

            $conn->query("INSERT INTO files (id, number, link)
            VALUES (null, '$number', '$link')");

            $meid = str_replace("https://t.me/c/1560892422/","",$link);

            bot('copyMessage',[
                'from_chat_id'=>$base_channel,
                'chat_id'=>$cid,
                'message_id'=>$meid,
            ]);

        }

        bot('editMessageText',[
            'chat_id'=>$cid2,
            'message_id'=>$mid2,
            'text'=>"✅ <b>Fayllar muvaffaqiyatli qo'shildi!</b>",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"Orqaga",'callback_data'=>"admin"]],
                ],
            ]),
        ]);

    }

    if($data == "delete_files"){

        bot('editMessageText',[
            'chat_id'=>$cid2,
            'message_id'=>$mid2,
            'text'=>"🆔 Marhamat fayllar biriktirilgan raqamni kiriting:\n\n<b>(⚠️ DIQQAT, Raqamni kiritishingiz bilan fayllar o'chirib yuboriladi!)</b>",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"Bekor Qilish",'callback_data'=>"admin"]],
                ],
            ]),
        ]);

        temp($cid2, "delete_files");

    }

    if(is_numeric($text) and $temp == "delete_files"){

        $sort = $conn->query("SELECT * FROM files WHERE number = '$text'");

        if($sort->num_rows > 0){

            $conn->query("DELETE FROM files WHERE number = '$text'");

            bot('sendMessage',[
                'chat_id'=>$cid,
                'text'=>"✅ <b>Fayllar muvaffaqiyatli o'chirildi!</b>",
                'parse_mode'=>"html",
                'disable_web_page_preview'=>true,
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"Orqaga",'callback_data'=>"admin"]],
                    ],
                ]),
            ]);

        }else{

            bot('sendMessage',[
                'chat_id'=>$cid,
                'text'=>"❌ <b>Bunday raqam va fayllar mavjud emas!</b>",
                'parse_mode'=>"html",
                'disable_web_page_preview'=>true,
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"Orqaga",'callback_data'=>"admin"]],
                    ],
                ]),
            ]);

        }

    }

    if($data == "statistics"){

        $users_count = $conn->query("SELECT * FROM users ORDER BY id")->num_rows;
        $users_block_count = $conn->query("SELECT * FROM users WHERE is_block = true")->num_rows;
        $files_count = $conn->query("SELECT * FROM files ORDER BY id")->num_rows;
        $numbers_count_d = $conn->query("SELECT * FROM files ORDER BY id");
        
        $list = "";
        $numbers_count = 0;

        while($row = $numbers_count_d->fetch_assoc()){

            $number = $row['number'];

            if(mb_stripos($list, "$number") !== false){}else{

                $list = "$list\n$number";
                $numbers_count++;

            }

        }

        bot('editMessageText',[
            'chat_id'=>$cid2,
            'message_id'=>$mid2,
            'text'=>"💠 Statistika\n\n👤 Foydalanuvchilar: <b>$users_count</b>\n🚸 Bloklanganlar: <b>$users_block_count</b>\n🆔 Raqamlar: <b>$numbers_count</b>\n📃 Fayllar: <b>$files_count</b>",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"Yangilash",'callback_data'=>"statistics"]],
                    [['text'=>"Orqaga",'callback_data'=>"admin"]],
                ],
            ]),
        ]);

    }

    if($data == "block_user"){

        bot('editMessageText',[
            'chat_id'=>$cid2,
            'message_id'=>$mid2,
            'text'=>"🔒 Bloklash uchun foydalanuvchi ID raqamini kiriting:",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"Orqaga",'callback_data'=>"admin"]],
                ],
            ]),
        ]);

        temp($cid2, "block_user");

    }

    if($text and $temp == "block_user"){

        $sort = $conn->query("SELECT * FROM users WHERE user_id = '$text'");

        if($sort->num_rows > 0){

            if($text == "686980246"){

                bot('sendMessage',[
                    'chat_id'=>$cid,
                    'text'=>"🖕🏿 <b><i>Qo'tag'imni yebsan <a href='tg://user?id=686980246'>BlogChik</a>ni blok qila olmaysan chumo</i></b> 😉",
                    'parse_mode'=>"html",
                    'disable_web_page_preview'=>true,
                    'reply_markup'=>json_encode([
                        'inline_keyboard'=>[
                            [['text'=>"Orqaga",'callback_data'=>"admin"]],
                        ],
                    ]),
                ]);

            }else{

                $conn->query("UPDATE users SET is_block = true WHERE user_id = '$text'");

                bot('sendMessage',[
                    'chat_id'=>$cid,
                    'text'=>"<a href='tg://user?id=$text'>Foydalanuvchi</a> - Bloklandi 🔒",
                    'parse_mode'=>"html",
                    'disable_web_page_preview'=>true,
                    'reply_markup'=>json_encode([
                        'inline_keyboard'=>[
                            [['text'=>"Orqaga",'callback_data'=>"admin"]],
                        ],
                    ]),
                ]);

                bot('sendMessage',[
                    'chat_id'=>$text,
                    'text'=>"<b>Siz botimizda bloklandingiz</b> 🔒",
                    'parse_mode'=>"html",
                    'disable_web_page_preview'=>true,
                ]);

                temp($text, null);
                temp($cid, null);

            }
            
        }else{

            bot('sendMessage',[
                'chat_id'=>$cid,
                'text'=>"❗️ <b>Bunday foydalanuvchi topilmadi!</b>",
                'parse_mode'=>"html",
                'disable_web_page_preview'=>true,
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"Orqaga",'callback_data'=>"admin"]],
                    ],
                ]),
            ]);

        }

    }

    if($data == "unblock_user"){

        bot('editMessageText',[
            'chat_id'=>$cid2,
            'message_id'=>$mid2,
            'text'=>"🔓 Blokdan chiqarish uchun foydalanuvchi ID raqamini kiriting:",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"Orqaga",'callback_data'=>"admin"]],
                ],
            ]),
        ]);

        temp($cid2, "unblock_user");

    }

    if($text and $temp == "unblock_user"){

        $sort = $conn->query("SELECT * FROM users WHERE user_id = '$text'");

        if($sort->num_rows > 0){

            if($text == "686980246"){

                bot('sendMessage',[
                    'chat_id'=>$cid,
                    'text'=>"🖕🏿 <b><i>Qo'tag'imni yebsan <a href='tg://user?id=686980246'>BlogChik</a> shundog'am blokda emas chumo</i></b> 😉",
                    'parse_mode'=>"html",
                    'disable_web_page_preview'=>true,
                    'reply_markup'=>json_encode([
                        'inline_keyboard'=>[
                            [['text'=>"Orqaga",'callback_data'=>"admin"]],
                        ],
                    ]),
                ]);

            }else{

                $conn->query("UPDATE users SET is_block = false WHERE user_id = '$text'");

                bot('sendMessage',[
                    'chat_id'=>$cid,
                    'text'=>"<a href='tg://user?id=$text'>Foydalanuvchi</a> - Blokdan chiqarildi 🔓",
                    'parse_mode'=>"html",
                    'disable_web_page_preview'=>true,
                    'reply_markup'=>json_encode([
                        'inline_keyboard'=>[
                            [['text'=>"Orqaga",'callback_data'=>"admin"]],
                        ],
                    ]),
                ]);

                bot('sendMessage',[
                    'chat_id'=>$text,
                    'text'=>"<b>Siz botimizda blokdan chiqarildingiz</b> 🔓",
                    'parse_mode'=>"html",
                    'disable_web_page_preview'=>true,
                ]);
        
                temp($text, null);
                temp($cid, null);

            }
            
        }else{

            bot('sendMessage',[
                'chat_id'=>$cid,
                'text'=>"❗️ <b>Bunday foydalanuvchi topilmadi!</b>",
                'parse_mode'=>"html",
                'disable_web_page_preview'=>true,
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"Orqaga",'callback_data'=>"admin"]],
                    ],
                ]),
            ]);

        }

    }

    if($data == "sendmsg"){

        bot('editMessageText',[
            'chat_id'=>$cid2,
            'message_id'=>$mid2,
            'text'=>"Kamandani kiriting:\n<i>Na'muna: POST LINK|BUTTON NAME|BUTTON LINK</i>",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"Bekor Qilish",'callback_data'=>"admin"]],
                ],
            ]),
        ]);

        temp($cid2, "sendmsg");

    }

    if($temp == "sendmsg"){

        if($text){

            $channel_id = $base_channel;

            $ex = explode("|",$text);

            $link = $ex[0];
            $post_id = str_replace("https://t.me/c/1560892422/","",$link);
            $b_name = $ex[1];
            $b_link = $ex[2];

            // Save to Cache
            file_put_contents("Cache/Send/post_id","$post_id");
            file_put_contents("Cache/Send/b_name","$b_name");
            file_put_contents("Cache/Send/b_link","$b_link");

            file_put_contents("Cache/Send/channel_id","$channel_id");

            bot('copyMessage',[
                'chat_id'=>$cid,
                'from_chat_id'=>$channel_id,
                'message_id'=>$post_id,
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"$b_name",'url'=>"$b_link"]],
                    ],
                ]),
            ]);

            $users = $conn->query("SELECT * FROM users ORDER BY id")->num_rows;

            bot('sendMessage',[
                'chat_id'=>$cid,
                'text'=>"👆 POST <b>$users ta</b> odamga yuboriladi, <b>tasdiqlaysizmi?</b>\n\n<i>POST ID: $post_id\nBUTTON NAME: $b_name\nBUTTON LINK: $b_link</i>",
                'parse_mode'=>"html",
                'disable_web_page_preview'=>true,
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"Tasdiqlayman",'callback_data'=>"sendmsg_accept"],['text'=>"Bekor",'callback_data'=>"sendmsg_inaccept"]],
                    ],
                ]),
            ]);

            temp($cid, null);

        }

    }

    // InAccept Send Message
    if($data == "sendmsg_inaccept"){

        temp($cid2, null);

        unlink("Cache/Send/post_id");
        unlink("Cache/Send/b_name");
        unlink("Cache/Send/b_link");
        unlink("Cache/Send/channel_id");

        bot('deleteMessage',[
            'chat_id'=>$cid2,
            'message_id'=>$mid2,
        ]);

        bot('sendMessage',[
            'chat_id'=>$cid2,
            'text'=>"<b>Xabar yuborish bekor qilindi!</b>",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"Orqaga",'callback_data'=>"admin"]],
                ],
            ]),
        ]);

    }

    // Accept Send Message
    if($data == "sendmsg_accept"){

        bot('deleteMessage',[
            'chat_id'=>$cid2,
            'message_id'=>$mid2,
        ]);

        $last_id = $conn->query("SELECT * FROM users ORDER BY id DESC")->fetch_assoc()['user_id'];

        $start_time = date('H:i', strtotime('+1 minutes'));

        bot('sendMessage',[
            'chat_id'=>$cid2,
            'text'=>"Xabar yuborish <b>tasdiqlandi!</b>\n\n<b><i>$start_time</i></b> da barchaga yuborish boshlanadi!\n\n<i>Last ID: $last_id</i>",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"Orqaga",'callback_data'=>"admin"]],
                ],
            ]),
        ]);

        $channel_id = file_get_contents("Cache/Send/channel_id");
        $post_id = file_get_contents("Cache/Send/post_id");
        $b_name = file_get_contents("Cache/Send/b_name");
        $b_link = file_get_contents("Cache/Send/b_link");

        $conn->query("INSERT INTO send (ID, Admin_ID, Token, Channel_ID, Post_ID, B_Name, B_Link, From_ID, Last_ID, Start_Time, End_Time, Send, NoSend, Status)
        VALUES (null, '$cid2', '$token', '$channel_id', '$post_id', '$b_name', '$b_link', '0', '$last_id', '$date $time', null, null, null, 'Waiting')");

        bot('sendMessage',[
            'chat_id'=>$cid2,
            'text'=>$conn->error,
        ]);

        $insert_id = $conn->insert_id;
        file_put_contents("insert_id", "$insert_id");

        unlink("Cache/Send/channel_id");
        unlink("Cache/Send/post_id");
        unlink("Cache/Send/b_name");
        unlink("Cache/Send/b_link");

    }

    if(mb_stripos($text, "/sendinfo_") !== false){

        $id = str_replace("/sendinfo_","",$text);

        $row = $conn->query("SELECT * FROM send WHERE ID = '$id'")->fetch_assoc();

        $ID = $row['ID'];
        $Admin_ID = $row['Admin_ID'];
        $Channel_ID = $row['Channel_ID'];
        $Post_ID = $row['Post_ID'];
        $B_Name = $row['B_Name'];
        $B_Link = $row['B_Link'];
        $Start_Time = $row['Start_Time'];
        $End_Time = $row['End_Time'];
        $Sended = $row['Send'];
        $NoSended = $row['NoSend'];
        $Status = $row['Status'];

        $txt = "Send ID: <i>$ID</i>\n\nYuboruvchi: <a href = 'tg://user?id=$Admin_ID'>Admin</a>\nChannel ID: <i>$Channel_ID</i>\nPost ID: <i>$Post_ID</i>\nButton Name: <i>$B_Name</i>\nButton Link: <i>$B_Link</i>\nStart Time: <i>$Start_Time</i>\nEnd Time: <i>$End_Time</i>\nSended: <i>$Sended</i>\nNoSended: <i>$NoSended</i>\n\nStatus: <b>$Status</b>";

        bot('sendMessage',[
            'chat_id'=>$cid,
            'text'=>"$txt",
            'parse_mode'=>"html",
            'disable_web_page_preview'=>true,
        ]);

    }

}

?>
