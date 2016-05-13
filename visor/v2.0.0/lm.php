<?php
        if(!(isset($_GET['e']))&&
                !(isset($_GET['c'])))
		header('Location: Im.php?r=ialarm/error');
	else
        {
            if((isset($_GET['e'])))
		header('Location: Im.php/fDispositivo?e='.$_GET['e']."e");
            
            if((isset($_GET['c'])))
                header('Location: Im.php/fDispositivo?e='.$_GET['c']."c");
        }
?>