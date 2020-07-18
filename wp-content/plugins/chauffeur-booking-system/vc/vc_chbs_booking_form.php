<?php

/******************************************************************************/
/******************************************************************************/

class WPBakeryShortCode_VC_CHBS_Booking_Form
{
    /**************************************************************************/
    
    function __construct() 
    {
        add_action('init',array($this,'vcMapping'));
    }
     
    /**************************************************************************/     

    public function vcMapping()
    {
        $Currency=new CHBSCurrency();
        $BookingForm=new CHBSBookingForm();
        $VisualComposer=new CHBSVisualComposer();
        
        vc_map
        ( 
            array
            (
                'base'                                                          =>  CHBSBookingForm::getShortcodeName(),
                'name'                                                          =>  __('Chauffeur Booking Form','chauffeur-booking-system'),
                'description'                                                   =>  __('Displays booking from.','chauffeur-booking-system'), 
                'category'                                                      =>  __('Content','templatica'),  
                'params'                                                        =>  array
                (   
                    array
                    (
                        'type'                                                  =>  'dropdown',
                        'param_name'                                            =>  'booking_form_id',
                        'heading'                                               =>  __('Booking form','chauffeur-booking-system'),
                        'description'                                           =>  __('Select booking form which has to be displayed.','chauffeur-booking-system'),
                        'value'                                                 =>  $VisualComposer->createParamDictionary($BookingForm->getDictionary()),
                        'admin_label'                                           =>  true
                    ),
                    array
                    (
                        'type'                                                  =>  'dropdown',
                        'param_name'                                            =>  'currency',
                        'heading'                                               =>  __('Currency','chauffeur-booking-system'),
                        'description'                                           =>  __('Select currency of booking form.','chauffeur-booking-system'),
                        'value'                                                 =>  $VisualComposer->createParamDictionary($Currency->getCurrency()),
                        'admin_label'                                           =>  true
                    )  
                )
            )
        );         
    } 
    
    /**************************************************************************/
} 
 
new WPBakeryShortCode_VC_CHBS_Booking_Form(); 