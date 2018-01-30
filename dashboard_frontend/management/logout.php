<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
    session_start();
    include '../config.php';
    include 'process-form.php';
    
    if(isset($_SESSION['loggedRole']))
    {
        $username = $_SESSION['loggedUsername'];
    
        /*unset($_SESSION['loggedUsername']);
        unset($_SESSION['loggedRole']);
        unset($_SESSION['loggedType']);
        unset($_SESSION['dashboardId']);
        unset($_SESSION['dashboardAuthorName']);
        unset($_SESSION['dashboardAuthorRole']);
        unset($_SESSION['dashboardTitle']);

        foreach ($_SESSION as $key=>$val)
        {
           if(strpos($key, 'dashViewUsername') !== false) 
           { 
              unset($_SESSION[$key]);
           }
        }*/

        if(isset($_SESSION['sessionExpired']))
        {
            if($_SESSION['sessionExpired'] == true)
            {
                $newLocation = "location: index.php?sessionExpired=true";
            }
            else
            {
               $newLocation = "location: index.php";
            }
        }
        else
        {
            $newLocation = "location: index.php";
        }

        $_SESSION = array();

        if(ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        header($newLocation); 

        notificatorLogout($username, $notificatorApiUsr, $notificatorApiPwd, $notificatorUrl, $ldapTool);
    }
    
    
    
    
    

