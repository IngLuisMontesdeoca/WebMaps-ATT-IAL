<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class fDispositivo
{
	public static function isMobile()
	{
		$detect = new Mobile_Detect;
		
		if(($detect->isMobile()) || ($detect->isTablet()))
		{
			if($detect->isAndroidOS())
			{
				if(!($detect->version('Android') > 2.1) )
					return "omobile";
			}
			
			if($detect->isBb() || $detect->isMotorola())
				return "omobile";
                        
                        
			
		return "nmobile";
		}
		else
			return "web";
	}
	
}