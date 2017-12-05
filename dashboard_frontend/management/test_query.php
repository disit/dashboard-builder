<?php
    /* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab https://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

    require_once( "sparqllib.php" );
    header("Content-type: application/json");
    //include librerie necessarie eventualmente
    //connessione al database con i dati ricavati da datasources
    if (isset($_GET['urlDS'])) {
        $hostname = mysqli_real_escape_string($link, $_GET['urlDS']);  //url di datasources    
    } else {
        $risposta = "Sorgente dati non specificata!";
        echo json_encode($risposta);
        //print($risposta);
        exit;
    }

    if (isset($_GET['tipo_acquisizione'])) {
        $modality = mysqli_real_escape_string($link, $_GET['tipo_acquisizione']); //modalità acquisizione
    } else {
        $risposta = "La modalità di acquisizione non è specificata!";
        echo json_encode($risposta);
        exit;
    }

    if (isset($_GET['valore_query'])) {
        $query = mysqli_real_escape_string($link, $_GET['valore_query']); //query   
    } else {
        $query = "";
        if ($query = "") {
            $risposta = "La query non è stata inserita!";
            echo json_encode($risposta);
            exit;
        }
    }
    if (isset($_GET['usernameDS'])) {
        $username = mysqli_real_escape_string($link, $_GET['usernameDS']); //username di datasources
    } else {
        $username = "";
    }
    if (isset($_GET['passwordDS'])) {
        $password = mysqli_real_escape_string($link, $_GET['passwordDS']); //password di datasources  
    } else {
        $password = "";
    }

    if (isset($_GET['databaseDS'])) {
        $database = mysqli_real_escape_string($link, $_GET['databaseDS']);
    } else {
        $risposta = "Il database non è specificato!";
        echo json_encode($risposta);
        exit;
    }

    if (isset($_GET['databaseTypeDS'])) {
        $dataType = mysqli_real_escape_string($link, $_GET['databaseTypeDS']);  //datatype  
    } else {
        $risposta = "Il database Type non è specificato!";
        echo json_encode($risposta);
        exit;
    }

    if ($dataType == 'MySQL') {
        $divisione = explode("//", $hostname);
        $url = $divisione[1];  
        $link = @mysqli_connect($url, $username, $password, $database);
        if (!$link){
            $risposta = "Error: connessione al server fallita! " . mysqli_connect_error();        
            echo json_encode($risposta);
            exit;
        }

        //
        mysqli_set_charset($link, 'utf8');
        if (!mysqli_query($link, $query)) {
            $risposta = "ERROR (mysql): " . mysqli_error($link);
            mysqli_close($link);
            echo json_encode($risposta);
            exit;
        } else {
            $risposta = "Nessun errore riscontrato!";
            mysqli_close($link);
            echo json_encode($risposta);
            exit;
        }
    } else if ($dataType == 'RDFstore') {
        $db = sparql_connect($hostname);
        $data = sparql_get($hostname, $query);
        if (!isset($data)) {
            $risposta = "ERROR (sparql): " . sparql_error();
            echo json_encode($risposta);
            exit;
        } else {
            $risposta = "Nessun errore riscontrato!";
            echo json_encode($risposta);
            exit;
        }
    } else {
        $risposta = "Query non inserita!";
        echo json_encode($risposta);
        exit;
    }

