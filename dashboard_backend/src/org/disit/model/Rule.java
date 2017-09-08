/* Dashboard thresholds evaluator.
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
package org.disit.model;

public class Rule 
{
   private boolean notifyEvents;
   //private String min; //Perché ci può essere -INF
   //private String max; //Perché ci può essere +INF
   private String op;
   private String thr1;
   private String thr2;
   private String desc;
   private int thrCnt;
   private String widgetId;
   private String widgetName;
   private String widgetTitle;
   private String dashboardId;
   private String dashboardTitle;
   private String eventType;
   private String metricName;
   
   public String getMetricName() 
   {
      return metricName;
   }

   public void setMetricName(String metricName) 
   {
      this.metricName = metricName;
   }
   
   public String getEventType() {
      return eventType;
   }

   public void setEventType(String eventType) {
      this.eventType = eventType;
   }

   public String getDashboardTitle() {
      return dashboardTitle;
   }

   public void setDashboardTitle(String dashboardTitle) {
      this.dashboardTitle = dashboardTitle;
   }

   public String getDashboardId() {
      return dashboardId;
   }
   public void setDashboardId(String dashboardId) {
      this.dashboardId = dashboardId;
   }

   public String getWidgetTitle() {
      return widgetTitle;
   }

   public void setWidgetTitle(String widgetTitle) {
      this.widgetTitle = widgetTitle;
   }

   public String getWidgetName() {
      return widgetName;
   }

   public void setWidgetName(String widgetName) {
      this.widgetName = widgetName;
   }

   public String getWidgetId() {
      return widgetId;
   }

   public void setWidgetId(String widgetId) {
      this.widgetId = widgetId;
   }

   public int getThrCnt() {
      return thrCnt;
   }

   public void setThrCnt(int thrCnt) {
      this.thrCnt = thrCnt;
   }

   public String getDesc() {
      return desc;
   }

   public void setDesc(String desc) {
      this.desc = desc;
   }
   
   public String getOp() {
      return op;
   }

   public void setOp(String op) {
      this.op = op;
   }
   
   public String getThr1() {
      return thr1;
   }

   public void setThr1(String thr1) {
      this.thr1 = thr1;
   }
   
   public String getThr2() {
      return thr2;
   }

   public void setThr2(String thr2) {
      this.thr2 = thr2;
   }

   public boolean getNotifyEvents() {
      return notifyEvents;
   }

   public void setNotifyEvents(boolean notifyEvents) {
      this.notifyEvents = notifyEvents;
   }

   public Rule() 
   {
      
   }
   
   public void buildEventType()
   {
      switch(this.op)
      {
         case "less":
            this.eventType = "Value < " + this.thr1;
            break;
            
         case "lessEqual":
            this.eventType = "Value <= " + this.thr1;
            break;
            
         case "greater":
            this.eventType = "Value > " + this.thr1;
            break;
            
         case "greaterEqual":
            this.eventType = "Value >= " + this.thr1;
            break;
            
         case "equal":
            this.eventType = "Value = " + this.thr1;
            break;
            
         case "notEqual":
            this.eventType = "Value != " + this.thr1;
            break;
            
         case "intervalOpen":
            this.eventType = this.thr1 + " < value < " + this.thr2;
            break;
            
         case "intervalClosed":
            this.eventType = this.thr1 + " <= value <= " + this.thr2;
            break;
            
         case "intervalLeftOpen":
            this.eventType = this.thr1 + " < value <= " + this.thr2;
            break;
            
         case "intervalRightOpen":
            this.eventType = this.thr1 + " <= value < " + this.thr2;
            break;   
      }
      
      if(!this.desc.equals(""))
      {
         this.eventType = this.eventType + " - " + this.desc;
      }
   }

   public Rule(boolean notifyEvents, String op, String thr1, String thr2, String desc, String widgetId, String widgetName, String widgetTitle, String metricName, String dashboardId, String dashboardTitle)    
   {
      this.notifyEvents = notifyEvents;
      this.op = op;
      this.thr1 = thr1;
      this.thr2 = thr2;
      this.desc = desc;
      this.thrCnt = 1;
      this.widgetId = widgetId;
      this.widgetName = widgetName;
      this.widgetTitle = widgetTitle;
      this.metricName = metricName;
      this.dashboardId = dashboardId;
      this.dashboardTitle = dashboardTitle;
      
      this.buildEventType();
   }
}
