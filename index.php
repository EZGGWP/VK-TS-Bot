<?php

define('CALLBACK_API_EVENT_CONFIRMATION', 'confirmation');
define('CALLBACK_API_EVENT_MESSAGE_NEW', 'message_new');
define('CALLBACK_API_EVENT_MESSAGE_REPLY', 'message_reply');

require_once 'config.php';
require_once 'global.php';

require_once 'api/vk_api.php';

require_once 'bot/bot.php';

if (!isset($_REQUEST)) {
  exit;
}


callback_handleEvent();

function callback_handleEvent() {
  $event = _callback_getEvent();

  log_msg($event);

  if (isset($event)) {

    try {
      switch ($event['type']) {
        case CALLBACK_API_EVENT_CONFIRMATION:
          _callback_handleConfirmation();
          break;

        case CALLBACK_API_EVENT_MESSAGE_NEW:
          _callback_handleMessageNew($event['object']);
          break;

        case CALLBACK_API_EVENT_MESSAGE_REPLY:
          _callback_okResponse();

        default:
          _callback_response('Unsupported event '.$event['type']);
          break;
      }
    } catch (Exception $e) {
      log_error($e);
    }

  };
  _callback_okResponse();
}

function _callback_getEvent() {
  return json_decode(file_get_contents('php://input'), true);
}

function _callback_handleConfirmation() {
  _callback_response(CALLBACK_API_CONFIRMATION_TOKEN);
}

function _callback_handleMessageNew($data) {
  $user_id = $data['message']['from_id'];
  $peer_id = $data['message']['peer_id'];
  $message_text = $data['message']['text'];
  if ($message_text == "tsb online") {
    bot_sendMessage($user_id, $peer_id);
  }
  _callback_okResponse();
}

function _callback_okResponse() {
  _callback_response('ok');
}

function _callback_response($data) {
  echo $data;
  exit();
}


