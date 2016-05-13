/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package httpRequest;

import java.io.IOException;
import java.lang.Thread.UncaughtExceptionHandler;
import java.util.Date;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.logging.Level;
import java.util.logging.Logger;
import org.xml.sax.SAXException;

/**
 *
 * @author Luis Montes
 */
public class Threads implements Runnable {

    private parserTCS _thXMLParserTCS;

    /**
     * @author: Luis Montes
     * @description: Constructor
     * @created: 23/10/2012
     * @param:
     * @return:
     */
    public Threads() throws SAXException {

        this._thXMLParserTCS = new parserTCS();
    }

    /**
     * @author: Luis Montes
     * @description: Metodo run
     * @created: 23/10/2012
     * @param:
     * @return:
     */
    public void run() {
        try {
            //Circular
            //String _thResponse = "<?xml version=\"1.0\"?><!DOCTYPE svc_result SYSTEM \"MLP_SVC_RESULT_320.DTD\"><svc_result ver=\"3.2.0\"><slia ver=\"3.0.0\"><pos><msid type=\"MSISDN\" enc=\"ASC\">525543328662</msid><pd><time utc_off=\"-0700\">20140529084358</time><shape><CircularArea srsName=\"www.epsg.org#4326\"><coord><X>19 32 43.061N</X><Y>099 12 15.019W</Y></coord><angle>135</angle><semiMajor>5000</semiMajor><semiMinor>1000</semiMinor><angularUnit>Degrees</angularUnit><distanceUnit>meter</distanceUnit></CircularArea></shape><alt>200</alt><alt_unc>50</alt_unc><lev_conf>67</lev_conf></pd></pos></slia></svc_result>";
            //CircularArc - CircularArc
            //String _thResponse = "<?xml version=\"1.0\"?><!DOCTYPE svc_result SYSTEM \"MLP_SVC_RESULT_320.DTD\"><svc_result ver=\"3.2.0\"><slia ver=\"3.0.0\"><pos><msid type=\"MSISDN\" enc=\"ASC\">523316619457</msid><pd><time utc_off=\"-0700\">20150415085328</time><shape><CircularArcArea srsName=\"www.epsg.org#4326\"><coord><X>20 40 34.435N</X><Y>103 22 55.285W</Y></coord><inRadius>0</inRadius><outRadius>71</outRadius><startAngle>266.0</startAngle><stopAngle>66.0</stopAngle><angularUnit>Degrees</angularUnit><distanceUnit>meter</distanceUnit></CircularArcArea></shape><lev_conf>67</lev_conf></pd></pos></slia></svc_result>";
            //CircularArc - Circular
            //String _thResponse = "<?xml version=\"1.0\"?><!DOCTYPE svc_result SYSTEM \"MLP_SVC_RESULT_320.DTD\"><svc_result ver=\"3.2.0\"><slia ver=\"3.0.0\"><pos><msid type=\"MSISDN\" enc=\"ASC\">525543328662</msid><pd><time utc_off=\"-0700\">20140529084358</time><shape><CircularArea srsName=\"www.epsg.org#4326\"><coord><X>19 32 43.061N</X><Y>099 12 15.019W</Y></coord><angle>135</angle><semiMajor>5000</semiMajor><semiMinor>1000</semiMinor><angularUnit>Degrees</angularUnit><distanceUnit>meter</distanceUnit></CircularArea></shape><alt>200</alt><alt_unc>50</alt_unc><lev_conf>67</lev_conf></pd></pos></slia></svc_result>";
            
            //CircularArc - CircularArc - Circular
            String _thResponse = "<?xml version=\"1.0\"?><!DOCTYPE svc_result SYSTEM \"MLP_SVC_RESULT_320.DTD\"><svc_result ver=\"3.2.0\"><slia ver=\"3.0.0\"><pos><msid type=\"MSISDN\" enc=\"ASC\">525565790530</msid><pd><time utc_off=\"-0700\">20150405032000</time><shape><CircularArea srsName=\"www.epsg.org#4326\"><coord><X>19 32 43.061N</X><Y>99 12 15.019W</Y></coord><radius>46</radius><distanceUnit>meter</distanceUnit></CircularArea></shape></pd></pos><pos><msid type=\"MSISDN\" enc=\"ASC\">523316619457</msid><pd><time utc_off=\"-0700\">20150405030000</time><shape><CircularArcArea srsName=\"www.epsg.org#4326\"><coord><X>20 40 34.435N</X><Y>103 22 55.285W</Y></coord><inRadius>0</inRadius><outRadius>71</outRadius><startAngle>266.0</startAngle><stopAngle>66.0</stopAngle><angularUnit>Degrees</angularUnit><distanceUnit>meter</distanceUnit></CircularArcArea></shape><lev_conf>67</lev_conf></pd></pos><pos><msid type=\"MSISDN\" enc=\"ASC\">523316619457</msid><pd><time utc_off=\"-0700\">20150405030000</time><shape><CircularArcArea srsName=\"www.epsg.org#4326\"><coord><X>20 40 34.435N</X><Y>103 22 55.285W</Y></coord><inRadius>0</inRadius><outRadius>71</outRadius><startAngle>266.0</startAngle><stopAngle>66.0</stopAngle><angularUnit>Degrees</angularUnit><distanceUnit>meter</distanceUnit></CircularArcArea></shape><lev_conf>67</lev_conf></pd></pos></slia></svc_result>";
            
            //String _thResponse = "<?xml version=\"1.0\"?><!DOCTYPE svc_result SYSTEM \"MLP_SVC_RESULT_320.DTD\"><svc_result ver=\"3.2.0\"><slia ver=\"3.0.0\"><pos><msid type=\"MSISDN\" enc=\"ASC\">525565790530</msid><pd><time utc_off=\"-0700\">20150415080250</time><shape><CircularArea srsName=\"www.epsg.org#4326\"><coord><X>19 32 43.061N</X><Y>99 12 15.019W</Y></coord><radius>46</radius><distanceUnit>meter</distanceUnit></CircularArea></shape></pd></pos></slia></svc_result>";
            this._thXMLParserTCS.leer(_thResponse);
            System.out.println(this._thXMLParserTCS._xmlGetResponse());
        } catch (IOException ex) {
            System.out.println("Err|IOException|Message -> " + ex.getMessage());
        } catch (SAXException ex) {
            System.out.println("Err|SAXException|Message -> " + ex.getMessage());
        } catch (Exception ex) {
            System.out.println("Err|Exception|Message -> " + ex.getMessage());
        }
    }

}
