/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package httpRequest;

import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

/**
 *
 * @author Luis Montes
 */
class httpRequest {
    /*
     args[0] - Tipo Request
     */

    public static void main(String[] args) throws MalformedURLException, IOException {
        String _reqResponse = "";
        String _reqErr = "";

        try {

            httpRequest _cl = new httpRequest();
            //System.out.println(_cl._xmlGetCoordinate("099 12 15.019W"));
           Thread _T = new Thread(new Threads());
           _T.start();
            
            /*
             String _reqXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>"
             + "<BillingGateway xmlns=\"http://wilaen.com/BillingGateway/services\">"
             + "<BillingCode>"+args[0]+"</BillingCode>"
             + "<User>"+args[1]+"</User>"
             + "<Password>"+args[2]+"</Password>"
             + "<PhoneNumber>"+args[3]+"</PhoneNumber>"
             + "<ReferenceCode>"+args[4]+"</ReferenceCode>"
             + "<ItemId>"+args[5]+"</ItemId>"
             + "<ItemName>"+args[6]+"</ItemName>"
             + "<ItemType>"+args[7]+"</ItemType>"
             + "</BillingGateway>";

             byte[] _arrByte = _reqXML.getBytes();

             URL _reqURL = new URL("http://184.106.12.88:7050/Nextel_Mx/BillingWebService");

             HttpURLConnection _reqConnection = (HttpURLConnection) _reqURL.openConnection();
             _reqConnection.setConnectTimeout(75000);
             _reqConnection.setReadTimeout(75000);
             _reqConnection.setRequestMethod("POST");
             _reqConnection.setRequestProperty("Content-Type", "text/xml");
             _reqConnection.setRequestProperty("Content-Length", "" + Integer.toString(_arrByte.length));
             //_reqConnection.setRequestProperty("Content-Language", "en-US");  
             _reqConnection.setUseCaches(false);
             _reqConnection.setDoOutput(true);

             DataOutputStream wr = new DataOutputStream(_reqConnection.getOutputStream());
             wr.write(_arrByte, 0, _arrByte.length);
             wr.flush();
             wr.close();

             //Procesar respuesta  
             int _resCode = _reqConnection.getResponseCode();//200, 500, 404, etc. 
             InputStream is;
             if (_resCode == HttpURLConnection.HTTP_OK) {
             System.out.println("HTTP200 OK");
             //transformar a string el response 
             is = _reqConnection.getInputStream();
             BufferedReader rd = new BufferedReader(new InputStreamReader(is));
             String line;
             StringBuilder response = new StringBuilder();
             while ((line = rd.readLine()) != null) {
             response.append(line);
             }

             rd.close();
             _reqResponse = response.toString();
             //System.out.println("Respuesta=" + _reqResponse);
             } else {
             //System.out.println("Error al enviar peticion");
             is = _reqConnection.getErrorStream();
             _reqErr = "ERR///Error al enviar peticion///" + is.toString();
             }
             } catch (java.net.SocketTimeoutException e) {
             //System.out.println("Req|Error- (SEF|AGPs) no respondio o esta tardando demasiado tiempo en responder|Message=" + e.getMessage() + "|StackTrace->" + e.getStackTrace());

             _reqErr = "ERR///Timeout///" + e.getMessage();
             } catch (java.net.ConnectException e) {
             //System.out.println("Req|Error- No se logro realizar la conexion hacia (SEF|AGPs)|Message=" + e.getMessage() + "|StackTrace->" + e.getStackTrace());

             _reqErr = "ERR///Connect///" + e.getMessage();*/
        } catch (Exception e) {
            //System.out.println("Req|Error-Respuesta al enviar peticion|Message=" + e.getMessage() + "|StackTrace->" + e.getStackTrace());

            _reqErr = "ERR///Exception///" + e.getMessage();
            System.out.println(_reqErr);
        }
        /*
         if (_reqErr == "") {
         System.out.println("OK///" + _reqResponse);
         } else {
         System.out.println(_reqErr);
         }*/
    }
    
    public double _xmlGetCoordinate(String _xmlCoordiante) {
        double _coordinate = 0;
        try {
            String _posicion = "";
            String[] _contenedor;
            int _degree;
            int _minute;
            float _second;
            double i = 0;
            int g = 0;

            if (_xmlCoordiante.indexOf("W") != -1) {
                g = 1;
                _posicion = "-" + _xmlCoordiante;
            } else {
                _posicion = _xmlCoordiante;
            }

            _posicion = _posicion.replace("W", "");
            _posicion = _posicion.replace("N", "");
            _posicion = _posicion.replace("S", "");
            _posicion = _posicion.replace("E", "");
            _contenedor = _posicion.split(" ");

            _degree = Integer.parseInt(_contenedor[0]);
            _minute = Integer.parseInt(_contenedor[1]);
            _second = Float.parseFloat(_contenedor[2]);
            i = (int) (((((_second / 60) + _minute) / 60) * 1000000));

            if (g == 1) {
                _coordinate = _degree - i / 1000000;
            } else {
                _coordinate = (_degree + i / 1000000);
            }
            
        } catch (Exception e) {
            System.out.println("XMLParser|Error-Parseando Coordenadas|Message=" + e.getMessage());
            e.getStackTrace();
        }
        return _coordinate;
    }

}


