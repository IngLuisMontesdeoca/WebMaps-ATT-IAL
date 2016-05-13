package httpRequest;

import java.io.IOException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.Hashtable;
import java.util.logging.Level;
import java.util.logging.Logger;
import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.DefaultHandler;
import org.xml.sax.helpers.XMLReaderFactory;

/**
 *
 * @author Luis Montes
 */
public class parserTCS extends DefaultHandler {

    private final XMLReader xr;
    private String _xmlRequest = "";
    private String _xmlResponse = "";
    private String _xmlValue = "";
    private String _xmlRESID = "";
    private String _xmlRESERR = "";
    private String _xmlADDINFO = "";
    private String _xmlPOSERR = "";
    private String _xmlPTN = "";
    private String _xmlTIME = "";
    private String _xmlLONGITUDE = "";
    private String _xmlLATITUDE = "";
    private String _xmlLONGITUDEUMT = "";
    private String _xmlLATITUDEUMT = "";
    private String _xmlRADIO = "";
    private Date _xmlDate;
    private String _xmlTIMESTAMP;
    public int _xmlCACHETIME;
    private int _reqThread;

    private String _xmlPosMethod = "";
    private String _xmlLocMethod = "";

    /**
     * @author: Luis Montes
     * @description: Constructor
     * @created: 23/10/2012
     * @param:
     * @return:
     */
    public parserTCS() throws SAXException {
        xr = XMLReaderFactory.createXMLReader();
        xr.setContentHandler(this);
        xr.setErrorHandler(this);
        try {
            this._xmlTIMESTAMP = "-6";
            this._xmlCACHETIME = 5;
        } catch (Exception e) {
            System.out.println("XMLParser|Error-Obteniendo atributos del archivo de configuracion|Message=" + e.getMessage());

        }
    }

    /**
     * @author: Luis Montes
     * @description: Metodo para leer la cadena XML
     * @created: 23/10/2012
     * @param: String stringXML .- Cadena XML
     * @return:
     */
    public void leer(final String stringXML) throws IOException, SAXException {
        try {
            _xmlRequest = stringXML;
            xr.parse(new InputSource(new java.io.StringReader(stringXML)));
        } catch (Exception e) {
            System.out.println("XMLParser|Error-General al parsear cadena MLP|Message=" + e.getMessage());
            e.getStackTrace();
        }
    }

    /**
     * @author: Luis Montes
     * @description: Obtener respuesta del parseo
     * @created: 23/10/2012
     * @param: String _xmlResponse .- Respuesta del parseo
     * @return:
     */
    public String _xmlGetResponse() {
        return _xmlResponse;
    }

    /**
     * @author: Luis Montes
     * @description: Comienzo del Documento XML
     * @created: 23/10/2012
     * @param:
     * @return:
     */
    @Override
    public void startDocument() {
        try {
            this._xmlRestartVars();
        } catch (Exception e) {
            System.out.println("XMLParser|Error-Parseando inicio de cadena MLP|Message=" + e.getMessage());
        }
    }

    /**
     * @author: Luis Montes
     * @description: Final del Documento XML
     * @created: 23/10/2012
     * @param:
     * @return:
     */
    @Override
    public void endDocument() {
        try {
            if (!"".equals(this._xmlRESID) && this._xmlPTN == "") {
                //Verficar el valor de  _xmlTIME
                _xmlGetDate();
                String _error = _getError(this._xmlRESID, "4");
                this._xmlResponse = "ERRORDISPATCHER/" + _xmlRESID + "/" + _xmlRESERR + "/" + _xmlADDINFO + "/" + _error + "/" + _xmlTIME;
            } else {
                this._xmlResponse = _xmlResponse.substring(0, _xmlResponse.length() - 3);
            }
        } catch (Exception e) {
            System.out.println("XMLParser|Error-Parseando caracteres de final de la cadena MLP|Message=" + e.getMessage() + "|" + _xmlResponse.length());
        }
    }

    public String _getError(String _code, String _generic) {
        String _error = "";
        try {
            switch (_code) {
                case "1":
                    if (this._xmlADDINFO.indexOf("lookupSubscriberList") != -1) {
                        _error = "500";
                    } else if (this._xmlADDINFO.indexOf("Connection refused") != -1) {
                        _error = "501";
                    } else {
                        _error = _generic;
                    }
                    break;
                case "2":
                    _error = "502";
                    break;
                default:
                    _error = _generic;
                    break;
            }
        } catch (Exception e) {
            System.out.println("XMLParser|Error-Formando cadena de error|Message=" + e.getMessage());
        }
        return _error;
    }

    /**
     * @author: Luis Montes
     * @description: Comienzo de un nuevo tag
     * @created: 23/10/2012
     * @param: String uri .- uri
     * @param: String name .- name
     * @param: String qName .- qName
     * @param: String atts .- atts
     * @return:
     */
    @Override
    public void startElement(String uri, String name, String qName, Attributes atts) {
        try {
            if ("result".equals(name)) {
                this._xmlRESID = atts.getValue(0);
            }
        } catch (Exception e) {
            System.out.println("XMLParser|Error-Parseando caracteres de inicio de elemento|Message=" + e.getMessage());
        }
    }

    /**
     * @author: Luis Montes
     * @description: Lectura de valores de cada tag
     * @created: 23/10/2012
     * @param: String uri .- uri
     * @param: String name .- name
     * @param: String qName .- qName
     * @param: String atts .- atts
     * @return:
     */
    @Override
    public void endElement(String uri, String name, String qName) {
        try {
            switch (name) {
                case "msid"://PTN
                    _xmlPTN = this._xmlValue;
                    break;
                case "time":
                    try {
                        //TIME
                        _xmlDate = _xmlGetDateResponse(this._xmlValue);
                        if (_xmlRESERR != "") {
                            //concatenar respuesta
                            _xmlDoResponseString();
                        }
                    } catch (ParseException ex) {
                        //Logger.getLogger(XMLParser.class.getName()).log(Level.SEVERE, null, ex);
                    }
                    break;
                case "X"://LATITUDE
                    try {
                        if ("0.0".equals(_xmlLONGITUDE)) {
                            _xmlLATITUDE = "0";
                        } else {
                            //_xmlLATITUDE = this._xmlValue;
                            _xmlLATITUDE = String.valueOf(_xmlGetCoordinate(this._xmlValue));
                        }
                    } catch (Exception exc) {
                        _xmlLATITUDEUMT = this._xmlValue;
                        _xmlLATITUDE = "0";
                    }

                    break;
                case "Y"://LONGITUDE
                    try {
                        if ("0.0".equals(_xmlLATITUDE)) {
                            _xmlLONGITUDE = "0";
                        } else {
                            //_xmlLONGITUDE = this._xmlValue;
                            _xmlLONGITUDE = String.valueOf(_xmlGetCoordinate(this._xmlValue));
                        }
                    } catch (Exception exc) {
                        _xmlLONGITUDEUMT = this._xmlValue;
                        _xmlLONGITUDE = "0";
                    }
                    break;
                case "radius"://RADIO CIRCULAR
                case "inRadius"://RADIO INTERNO
                case "outRadius"://RADIO EXTERNO
                    if(name == "inRadius" &&  _xmlRequest.indexOf("outRadius") != -1  ){
                        return;
                    }                            
                    if (_xmlLocMethod == "") {
                        _xmlRADIO = this._xmlValue;
                        this._xmlGetMetodo();
                    }
                    //System.out.println("XMLParser|_xmlLocMethod=" + _xmlLocMethod + " _xmlRADIO=|" + _xmlRADIO + "|");
                    _xmlDoResponseString();
                    break;
                case "semiMajor"://RADIO
                    if (_xmlLocMethod == "") {
                        _xmlRADIO = this._xmlValue;
                        this._xmlGetMetodo();
                        /**
                         * ** Develop ****
                         * this._reqLog._logSaveLogThread("XMLParser|_xmlRADIO="
                         * + _xmlRADIO, String.valueOf(this._reqThread));
                         * this._reqLog._logSaveLogThread("XMLParser|_xmlPosMethod="
                         * + _xmlPosMethod, String.valueOf(this._reqThread));
                         * *** Develop ***
                         */
                    }
                    System.out.println("XMLParser|_xmlLocMethod=" + _xmlLocMethod);
                    //concatenar respuesta
                    _xmlDoResponseString();
                    break;
                case "poserr"://POS_ERR
                    _xmlPOSERR = this._xmlValue;
                    break;
                case "result"://RESULT_CODE
                    _xmlRESERR = this._xmlValue;
                    break;
                case "add_info"://"ADD_INFO
                    _xmlADDINFO = this._xmlValue;
                    break;
            }
        } catch (Exception e) {
            System.out.println("XMLParser|Error-Parseando elementos de la cadena|Message=" + e.getMessage());
        }
    }

    private void getTypeOfLocationByPosMethod() {
        switch (this._xmlPosMethod) {
            case "GPS":
            case "A-GPS":
                this._xmlLocMethod = "A";
                break;
            case "AFLT":
            case "EFLT":
                this._xmlLocMethod = "T";
                break;
            case "CELL":
                this._xmlLocMethod = "C";
                break;
        }
    }

    /**
     * @author: Luis Montes
     * @description: Metodo para obtener el tipo de localizacion en base al
     * radio
     * @created: 03/07/2014
     * @param:
     * @return: void
     */
    private void _xmlGetMetodo() {
        int _radioInt = Integer.parseInt(this._xmlRADIO);
        if (_radioInt <= 95) {
            this._xmlLocMethod = "A";
        } else if (_radioInt >= 96 && _radioInt <= 4998) {
            this._xmlLocMethod = "T";
        } else if (_radioInt >= 4999) {
            this._xmlLocMethod = "C";
        }
    }

    /**
     * @author: Luis Montes
     * @description: Funcion que lee los valores de cada tag
     * @created: 23/10/2012
     * @param: char[] ch .- uri
     * @param: int start .- name
     * @param: int length .- qName
     * @return:
     */
    @Override
    public void characters(char[] ch, int start, int length) throws SAXException {
        try {
            this._xmlValue = String.valueOf(ch, start, length);
        } catch (Exception e) {
            System.out.println("XMLParser|Error-Parseando caracteres de la cadena|Message=" + e.getMessage());
        }
    }

    /**
     * @author: Luis Montes
     * @description: Verificar si la repsuesta devolvio fecha
     * @created: 23/10/2012
     * @param: void
     * @return: void
     */
    private void _xmlGetDate() {
        if (_xmlTIME == "") {
            _xmlGetDateServer();
        }
    }

    /**
     * @author: Luis Montes
     * @description: Funcion para convertir la cadena de la fecha Date
     * @created: 23/10/2012
     * @param: String fecha .- uri
     * @return: Date _date .- Fecha
     */
    private Date _xmlGetDateResponse(String fecha) throws ParseException {
        Calendar _cal = Calendar.getInstance();
        try {
            SimpleDateFormat _dateFormat = new SimpleDateFormat("dd/MM/yyyy HH:mm:ss");
            Date _date = _dateFormat.parse(fecha.substring(6, 8) + "/" + fecha.substring(4, 6) + "/" + fecha.substring(0, 4) + " " + fecha.substring(8, 10) + ":" + fecha.substring(10, 12) + ":" + fecha.substring(12, 14));

            _cal.set(_date.getYear() + 1900, _date.getMonth(), _date.getDate(), _date.getHours(), _date.getMinutes(), _date.getSeconds());
            _cal.add(Calendar.HOUR, Integer.valueOf(this._xmlTIMESTAMP));
            Date _response = _cal.getTime();
            String _month = (_response.getMonth() < 9) ? ("0" + String.valueOf((_response.getMonth() + 1))) : String.valueOf(_response.getMonth() + 1);
            String _day = (_response.getDate() < 10) ? ("0" + String.valueOf(_response.getDate())) : String.valueOf(_response.getDate());
            String _hour = (_response.getHours() < 10) ? ("0" + String.valueOf(_response.getHours())) : String.valueOf(_response.getHours());
            String _minute = (_response.getMinutes() < 10) ? ("0" + String.valueOf(_response.getMinutes())) : String.valueOf(_response.getMinutes());
            String _second = (_response.getSeconds() < 10) ? ("0" + String.valueOf(_response.getSeconds())) : String.valueOf(_response.getSeconds());
            _xmlTIME = (_response.getYear() + 1900) + "-" + _month + "-" + _day + " " + _hour + ":" + _minute + ":" + _second;
        } catch (Exception e) {
            System.out.println("XMLParser|Error-Parseando fecha de localizacion|Message=" + e.getMessage());
        }
        return _cal.getTime();
    }

    /**
     * @author: Luis Montes
     * @description: Funcion para obtener la fecha en el servidor
     * @created: 23/10/2012
     * @param: void
     * @return: void
     */
    private void _xmlGetDateServer() {
        try {
            SimpleDateFormat _dateFormat = new SimpleDateFormat("dd/MM/yyyy HH:mm:ss");
            Date _date = new Date();
            Calendar _cal = Calendar.getInstance();
            _cal.set(_date.getYear() + 1900, _date.getMonth(), _date.getDate(), _date.getHours(), _date.getMinutes(), _date.getSeconds());
            Date _response = _cal.getTime();
            String _month = (_response.getMonth() < 9) ? ("0" + String.valueOf((_response.getMonth() + 1))) : String.valueOf(_response.getMonth() + 1);
            String _day = (_response.getDate() < 10) ? ("0" + String.valueOf(_response.getDate())) : String.valueOf(_response.getDate());
            String _hour = (_response.getHours() < 10) ? ("0" + String.valueOf(_response.getHours())) : String.valueOf(_response.getHours());
            String _minute = (_response.getMinutes() < 10) ? ("0" + String.valueOf(_response.getMinutes())) : String.valueOf(_response.getMinutes());
            String _second = (_response.getSeconds() < 10) ? ("0" + String.valueOf(_response.getSeconds())) : String.valueOf(_response.getSeconds());
            _xmlTIME = (_response.getYear() + 1900) + "-" + _month + "-" + _day + " " + _hour + ":" + _minute + ":" + _second;
        } catch (Exception e) {
            System.out.println("XMLParser|Error-Obteniendo fecha del servidor|Message=" + e.getMessage());
        }
    }

    /**
     * @author: Luis Montes
     * @description: Convetir coordenadas
     * @created: 23/10/2012
     * @param: String _xmlCoordiante .- coordenada en grados
     * @return: double _coordinate .- Coordenada en formato double
     */
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
        }
        return _coordinate;
    }

    /**
     * @author: Luis Montes
     * @description: Construtir cadena de respuesta
     * @created: 23/10/2012
     * @param:
     * @return:
     */
    public void _xmlDoResponseString() {
        try {
            if (!"".equals(_xmlRESERR)) {
                String _errorCode = this._getError(_xmlRESID, "0");
                _xmlResponse += _xmlPTN + "/0/" + _xmlTIME + "/" + _xmlRESID + "/" + _xmlRESERR + "/" + _xmlADDINFO + "/" + _errorCode + "-/-";
            } else {
                //Verficar el valor de  _xmlTIME
                _xmlGetDate();
                //Verificar si es posicion de cache
                Calendar _cal = Calendar.getInstance();
                //Obtener diferencia en minutos
                long _dif = ((_cal.getTime().getTime() - this._xmlDate.getTime()) / (1000 * 60));

                if (_xmlLONGITUDE != "0" && _xmlLONGITUDE != "0.0") {
                    if (_dif <= this._xmlCACHETIME) {
                        _xmlResponse += _xmlPTN + "/1/" + _xmlLONGITUDE + "/" + _xmlLATITUDE + "/" + _xmlRADIO + "/" + _xmlTIME + "/" + _xmlLocMethod + "-/-";
                    } else {
                        _xmlResponse += _xmlPTN + "/3/" + _xmlLONGITUDE + "/" + _xmlLATITUDE + "/" + _xmlRADIO + "/" + _xmlTIME + "/" + _xmlLocMethod + "-/-";
                    }
                }/*else{
                 _xmlResponse += _xmlPTN + "/0/" + _xmlTIME + "/0/FORMAT ERR/FORMAT ERR/4-/-";
                 }*/


            }
            //reiniciar variables
            this._xmlPOSERR = "";
            this._xmlRESID = "";
            this._xmlRESERR = "";
            this._xmlADDINFO = "";
            this._xmlTIME = "";
            this._xmlLONGITUDE = "";
            this._xmlLATITUDE = "";
            this._xmlRADIO = "";
            this._xmlPosMethod = "";
            this._xmlLocMethod = "";
        } catch (Exception e) {
            System.out.println("XMLParser|Error-Formando cadena de respuesta de parseo|Message=" + e.getMessage());
        }
    }

    /**
     * @author: Luis Montes
     * @description: Reiniciar variables locales
     * @created: 23/10/2012
     * @param:
     * @return:
     */
    private void _xmlRestartVars() {
        this._xmlResponse = "";
        this._xmlRESID = "";
        this._xmlRESERR = "";
        this._xmlADDINFO = "";
        this._xmlPTN = "";
        this._xmlTIME = "";
        this._xmlLONGITUDE = "";
        this._xmlLATITUDE = "";
        this._xmlRADIO = "";
        this._xmlPosMethod = "";
        this._xmlLocMethod = "";
    }
}
