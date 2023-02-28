<?php

namespace App\Services\MailApi;
use Mailjet\Client;
use Mailjet\Resources;

class MailSender
{
    private $apiKey = '429492cd53254d9a81b4982846766008';
    private $privateKey = '29981792c09fbbf8fa22865b7af0b88f';
    public function send($emailTo, $name, $subject, $content){

        $mj = new Client($this->apiKey, $this->privateKey,true,['version' => 'v3.1']);
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