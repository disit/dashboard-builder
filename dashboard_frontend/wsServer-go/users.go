package main

import (
	"github.com/gorilla/websocket"
)

type WebsocketUser struct {
	id               string
	MetricName       string `json:"metricName"`
	UserType         string `json:"userType"`
	socket           *websocket.Conn
	send             chan []byte
	WidgetUniqueName interface{} `json:"widgetUniqueName"`
	sendingAck       bool
	msgIdAck         int64
	ClientIp         string `json:"clientIP"`
	Origin           string `json:"origin"`
	ValidOrigin      bool   `json:"validOrigin"`
	MsgCount         int32  `json:"msgCount"`
	AppID            string `json:"appId"`
}
