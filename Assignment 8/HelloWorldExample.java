
/* $Id: HelloWorldExample.java,v 1.2 2001/11/29 18:27:25 remm Exp $
*
*/

import java.io.*;
import java.lang.*;
import java.net.URL;
import java.net.URLConnection;
import javax.servlet.*;
import javax.servlet.http.*;
import javax.xml.parsers.*;
import org.xml.sax.SAXException;
import java.util.*;
import java.io.BufferedReader.*;
import java.net.*;
import org.w3c.dom.*;
import java.io.*;
import java.net.*;
import java.text.*;
import java.util.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.net.URL;
import java.net.MalformedURLException;
import java.util.regex.*;
import java.lang.*;
import java.lang.Number;
import javax.xml.parsers.*;
import java.net.URL;
import java.io.InputStream;
import org.w3c.dom.*;
import java.io.BufferedReader;



/**
* The simplest possible servlet.
*
* @author James Duncan Davidson
*/

public class HelloWorldExample extends HttpServlet {


   public void doGet(HttpServletRequest request,
                     HttpServletResponse response)
       throws IOException, ServletException
   {
     
       String location = request.getParameter("location");
       String type = request.getParameter("type");
       String tempunit = request.getParameter("tempunit");
       try
       {
    	   request.setCharacterEncoding("UTF-8");
    	   PrintWriter out = response.getWriter();
    	   response.setContentType("text/html; charset=UTF-8");
    	   String urlstr = "http://default-environment-q3twzrv5dr.elasticbeanstalk.com/?location="+location+"&type="+type+"&tempunit="+tempunit;
    	   URL url = new URL(urlstr);
    	   URLConnection urlConnection = url.openConnection();
    	   urlConnection.setAllowUserInteraction(false);
    	   InputStream urlStream = url.openStream();
    	   DocumentBuilderFactory domFactory = DocumentBuilderFactory.newInstance();
    	   domFactory.setNamespaceAware(true); 
    	   DocumentBuilder builder = domFactory.newDocumentBuilder();
    	   Document doc = builder.parse(urlStream);
    	   NodeList weatherList = doc.getElementsByTagName("forecast");
    	   int length = weatherList.getLength();
           
    	   String jasonString = "{\"weather\":{\"forecast\":[";
    	   int i=0;
           for(i=0;i<length-1;i++)
           {
           	
    		   Node node = weatherList.item(i);
    		   if (node.getNodeType() == Node.ELEMENT_NODE)
    		   {
    			   Element weather = (Element)node;
    			   String w_text = weather.getAttribute("text");
    			   String w_high = weather.getAttribute("high");
    			   String w_day = weather.getAttribute("day");
    			   String w_low = weather.getAttribute("low");
    			   jasonString+="{\"text\":\""+w_text+"\",\"high\":"+w_high+",\"day\":\""+w_day+"\",\"low\":"+w_low+"},";
    			  
    		   }
    		   
           }
           

		   Node node = weatherList.item(i);
		   if (node.getNodeType() == Node.ELEMENT_NODE)
		   {
			   Element weather = (Element)node;
			   String w_text = weather.getAttribute("text");
			   String w_high = weather.getAttribute("high");
			   String w_day = weather.getAttribute("day");
			   String w_low = weather.getAttribute("low");
			   jasonString+="{\"text\":\""+w_text+"\",\"high\":"+w_high+",\"day\":\""+w_day+"\",\"low\":"+w_low+"}";
			  
		   }
		   
      		   
		   NodeList conditionList = doc.getElementsByTagName("condition");
    	   Node node1 = conditionList.item(0);
		   if (node1.getNodeType() == Node.ELEMENT_NODE)
		   {
			   Element weather = (Element)node1;
    	   	jasonString = jasonString+ "],\"condition\":{";
       		String w_ctext = weather.getAttribute("text");
       		String w_ctemp = weather.getAttribute("temp");
       		jasonString+="\"text\":\""+w_ctext+"\",\"temp\":"+w_ctemp+"},";
		   }
		   
		   
		   
		   NodeList locationList = doc.getElementsByTagName("location");
    	   Node node2 = locationList.item(0);
		   if (node2.getNodeType() == Node.ELEMENT_NODE)
		   {
			   Element weather = (Element)node2;
       		jasonString = jasonString+ "\"location\":";
       
       		String w_region = weather.getAttribute("region");
       		String w_country = weather.getAttribute("country");
       		String w_city = weather.getAttribute("city");
       		jasonString+="{\"region\":\""+w_region+"\",\"country\":\""+w_country+"\",\"city\":\""+w_city+"\"},";
		   }
		   
		   NodeList linkList = doc.getElementsByTagName("link");
    	   Node node3 = (Node)linkList.item(0);
		   if (node3.getNodeType() == Node.ELEMENT_NODE)
		   {
			   	Element weather = (Element)node3;
			   	String w_link = weather.getFirstChild().getNodeValue();
       			jasonString+="\"link\":\""+w_link+"\",";
		   }
		   
		   
		   NodeList imgList = doc.getElementsByTagName("img");
    	   Node node4 = (Node)imgList.item(0);
		   if (node4.getNodeType() == Node.ELEMENT_NODE)
		   {
			   Element weather = (Element)node4;
			   String w_img = weather.getFirstChild().getNodeValue();
			   jasonString+="\"img\":\""+w_img+"\",";
		   }
		   
		  
		   NodeList feedList = doc.getElementsByTagName("feed");
    	   Node node5 = (Node)feedList.item(0);
		   if (node5.getNodeType() == Node.ELEMENT_NODE)
		   {
			   	Element weather = (Element)node5;
       			//String w_feed = weather.getAttribute("feed1");
			   	String w_feed = weather.getFirstChild().getNodeValue();
			   	jasonString+="\"feed\":\""+w_feed+"\",";
		   }
		   
		   NodeList unitList = doc.getElementsByTagName("units");
    	   Node node6 = (Node)unitList.item(0);
    	   
		   if (node6.getNodeType() == Node.ELEMENT_NODE)
		   {
			   Element weather = (Element)node6;
       		jasonString = jasonString+ "\"units\":";
       		String w_temp = weather.getAttribute("temperature");
            jasonString+="{\"temperature\":\""+w_temp+"\"";
		   }
		   jasonString = jasonString + "}}}";
       out.println(jasonString);
   }
   catch(MalformedURLException e)
   {
   	System.out.print(e.getMessage());
   }
   catch(IOException e)
   {
   	System.out.print(e.getMessage());
   }
   catch(Exception e)
   {
   	System.out.print(e.getMessage());
   }

   }
}



