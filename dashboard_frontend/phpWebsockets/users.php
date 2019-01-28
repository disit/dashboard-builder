<?php

class WebSocketUser {

  public $socket;
  public $id;
  public $headers = array();
  public $handshake = false;
  
  //Aggiunti da me
  public $userType;
  public $metricName;
  public $widgetUniqueName;

  public $handlingPartialPacket = false;
  public $partialBuffer = "";

  public $sendingContinuous = false;
  public $partialMessage = "";
  
  public $hasSentClose = false;

  function __construct($id, $socket) {
    $this->id = $id;
    $this->socket = $socket;
    $this->userType = null;
    $this->metricName = null;
    $this->widgetUniqueName = null;
  }
}