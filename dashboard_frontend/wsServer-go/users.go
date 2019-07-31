package main

import (
	"github.com/gorilla/websocket"
)

type WebsocketUser struct {
	id, metricName   string
	userType         string
	socket           *websocket.Conn
	send             chan []byte
	widgetUniqueName interface{}
	sendingAck       bool
	msgIdAck		     int64
	clientIp         string
  validOrigin      bool
}
