<?php

namespace App\Traits;

use App\Models\SummaryReport;
use Mail;

trait SendReport
{
    public function basic_email(SummaryReport $summaryReport) {
        $from = \Config::get('mail.from');
        $admin = \Config::get('mail.admin');
        $data = array('name'=>$admin['name'],'summaryReport'=>$summaryReport);

        Mail::send(['html'=>'mail'], $data, function($message) use ($from,$admin) {
            $message->to($admin['address'], $admin['name'])->subject
            ('Books Summary Report');
            $message->from($from['address'],$from['name']);
        });
    }
}
