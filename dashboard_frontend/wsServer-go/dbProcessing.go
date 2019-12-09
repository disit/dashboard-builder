package main

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"log"
	"strings"
	"time"
)

// funzione chiave per l'interazione col db, esegue all'interno delle routines

func dbCommunication(jsonMsg []byte, user *WebsocketUser) {
	var response = map[string]interface{}{
		"result":  "",
		"msgType": "",
	}
	dat, jsonParsed := processingMsg(jsonMsg)
	response["msgType"] = dat["msgType"]
	switch dat["msgType"] {
	case "AddEmitter":
		//sender: nodered
		//receiver: wsServer
		/* esempio messaggio
		   { msgType: 'AddEmitter',
		     name: 'NR_89ba9b5e_c16348',
		     valueType: 'String',
		     user: 'badii',
		     startValue: 'Off',
		     domainType: 'impulse',
		     offValue: 'Off',
		     onValue: 'On',
		     minValue: null,
		     maxValue: null,
		     endPointPort: 1895,
		     endPointHost: '\'0.0.0.0\'',
		     httpRoot: null,
		     appId: 'da5698be17b9b46962335799779fbeca8ce5d491c0d26243bafef9ea1837a9d8',
		     flowId: 'd9c61fae.cf47a',
		     flowName: 'Flow 4',
		     nodeId: '89ba9b5e.c16348',
		     widgetType: 'widgetImpulseButton',
		     widgetTitle: 'impulsetry',
		     dashboardTitle: 'NuovaJSON',
		     accessToken: '...' } */

		/* aggiunge un emitter alla tabella degli emitter */
		//TBD
		//mysqli_autocommit($link, FALSE);
		//mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
		var err error
		var username, role, organization string
		if dat["accessToken"] != nil {
			username, role, err = checkToken(dat["accessToken"].(string), "nodered")
			if err != nil {
				username, role, err = checkToken(dat["accessToken"].(string), "nodered-iotedge")
			}
		} else {
			err = fmt.Errorf("missing accessToken")
		}
		if err != nil {
			log.Print(err)
			response["result"] = "Ko"
			response["error"] = err
		} else {
			if role != "RootAdmin" {
				organization, _ = getOrganization(username)
				log.Print("AddEmitter: force user ", username, "@", organization, "/", dat["user"])
				dat["user"] = username
				dat["organization"] = organization
			} else {
				organization, _ = getOrganization(dat["user"].(string))
				log.Print("AddEmitter: RootAdmin using user ", dat["user"], "@", organization)
				dat["organization"] = organization
			}
			var oldNrInputId string
			err := db.QueryRow("SELECT id FROM "+dashboard+".NodeRedInputs WHERE NodeRedInputs.nodeId = ? AND NodeRedInputs.user = ?;", dat["nodeId"], dat["user"]).Scan(&oldNrInputId)
			if err != nil {
				log.Print(err)
			}
			log.Print("oldNrInputId:", oldNrInputId)

			_, err = db.Exec("DELETE FROM Dashboard.NodeRedInputs WHERE NodeRedInputs.nodeId = ? AND NodeRedInputs.user = ?;", dat["nodeId"], dat["user"])
			if err != nil {
				log.Print(err)
				response["result"] = "Ko"
				response["error"] = err
			} else {
				res, err := db.Exec("INSERT INTO Dashboard.NodeRedInputs(name, valueType, user, startValue, domainType, minValue, NodeRedInputs.maxValue, offValue, onValue, endPointPort, endPointHost, httpRoot, appId, flowId, flowName, nodeId, organization) "+
					"VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", dat["name"], dat["valueType"], dat["user"], dat["startValue"], dat["domainType"], dat["minValue"], dat["maxValue"], dat["offValue"], dat["onValue"], dat["endPointPort"], dat["endPointHost"], dat["httpRoot"], dat["appId"], dat["flowId"], dat["$flowName"], dat["nodeId"], dat["organization"])
				if err != nil {
					log.Print(err)
					response["result"] = "Ko"
					response["error"] = err
				} else {
					nrInputId, _ := res.LastInsertId()
					log.Print("new input id:", nrInputId)
					if dat["widgetType"] != nil {
						var nDashboards int
						var result = true
						// controlla se esiste gia' una dashboard con titolo uguale..
						err = db.QueryRow("SELECT COUNT(*) FROM "+dashboard+".Config_dashboard WHERE title_header = ?"+
							" AND user = ?;", dat["dashboardTitle"], dat["user"]).Scan(&nDashboards)
						if err != nil {
							log.Print(err)
							response["result"] = "Ko"
							result = false
						} else if nDashboards == 0 {
							// in caso negativo, ne crea una inserendo in Config_dashboard
							result = createDashboard(db, dat)
						}
						if result {
							/* aggiunge widget alla dashboard */
							addWidget, w := addWidget(db, dat["dashboardTitle"], dat["user"], dat["widgetType"], dat["name"],
								dat["metricType"], dat["appId"], dat["flowId"], dat["nodeId"], dat["widgetTitle"])
							if addWidget {
								/* aggiunge o aggiorna tabella ActuatorsAppsValues */
								err = nil
								if oldNrInputId == "" {
									_, err = db.Exec("INSERT INTO Dashboard.ActuatorsAppsValues(widgetName, actionTime, value, username, ipAddress, actuationResult, actuationResultTime, nrInputId) "+
										"VALUES ('none', CURRENT_TIMESTAMP, ?, ?, '127.0.0.1', 'Ok', CURRENT_TIMESTAMP, ?)", dat["startValue"], dat["user"], nrInputId)
								} else {
									_, err = db.Exec("UPDATE Dashboard.ActuatorsAppsValues SET nrInputId = ? WHERE nrInputId = ?", nrInputId, oldNrInputId)
								}
								if err != nil {
									log.Print(err)
									response["result"] = "Ko"
									response["error"] = err
								} else {
									/* aggiunge la connessione tra quelle associate all'emitter */
									//user.metricName = dat["metricName"].(string) //check
									user.userType = "actuator"
									user.widgetUniqueName = w["widgetUniqueName"]
									if user.widgetUniqueName != "" && user.widgetUniqueName != nil {
										publish([]byte("subscribe"+user.widgetUniqueName.(string)), "default") //check???
										mu.Lock()
										widgetUniqueName := user.widgetUniqueName.(string)
										ws.clientWidgets[widgetUniqueName] = append(ws.clientWidgets[widgetUniqueName], user)
										log.Print("AddEmitter - added user for widget: ", widgetUniqueName, " count: ", ws.clientWidgets[widgetUniqueName])
										mu.Unlock()
										response["widgetUniqueName"] = user.widgetUniqueName
										response["result"] = "Ok"
									} else {
										log.Print("invalid widget unique id:", user.widgetUniqueName)
										response["result"] = "Ko"
									}
								}
							} else {
								log.Print("failed addWidget")
								response["result"] = "Ko"
							}
						} else {
							log.Print("failed create dashboard")
							response["result"] = "Ko"
						}
					} else {
						log.Print("no widgetType")
						response["result"] = "Ok"
					}
				}
			}
		}
		break
	case "RegisterEmitter":
		//not used?
		/* esempio messaggio
		   { msgType: 'RegisterEmitter',
			 widgetUniqueName: '...'
		     accessToken: '...' } */

		/* aggiunge la connessione tra quelle associate all'emitter */
		user.userType = "actuator"
		//user.metricName = dat["metricName"].(string) //check
		user.widgetUniqueName = dat["widgetUniqueName"]
		if user.validOrigin {
			if user.widgetUniqueName != "" && user.widgetUniqueName != nil {
				publish([]byte("subscribe"+user.widgetUniqueName.(string)), "default") //check???
				mu.Lock()
				widgetUniqueName := user.widgetUniqueName.(string)
				ws.clientWidgets[widgetUniqueName] = append(ws.clientWidgets[widgetUniqueName], user)
				log.Print("RegisterEmitter - added user for widget: ", widgetUniqueName, " count: ", ws.clientWidgets[widgetUniqueName])
				mu.Unlock()
				response["widgetUniqueName"] = user.widgetUniqueName
				response["result"] = "Ok"
			} else {
				log.Print("invalid widget unique id:", user.widgetUniqueName)
				response["result"] = "Ko"
			}
		} else {
			log.Print("RegisterEmitter invalid origin")
			response["result"] = "Ko"
		}
		break
	case "SendToEmitter":
		// sender: dashboard
		// receiver: wsServer
		var username string
		var role string
		var idDashboard string
		var creator string
		var err error

		//check widgetUniqueName
		widgetUniqueName := dat["widgetUniqueName"]

		if !user.validOrigin {
			log.Print("SendEmitter ", widgetUniqueName, " invalid origin")
			response["result"] = "Ko"
			break
		}

		err = db.QueryRow("SELECT id_dashboard, creator FROM "+dashboard+".Config_widget_dashboard wd join "+dashboard+".Config_dashboard d ON wd.id_dashboard=d.Id where name_w= ? and deleted='no';", widgetUniqueName).Scan(&idDashboard, &creator)
		if err != nil {
			log.Print("SendToEmitter failed widget ", widgetUniqueName)
			response["result"] = "Ko"
			break
		}
		log.Print("widget ", widgetUniqueName, " -> dashboard:", idDashboard, " creator:", creator)
		if idDashboard == "" || creator == "" {
			log.Print("SendToEmitter invalid widget ", widgetUniqueName)
			response["result"] = "Ko"
			break
		}

		//check access token if dashboard
		if strings.Contains(ws.requireToken, "dashboard") {
			if dat["accessToken"] == nil {
				log.Print("SendToEmitter require accessToken")
				response["result"] = "Ko"
				break
			}
			username, role, err = checkToken(dat["accessToken"].(string), "nodered")
			if err != nil {
				username, role, err = checkToken(dat["accessToken"].(string), "nodered-iotedge")
			}
			if err != nil {
				log.Print("invalid token ", err)
				response["result"] = "Ko"
				break
			}
			log.Print("SendToEmitter user: ", username, " role: ", role)
		}

		//dashboard pubblica?

		//se privata l'utente può accederci?

		/* salva il dato ricevuto in tabella ActuatorsAppsValues*/

		ipAddress := user.clientIp
		actionTime := time.Now().Format("2006-01-02 15:04:05")
		log.Print(ipAddress, " ", actionTime)
		res, err := db.Exec("INSERT INTO Dashboard.ActuatorsAppsValues(widgetName, actionTime, value, username, ipAddress, nrInputId) "+
			"VALUES(?, ?, ?, ?, ?, ?)", dat["widgetUniqueName"], actionTime, dat["value"], dat["username"], ipAddress, dat["nrInputId"])
		if err != nil {
			log.Print(err)
			response["result"] = "Ko"
		} else {
			/* invia il dato ricevuto sulla connessione associata all'emitter */
			user.widgetUniqueName = dat["widgetUniqueName"]
			lastId, _ := res.LastInsertId()
			newMessage := &EmitterMessage{
				MsgType:          "DataToEmitter",
				WidgetUniqueName: dat["widgetUniqueName"].(string),
				NewValue:         dat["value"],
				MsgId:            lastId,
			}
			user.msgIdAck = lastId
			// inoltra i nuovi dati ai vari user connessi. Con l'implementazione
			// redis viene chiamata la funzione publish altrimenti, si inserisce
			// direttamente il nuovo messaggio nel canale replyAll del manager.

			//salva i dati ricevuti

			newMsg, err := json.Marshal(newMessage)
			if err != nil {
				log.Print(err)
			}
			widgetUniqueName := dat["widgetUniqueName"].(string)
			if ws.redisEnabled == "yes" {
				publish(newMsg, widgetUniqueName)
			} else {
				manager.replyAll <- newMsg
			}

			response["result"] = "Ok"
			response["widgetUniqueName"] = widgetUniqueName
			response["msgId"] = lastId

			//aggiunge la connessione attuale a quelle legate allo widget (se non già presente)
			mu.Lock()
			found := false
			for _, value := range ws.clientWidgets[widgetUniqueName] {
				if value == user {
					found = true
					break
				}
			}
			if !found {
				ws.clientWidgets[widgetUniqueName] = append(ws.clientWidgets[widgetUniqueName], user)
				log.Print("SendToEmitter - added user for widget: ", widgetUniqueName, " count: ", ws.clientWidgets[widgetUniqueName])
			}
			mu.Unlock()
		}
		break
	case "DataToEmitterAck":
		//sender: nodered
		//receiver: wsServer
		//nodered acknowledges the receipt of a DataToEmitter message

		msgId := (int64)(dat["msgId"].(float64))
		if msgId != user.msgIdAck {
			log.Print("ack of invalid msg: waiting ", user.msgIdAck, " arrived ", msgId)
		}
		actionResultTime := time.Now().Format("2006-01-02 15:04:05")
		res, err := db.Exec("UPDATE Dashboard.ActuatorsAppsValues SET actuationResult = 'Ok', actuationResultTime = ? WHERE id = ?", actionResultTime, msgId)
		if err != nil {
			log.Print(err)
			response["result"] = "Ko"
		} else if nrows, _ := res.RowsAffected(); nrows != 1 {
			log.Print("failed update: msg not found on ack")
			response["result"] = "Ko"
		} else {
			/* invia il dato ricevuto sulla connessione associata all'emitter */
			newMessage := &EmitterMessage{
				MsgType:          "DataToEmitterAck",
				WidgetUniqueName: dat["widgetUniqueName"].(string),
				MsgId:            msgId,
			}
			user.sendingAck = true

			// inoltra i nuovi dati ai vari user connessi. Con l'implementazione
			// redis viene chiamata la funzione publish altrimenti, si inserisce
			// direttamente il nuovo messaggio nel canale replyAll del manager.

			newMsg, err := json.Marshal(newMessage)
			if err != nil {
				log.Print(err)
			}
			if ws.redisEnabled == "yes" {
				publish(newMsg, dat["widgetUniqueName"].(string))
			} else {
				manager.replyAll <- newMsg
			}
			response["result"] = "Ok"
			response["widgetUniqueName"] = dat["widgetUniqueName"].(string)
		}
		break
	case "DelEmitter":
		break
	case "AddEditMetric":
		/* inserisce un nuovo record nella tabella NodeRedMetrics del databse dashboard manager se il blocchetto e' nuovo ,altrimenti
		aggiorna i dati precedenti facendo una delete e un insert
		*/
		var username string
		var role string
		var organization string
		var err error
		if dat["accessToken"] != nil {
			username, role, err = checkToken(dat["accessToken"].(string), "nodered")
			if err != nil {
				username, role, err = checkToken(dat["accessToken"].(string), "nodered-iotedge")
			}
		} else {
			err = fmt.Errorf("missing accessToken")
		}
		if err != nil {
			log.Print(err)
			response["result"] = "Ko"
			response["error"] = err
		} else {
			if role != "RootAdmin" {
				organization, _ = getOrganization(username)
				log.Print("AddEditMetric: force user ", username, "@", organization, "/", dat["user"])
				dat["user"] = username
				dat["organization"] = organization
			} else {
				organization, _ = getOrganization(dat["user"].(string))
				log.Print("AddEditMetric: RootAdmin using user ", dat["user"], "@", organization)
				dat["organization"] = organization
			}
			_, err := db.Exec("DELETE FROM "+dashboard+".NodeRedMetrics WHERE NodeRedMetrics.name = ? "+
				" AND NodeRedMetrics.metricType = ? AND NodeRedMetrics.user = ? "+
				"AND appId <=> ? AND flowId <=> ?;", dat["metricName"], dat["metricType"], dat["user"], dat["appId"], dat["flowId"])

			if err != nil {
				log.Print(err)
				response["result"] = "Ko"
			} else {
				_, err := db.Exec("INSERT INTO "+dashboard+".NodeRedMetrics(name, metricType, user,"+
					" shortDesc, fullDesc, appId, flowId, flowName, nodeId, httpRoot, organization)"+
					" VALUES(?,?,?,?,?,?,?,?,?,?,?);", dat["metricName"], dat["metricType"], dat["user"], dat["metricName"], dat["metricName"], dat["appId"], dat["flowId"], dat["flowName"], dat["nodeId"], dat["httpRoot"], dat["organization"])

				if err != nil {
					log.Print(err)
					response["result"] = "Ko"
				} else {

					var metricStartValue int
					var dataField string

					switch dat["metricType"] {
					case "Intero", "Float":
						dataField = "value_num"
						metricStartValue = 0
						break
					case "Percentuale":
						dataField = "value_perc1"
						metricStartValue = 0
						break
					case "Testuale", "webContent":
						dataField = "value_text"
						metricStartValue = 0
						break
					case "Series":
						dataField = "series"
						metricStartValue = 0
						break
					}

					var count int
					computationDate := time.Now().String()
					computationDate = computationDate[0:19]

					/* controlla se sono presenti gia' dati per la metrica in esame, altrimenti inserisce nella tabella data
					un nuovo record */

					err2 := db.QueryRow("SELECT COUNT(*) FROM "+dashboard+".Data WHERE idMetric_data = ?;", dat["metricName"]).Scan(&count)

					if err2 != nil {
						log.Print(err2)
					}

					if count == 0 {
						_, err := db.Exec("INSERT INTO "+dashboard+".Data(IdMetric_data,computationDate,"+dataField+", appId, flowId, nrMetricType, nrUsername)"+
							" VALUES(?,?,?,?,?,?,?);", dat["metricName"], computationDate, metricStartValue, dat["appId"], dat["flowId"], dat["metricType"], dat["user"])
						if err != nil {
							log.Print(err)
						}

					}

					if dat["widgetType"] != nil {
						var count2 int
						// controlla se esiste gia' una dashboard con titolo uguale..
						err3 := db.QueryRow("SELECT COUNT(*) FROM "+dashboard+".Config_dashboard WHERE title_header = ?"+
							" AND user = ?;", dat["dashboardTitle"], dat["user"]).Scan(&count2)

						if err3 != nil {
							log.Print(err3)
							response["result"] = "Ko"
						} else if count2 == 0 {

							// in caso negativo, ne crea una inserendo in Config_dashboard
							done := createDashboard(db, dat)

							// aggiungiamo il widget

							if done {
								done, _ = addWidget(db, dat["dashboardTitle"], dat["user"], dat["widgetType"], dat["metricName"],
									dat["metricType"], dat["appId"], dat["flowId"], dat["nodeId"], dat["widgetTitle"])
							}
							if done {
								response["result"] = "Ok"
							} else {
								response["result"] = "Ko"
							}

						} else {
							addWidget, _ := addWidget(db, dat["dashboardTitle"], dat["user"], dat["widgetType"], dat["metricName"],
								dat["metricType"], dat["appId"], dat["flowId"], dat["nodeId"], dat["widgetTitle"])
							if addWidget {
								response["result"] = "Ok"
							} else {
								response["result"] = "Ko"
							}
						}
					} else {
						response["result"] = "Ok"
					}
				}
			}
		}
		break
	case "AddMetricData":
		//sender: nodered
		newMessage := &Message{
			MsgType:    "newNRMetricData",
			MetricName: dat["metricName"].(string),
			NewValue:   dat["newValue"]}

		// inoltra i nuovi dati ai vari user connessi. Con l'implementazione
		// redis viene chiamata la funzione publish altrimenti, si inserisce
		// direttamente il nuovo messaggio nel canale replyAll del manager.

		newMsg, err := json.Marshal(newMessage)

		if err != nil {
			log.Print(err)
		}
		if ws.redisEnabled == "yes" {
			publish(newMsg, dat["metricName"].(string))
		} else {
			manager.replyAll <- newMsg
		}

		computationDate := time.Now().String()
		computationDate = computationDate[0:19]

		//aggiunge i nuovi valori alla tabella Data

		switch dat["metricType"] {
		case "Intero", "Float":
			val := "value_num"
			res := caseQuery(db, computationDate, dat, val)
			response["result"] = res
			break

		case "Percentuale":

			val := "value_perc1"
			res := caseQuery(db, computationDate, dat, val)
			response["result"] = res
			break

		case "Series":

			val := "series"
			res := caseQuery(db, computationDate, dat, val)
			response["result"] = res
			break

		case "Testuale", "webContent":

			if strings.Index(fmt.Sprint(dat["newValue"]), "OperatorEvent") > -1 {

				_, err2 := db.Exec("INSERT INTO "+dashboard+".OperatorEvents(time, personNumber, lat, lng, codeColor, user) VALUES(?, ?, ?, ?, ?, ?)"+
					";", computationDate, jsonParsed["personNumber"], jsonParsed["lat"], jsonParsed["lng"], jsonParsed["codeColor"], jsonParsed["user"])

				if err2 != nil {
					log.Print(err2)
					response["result"] = "Ko"

				} else {
					response["result"] = "Ok"
				}

			} else {

				val := "value_text"

				res := caseQuery(db, computationDate, dat, val)

				response["result"] = res
				break

			}

		case "geoJson":
			response["result"] = "Ok"
			break
		}

		break

	case "ClientWidgetRegistration":
		//sender: dashboard
		//receiver: wsServer
		user.userType = dat["userType"].(string)
		user.metricName = dat["metricName"].(string)
		user.widgetUniqueName = dat["widgetUniqueName"]
		publish([]byte("subscribe"+dat["metricName"].(string)), "default")
		if !user.validOrigin {
			log.Print("ClientWidgetRegistration invalid origin")
			response["result"] = "Ko"
			break
		}

		if dat["widgetUniqueName"] != "" && dat["widgetUniqueName"] != nil {

			/* se la metrica personale su cui insiste il widget e` gia` presentein memoria,
			il widget viene inserito nella coda degli widget relativi a tale metrica, altrimenti
			viene creata una nuova coda e inserito il widget.
			*/

			mu.Lock()
			m := ws.clientWidgets
			if findKey(m, user.metricName) {
				if len(m[user.metricName]) > 1 {
					m[user.metricName] = append(m[user.metricName], user)
					ws.clientWidgets = m

					mu.Unlock()

				} else {

					m[user.metricName] = append(m[user.metricName], user)
					ws.clientWidgets = m
					publish([]byte("subscribe"+dat["metricName"].(string)), "default")
					mu.Unlock()

				}
			} else {
				m[user.metricName] = append(m[user.metricName], user)
				ws.clientWidgets = m
				publish([]byte("subscribe"+dat["metricName"].(string)), "default")
				mu.Unlock()

			}
			response["result"] = "Ok"
		} else {
		}
		break

	case "DelMetric":
		var count int
		err2 := db.QueryRow("SELECT COUNT(*) FROM "+dashboard+".Config_widget_dashboard WHERE nodeId = ?;", dat["nodeId"]).Scan(&count)

		if err2 != nil {
			log.Print(err2)
			response["result"] = "Ko"
		}

		/* se esistono piu` wiget che insistono sulla stessa metrica in esame, viene rimosso il widget istanziato
		in precedenza dal blocchetto lasciando gli altri e la metrica personale
		*/

		if count > 1 {
			_, err := db.Exec("DELETE FROM "+dashboard+".Config_widget_dashboard WHERE nodeId = ?;", dat["nodeId"])

			if err != nil {
				log.Print(err)
				response["result"] = "Ko"
			} else {
				response["result"] = "Ok"
			}
		} else {

			// se rimane un solo widget, cancella widget, metrica e dati

			tx, err := db.Begin()
			if err != nil {
				log.Print(err)
				return
			}
			stmt, err := tx.Prepare("DELETE FROM " + dashboard + ".NodeRedMetrics WHERE NodeRedMetrics.name = ? AND NodeRedMetrics.metricType = ?" +
				" AND NodeRedMetrics.user = ? AND NodeRedMetrics.appId = ? AND NodeRedMetrics.flowId = ?;")
			if err != nil {
				log.Print(err)
				tx.Rollback()
				return
			}
			defer stmt.Close()

			_, err = stmt.Exec(dat["metricName"], dat["metricType"], dat["user"], dat["appId"], dat["flowId"])

			if err != nil {
				log.Print(err)
				tx.Rollback()
				return

			} else {
				stmt2, err := tx.Prepare("DELETE FROM " + dashboard + ".Data WHERE Data.IdMetric_data = ? AND appId = ?" +
					" AND flowId = ? AND nrMetricType = ? AND nrUsername = ?;")
				if err != nil {
					log.Print(err)
					tx.Rollback()
					response["result"] = "Ko"
				}
				defer stmt2.Close()
				_, err = stmt2.Exec(dat["metricName"], dat["appId"], dat["flowId"], dat["metricType"], dat["user"])
				if err != nil {
					log.Print(err)
					tx.Rollback()
					response["result"] = "Ko"

				} else {
					stmt3, err := tx.Prepare("DELETE FROM " + dashboard + ".Config_widget_dashboard WHERE nodeId = ?;")
					if err != nil {
						log.Print(err)
						tx.Rollback()
						response["return"] = "Ko"
					}
					defer stmt3.Close()
					_, err = stmt3.Exec(dat["nodeId"])
					if err != nil {
						log.Print(err)
						tx.Rollback()
						response["return"] = "Ko"
					} else {
						tx.Commit()
						response["result"] = "Ok"
					}
				}
			}
		}
		break

	default:
		log.Print("messaggio ignorato ", dat["msgType"])
		break
	}

	reply, _ := json.Marshal(response)
	log.Print(string(reply))
	user.send <- reply
}

func createDashboard(db *sql.DB, dat map[string]interface{}) bool {
	title := dat["dashboardTitle"]
	dashboardAuthorName := dat["user"]
	organization := dat["organization"]
	subtitle := ""
	color := "rgba(51, 204, 255, 1)"
	background := "#FFFFFF"
	externalColor := "#FFFFFF"
	nCols := 15
	headerFontColor := "white"
	headerFontSize := 28
	viewMode := "alwaysResponsive"
	/*
		addLogo := false
		logoLink := null
		filename := null
		widgetsBorders := "yes"
		widgetsBordersColor := "rgba(51, 204, 255, 1)"
	*/
	visibility := "author"
	headerVisible := 1
	embeddable := "yes"
	authorizedPagesJson := "[]"
	width := (nCols * 78) + 10

	lastUsedColors := []string{
		"rgba(51, 204, 255, 1)",
		"rgba(255,255,255,1)",
		"rgba(255,255,255,1)",
		"rgba(255,255,255,1)",
		"rgba(255,255,255,1)",
		"rgba(255,255,255,1)",
		"rgba(255,255,255,1)",
		"rgba(255,255,255,1)",
		"rgba(255,255,255,1)",
		"rgba(255,255,255,1)",
		"rgba(255,255,255,1)",
		"rgba(255,255,255,1)",
	}

	lastUsedColorsjson, err := json.Marshal(lastUsedColors)
	if err != nil {
		log.Println("Decoding error:", err)
		return false
	}

	limit, current, err := ownershipLimitsDash(dat)
	if err != nil {
		log.Print(err)
		return false
	}
	if limit <= current {
		log.Print("cannot create dashboard limit exceeded:", limit, current)
		return false
	}

	res, err := db.Exec("INSERT INTO "+dashboard+".Config_dashboard"+
		"(Id, name_dashboard,  title_header, subtitle_header, color_header, width, height, num_rows,"+
		" num_columns, user, status_dashboard, creation_date, color_background, external_frame_color, headerFontColor,"+
		" headerFontSize, visibility, headerVisible, embeddable, authorizedPagesJson, viewMode, last_edit_date, "+
		"lastUsedColors,organizations) VALUES(NULL,?,?,?,?,?,0,0,?,?,1,now(),?,?,?,?,?,?,?,?,?,current_timestamp,?,?);"+
		"", title, title, subtitle, color, width, nCols, dashboardAuthorName, background, externalColor, headerFontColor,
		headerFontSize, visibility, headerVisible, embeddable, authorizedPagesJson, viewMode, lastUsedColorsjson, organization)

	if err != nil {
		log.Print(err)
	} else {

		id, err := res.LastInsertId()
		if err != nil {

			log.Print(err)

		} else {

			//salviamo la corrispondenza tra utente e dashboard nelle API di ownership

			newDashId := id

			_ = ownershipRegisterDash(newDashId, title, dat)
			return true
		}
	}
	return false
}

// funzione per l'inserimento dei widget

func addWidget(db *sql.DB, dashboardTitle interface{}, username interface{}, widgetType interface{}, metricName interface{}, metricType interface{}, appId interface{},
	flowId interface{}, nodeId interface{}, widgetTitle interface{}) (bool, map[string]interface{}) {

	id_metric := metricName
	title_w := widgetTitle
	nextId := "1"
	var count3 int

	err2 := db.QueryRow("SELECT COUNT(*) FROM "+dashboard+".Config_widget_dashboard WHERE nodeId = ?;", nodeId).Scan(&count3)

	if err2 != nil {
		log.Print(err2)
		m := map[string]interface{}{
			"widgetUniqueName": nil,
		}
		return false, m
	}

	if count3 != 0 {
		var id_dashboard, size_rows, size_columns int
		var name_w string
		err := db.QueryRow("SELECT id_dashboard, name_w, size_rows, size_columns FROM "+dashboard+".Config_widget_dashboard WHERE nodeId = ?;", nodeId).Scan(&id_dashboard, &name_w, &size_rows, &size_columns)
		if err != nil {
			log.Print(err)
		}

		currentWidgetDashId := id_dashboard
		currentWidgetUniqueId := name_w
		var count4 int
		err3 := db.QueryRow("SELECT COUNT(*) FROM "+dashboard+".Config_dashboard WHERE Id = ?;", currentWidgetDashId).Scan(&count4)

		if err3 != nil {
			log.Print(err3)

			m := map[string]interface{}{
				"widgetUniqueName": nil,
			}
			return false, m
		}
		if count4 != 0 {

			var user, title_header string
			err4 := db.QueryRow("SELECT user, title_header FROM "+dashboard+".Config_dashboard WHERE Id = ?;", currentWidgetDashId).Scan(&user, &title_header)

			if err4 != nil {
				log.Print(err4)

				m := map[string]interface{}{
					"widgetUniqueName": nil,
				}
				return false, m
			}

			if user == username && title_header == dashboardTitle {

				// non ha cambiato dashboard
				m := map[string]interface{}{
					"widgetUniqueName": name_w,
				}
				return true, m

			} else {
				// dashboard cambiata, va cancellato il widget dalla vecchia e messo nella nuova

				boolean, id := insertW(db, username, dashboardTitle, widgetType, nextId, id_metric, appId, flowId, metricType, nodeId, title_w)

				if boolean {

					//cancellazione widget da dashboard vecchia

					_, err2 := db.Exec("DELETE FROM "+dashboard+".Config_widget_dashboard WHERE name_w = ?; ", currentWidgetUniqueId)
					if err2 != nil {
						log.Print(err2)
						m := map[string]interface{}{
							"widgetUniqueName": nil,
						}
						return false, m
					}

					type_w := widgetType
					name_w := strings.Replace(id_metric.(string), "+", "", -1) + "_" + fmt.Sprint(id.(int64)) + type_w.(string) + string(nextId)
					name_w = strings.Replace(name_w, "%20", "NBSP", -1)
					m := map[string]interface{}{
						"widgetUniqueName": name_w,
					}

					return true, m

				} else {
					m := map[string]interface{}{
						"widgetUniqueName": nil,
					}
					return false, m
				}

			}
		}

	} else {

		boolean, id := insertW(db, username, dashboardTitle, widgetType, nextId, id_metric, appId, flowId, metricType, nodeId, title_w)

		if boolean {
			type_w := widgetType
			name_w := strings.Replace(id_metric.(string), "+", "", -1) + "_" + fmt.Sprint(id.(int64)) + type_w.(string) + string(nextId)
			name_w = strings.Replace(name_w, "%20", "NBSP", -1)
			m := map[string]interface{}{
				"widgetUniqueName": name_w,
			}

			return true, m

		} else {
			m := map[string]interface{}{
				"widgetUniqueName": nil,
			}
			return false, m

		}

	}

	m := map[string]interface{}{
		"widgetUniqueName": nil,
	}
	return false, m

}

// funzione che esegue una particolare query; implementata per evitare ridondanza e ripetizioni nel codice.

func caseQuery(db *sql.DB, computationDate string, dat map[string]interface{}, value string) string {
	_, err2 := db.Exec("INSERT INTO "+dashboard+".Data(IdMetric_data, computationDate,"+value+",appId, flowId, nrMetricType, nrUsername)"+
		" VALUES(?, ?, ?, ?, ?, ?, ?);", dat["metricName"], computationDate, dat["newValue"], dat["appId"], dat["flowId"], dat["metricType"], dat["user"])

	if err2 != nil {
		log.Print(err2)
		return "Ko"
	} else {
		return "Ok"
	}

}

// funzione per la ricerca di una particolare chiave nella mappa dei websocket user connessi.

func findKey(m map[string][]*WebsocketUser, s string) bool {
	for value := range m {
		if value == s && m[value] != nil {
			return true
		}
	}
	return false
}

// funzione per l'inserimento del widget chiamata all'interno di addWidget; implementata per ridurre ridondanza nel codice.

func insertW(db *sql.DB, username interface{}, dashboardTitle interface{}, widgetType interface{}, nextId string, id_metric interface{}, appId interface{}, flowId interface{}, metricType interface{}, nodeId interface{}, title_w interface{}) (bool, interface{}) {

	var n_row interface{} = nil
	var n_column interface{} = nil
	newWidgetType := widgetType
	var firstFreeRow interface{} = nil
	var id interface{}
	err := db.QueryRow("SELECT Id FROM "+dashboard+".Config_dashboard WHERE user =  ?  AND title_header = ? ;", username, dashboardTitle).Scan(&id)
	if err != nil {
		log.Print(err)
	}

	id_dashboard := id
	var defaultMain, mono_multi sql.NullString
	var defaultTarget sql.NullString
	var targetWidget sql.NullString
	err2 := db.QueryRow("SELECT defaultParametersMainWidget, defaultParametersTargetWidget, targetWidget , mono_multi FROM "+dashboard+".WidgetsIconsMap AS iconsMap LEFT JOIN "+dashboard+".Widgets AS widgets ON"+
		" iconsMap.mainWidget = widgets.id_type_widget WHERE iconsMap.mainWidget = ? AND iconsMap.targetWidget = '';", newWidgetType).Scan(&defaultMain, &defaultTarget, &targetWidget, &mono_multi)

	if err2 != nil {
		log.Print(err2)
		return false, id
	} else if defaultMain.Valid {
		log.Print(newWidgetType, " ", defaultMain, " ", mono_multi)
		dbRow2 := processingMsg2([]byte(defaultMain.String))
		if defaultTarget.Valid {
			_ = processingMsg2([]byte(defaultTarget.String))
		}

		if mono_multi.String == "Mono" {

			// caso widget selezionato di tipo "Mono"

			if !targetWidget.Valid || targetWidget.String == "" {

				// caso widget selezionato di tipo singolo (mancano i series e qualcun'altro)

				var auto_increment string

				err := db.QueryRow("SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'Config_widget_dashboard';", dashboard).Scan(&auto_increment)

				if err != nil {

					log.Print(err)
					return false, id

				}

				// calcolo del nextId

				if auto_increment != "" && auto_increment != "<nil>" {
					nextId = auto_increment
				}

				// calcolo del first freee row

				var max interface{}
				err = db.QueryRow("SELECT MAX(n_row + size_rows) AS maxRow  FROM "+dashboard+".Config_widget_dashboard WHERE id_dashboard = ?;", id_dashboard).Scan(&max)

				if err != nil {
					log.Print(err)
					return false, id
				} else {
					if max == nil {
						firstFreeRow = 1
					} else {
						firstFreeRow = max
					}

					// costruzione n_row ed n_columns

					n_row = firstFreeRow
					n_column = 1

					// costruzione size_rows e size_columns

					size_rows := dbRow2["size_rows"]
					size_columns := dbRow2["size_columns"]

					// costruzione nome widget

					creator := username
					type_w := widgetType
					name_w := strings.Replace(id_metric.(string), "+", "", -1) + "_" + fmt.Sprint(id_dashboard.(int64)) + type_w.(string) + string(nextId)
					name_w = strings.Replace(name_w, "%20", "NBSP", -1)

					if type_w == "widgetExternalContent" {
						dbRow2["link_w"] = "http://www.disit.org"
					}
					dbRow2["actuatorTarget"] = "app"

					// inserimento su db

					_, err2 := db.Exec("INSERT INTO "+dashboard+".Config_widget_dashboard(appId, flowId, nrMetricType, nodeId, name_w, id_dashboard, id_metric, type_w,"+
						" n_row, n_column, size_rows,size_columns, title_w, color_w, frequency_w,temporal_range_w, municipality_w,"+
						" infoMessage_w, link_w, parameters, frame_color_w, udm, udmPos,fontSize, fontColor, controlsPosition, showTitle,"+
						" controlsVisibility, zoomFactor, defaultTab,zoomControlsColor, scaleX, scaleY, headerFontColor, styleParameters, "+
						"infoJson, serviceUri, viewMode, hospitalList, notificatorRegistered,notificatorEnabled, enableFullscreenTab, "+
						"enableFullscreenModal, fontFamily, entityJson, attributeName, creator, lastEditor, canceller,lastEditDate, cancelDate, "+
						"actuatorTarget, actuatorEntity, actuatorAttribute, chartColor, dataLabelsFontSize, dataLabelsFontColor, chartLabelsFontSize,"+
						"chartLabelsFontColor, sm_based, rowParameters, sm_field, wizardRowIds) VALUES(?, ?, ?, ?, ?, ?, ?, ?,"+
						" ?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?, ?, ?,?, ?, ?,?, ?, ?,?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?,"+
						" ?, ?, ?, ?, ? ,? , null);", appId, flowId, metricType, nodeId, name_w, id_dashboard,
						id_metric, type_w, n_row, n_column, size_rows, size_columns, title_w, dbRow2["color_w"], dbRow2["frequency_w"],
						dbRow2["temporal_range_w"], dbRow2["municipality_w"], dbRow2["infoMessage_w"], dbRow2["link_w"], dbRow2["parameters"],
						dbRow2["frame_color_w"], dbRow2["udm"], dbRow2["udmPos"], dbRow2["fontSize"], dbRow2["fontColor"], dbRow2["controlsPosition"],
						dbRow2["showTitle"], dbRow2["controlsVisibility"], dbRow2["zoomFactor"], dbRow2["defaultTab"], dbRow2["zoomControlsColor"], dbRow2["scaleX"],
						dbRow2["scaleY"], dbRow2["headerFontColor"], dbRow2["styleParameters"], dbRow2["infoJson"], dbRow2["serviceUri"], dbRow2["viewMode"],
						dbRow2["hospitalList"], dbRow2["notificatorRegistered"], dbRow2["notificatorEnabled"], dbRow2["enableFullscreenTab"],
						dbRow2["enableFullscreenModal"], dbRow2["fontFamily"], dbRow2["entityJson"], dbRow2["attributeName"], creator, sql.NullString{}, dbRow2["canceller"],
						dbRow2["lastEditDate"], dbRow2["cancelDate"], dbRow2["actuatorTarget"], dbRow2["actuatorEntity"], dbRow2["actuatorAttribute"],
						dbRow2["chartColor"], dbRow2["dataLabelsFontSize"], dbRow2["dataLabelsFontColor"], dbRow2["chartLabelsFontSize"], dbRow2["chartLabelsFontColor"],
						"no", sql.NullString{}, sql.NullString{})

					if err2 != nil {

						log.Print(err2)
						return false, id

					} else {
						return true, id
					}
				}
				// se si esce dal ciclo e si arriva qui, si e` sicuramente scritto correttamente su db

				return true, id

			} else {
				log.Print("non gestito targetWidget <", targetWidget, ">")
				// CASO WIDGET COMBO PER ORA NON SI USA
			}
		} else {
			log.Print("non gestito mono/multi: ", mono_multi)
		}
	} else {
		log.Print("ERROR defaultMain is null")
		return false, id
	}

	return false, id
}
