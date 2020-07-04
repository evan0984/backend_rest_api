<?php

namespace common\components;

use Yii;

class MailHelper
{   
    //send email for new lead
    /* example:
        $lead['first_name']='john';
        $lead['last_name']='cool';
        $lead = json_decode(json_encode($lead));
        MailHelper::newLead($lead);
    */
    public static function newLead($lead) {
        Yii::$app->mailer->compose(['html' => '@common/mail/layouts/html/new_lead', 'text' => '@common/mail/layouts/text/new_lead'], ['lead'=>$lead])
            ->setFrom(['budmeow420@budmeow.com'])
            ->setTo("ninzzo@softpro.ua")
            ->setSubject('New lead')
            ->send(); 
    }
	
}


 


