/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab https://www.disit.org - University of Florence

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */

function addLink(name, url, linkElement, elementToBeWrapped, target)
{
    if(url) 
    {
        if(linkElement.length === 0)
        {
            if (target === null)
            {
              //  console.log("Arriva in widgetLinkHandler.js CASO BLANK TARGET = " + target);
                linkElement = $("<a id='" + name + "_link_w' href='" + url + "' target='_blank' class='elementLink2'></a>");
                elementToBeWrapped.wrap(linkElement);
            }
            else
            {
              //  console.log("Arriva in widgetLinkHandler.js  CASO SAME TARGET = " + target);
                linkElement = $("<a id='" + name + "_link_w' href='" + url + "' target='"+ target + "' class='elementLink2'></a>");
                elementToBeWrapped.wrap(linkElement);
            }
        }
    }
}


