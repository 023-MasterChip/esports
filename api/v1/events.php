<?php

include_once('../config/database.php');
include_once('./objects/event.php');
include_once('token_validate.php');

$db = new Database();


$conn = $db->getConnection();

if ($conn) {

  //   echo "Database Connected!";

}

$event = new Event($conn);


$req = $_SERVER['REQUEST_METHOD'];

$token = validateToken();

switch ($_SERVER['REQUEST_METHOD']) {

  case 'GET':

    if ($token) {
      // get single event
      $id = $_GET['id'];
      echo $event->getEventById($id);
      break;
    } else {
      echo 'Access Denied!';
    }
    break;

  case 'POST':
    $post = $_POST;
    // create event function
    if (isset($post['create'])) {
      echo $event->createEvent($post);
    }
    // update event function
    elseif (isset($post['update'])) {
      if ($token) {
        $id =  $_GET['id'];
        $token_user = $token->data->user_id;
        echo $event->updateEvent($id, $post, $token_user);
      } else {
        echo 'Access Denied!';
      }
    } elseif (isset($post['teamevent'])) {

      if ($token) {
        echo $event->teamEvent($post);
      } else {
        echo 'Access Denied!';
      }
    } elseif (isset($post['playerevent'])) {

      if ($token) {
        echo $event->playerEvent($post);
      } else {
        echo 'Access Denied!';
      }
    }
    elseif (isset($post['archive'])) {

      if ($token) {
        echo $event->getArchivedEvents($post);
      } else {
        echo 'Access Denied!';
      }
    }
    // get all events
    else {
      if ($token) {
        echo $event->getEvents();
      } else {
        echo 'Access Denied!';
      }
    }

    break;

  case 'DELETE':
    // delete event using id
    if ($token) {
      $id =  $_GET['id'];
      echo $event->deleteEvent($id);
    } else {
      echo 'Access Denied!';
    }
    break;
  default:
}
