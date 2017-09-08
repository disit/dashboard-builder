package org.disit.model;

public class SingleRule 
{

   private boolean notifyEvents;
   //private String min;
   //private String max;
   private String op;
   private String thr1;
   private String thr2;
   private String color;
   private String desc;

   public String getDesc() {
      return desc;
   }

   public void setDesc(String desc) {
      this.desc = desc;
   }

   public String getColor() {
      return color;
   }

   public void setColor(String color) {
      this.color = color;
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

   /*public String getMax() {
      return max;
   }

   public void setMax(String max) {
      this.max = max;
   }

   public String getMin() {
      return min;
   }

   public void setMin(String min) {
      this.min = min;
   }*/

   public boolean getNotifyEvents() {
      return notifyEvents;
   }

   public void setNotifyEvents(boolean notifyEvents) {
      this.notifyEvents = notifyEvents;
   }

   public SingleRule() 
   {
      
   }
}
