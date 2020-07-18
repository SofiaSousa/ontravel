<?php

/******************************************************************************/
/******************************************************************************/

class CHBSFixerIo
{
    /**************************************************************************/
    
    function __construct()
    {
        
    }
    
    /**************************************************************************/
    
    function getRate()
    {        
        $url='http://data.fixer.io/api/latest?access_key='.CHBSOption::getOption('fixer_io_api_key').'&base='.CHBSCurrency::getBaseCurrency();
        
        if(($content=file_get_contents($url))===false) return(false);
        
        $data=json_decode($content);
        
        if($data->{'success'})
        {
            return($data->{'rates'});
        }
        
        return(false);
    }

    /**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/