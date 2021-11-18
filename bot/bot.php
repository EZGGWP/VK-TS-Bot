<?php

function bot_sendMessage($user_id, $peer_id) {
  $users_get_response = vkApi_usersGet($user_id);
  $user = array_pop($users_get_response);

  $url = ""; // TeamSpeak "data fetcher" URL (github.com/EZGGWP/TS-Data-fetcher)
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array()));
  curl_setopt($curl, CURLOPT_POST, true);
  $res = curl_exec($curl);
  $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  $error = curl_error($curl);

  $msg = "";
  if ($code == 200) {
    _log_write("[INFO] TS data fetched successfully");
    $jsonRes = json_decode($res, true);
    $countUsers = count($jsonRes['users']);

    $bit = defineCharByNumber($countUsers);

    $msg = "Привет, {$user['first_name']}!\nНа данный момент на сервере {$countUsers} пользовате{$bit}:\n";
    foreach ($jsonRes['users'] as $key => $value) {
      $msg .= "\n▻".$key." (".$value.")";
    }
    
  } else {
    $msg = "Не удалось получить данные с сервера.";
  }
    $attachments = array();

    vkApi_messagesSend($peer_id, $msg, $attachments);


  
}

function _bot_uploadPhoto($user_id, $file_name) {
  $upload_server_response = vkApi_photosGetMessagesUploadServer($user_id);
  $upload_response = vkApi_upload($upload_server_response['upload_url'], $file_name);

  $photo = $upload_response['photo'];
  $server = $upload_response['server'];
  $hash = $upload_response['hash'];

  $save_response = vkApi_photosSaveMessagesPhoto($photo, $server, $hash);
  $photo = array_pop($save_response);

  return $photo;
}

function _bot_uploadVoiceMessage($user_id, $file_name) {
  $upload_server_response = vkApi_docsGetMessagesUploadServer($user_id, 'audio_message');
  $upload_response = vkApi_upload($upload_server_response['upload_url'], $file_name);

  $file = $upload_response['file'];

  $save_response = vkApi_docsSave($file, 'Voice message');
  $doc = array_pop($save_response);

  return $doc;
}

function defineCharByNumber($number) {
  $char = '';
  $preLastChar = '';

  $lastChar = substr(strval($number), -1, 1);

  if (strlen($number) > 1) $preLastChar = substr(strval($number), -2, 1);

  if ($preLastChar == "1" || $lastChar == "0" || $lastChar == "5" || $lastChar == "6" || $lastChar == "7" || $lastChar == "8" || $lastChar == "9") {
      $char = 'лей';
  } else if ($lastChar == "2" || $lastChar == "3" || $lastChar == "4") {
      $char = 'ля';
  } else $char = 'ль';

  return $char;
}
