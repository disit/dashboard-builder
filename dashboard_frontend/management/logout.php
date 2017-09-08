<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

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
    
    unset($_SESSION['loggedUsername']);
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
    }
    
    header("location: index.php"); 

