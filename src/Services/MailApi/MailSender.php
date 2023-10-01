<?php

namespace App\Services\MailApi;
use Mailjet\Client;
use Mailjet\Resources;

class MailSender
{

    public function send($emailTo, $name, $subject, $content){

        $mj = new Client($_ENV['MAIL_API_KEY'],$_ENV['MAIL_API_PRIVATE_KEY'],true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "winigajordan@gmail.com",
                        'Name' => "Light Bank"
                    ],
                    'To' => [
                        [
                            'Email' => $emailTo,
                            'Name' => $name
                        ]
                    ],
                    'Subject' => $subject,
                    'TextPart' => $content
                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);

    }
}