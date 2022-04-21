<?php

new ultraMsgChatBot("71t5bkolgwgus68k", "instance5460");

class ultraMsgChatBot
{
    var $client;
    public function __construct($ultramsg_token, $instance_id)
    {
        //incloud ultramsg class
        //https://github.com/ultramsg/whatsapp-php-sdk

        //require_once ('vendor/autoload.php'); // if you use Composer 
        //composer require ultramsg/whatsapp-php-sdk

        require_once('ultramsg.class.php'); //Latest 2.0.3 stable
        //Include a dictionary to generate random words and sentences
        require_once('ultramsg-dictionary.php');

        $ultramsgDictionary = new ultramsgDictionary();
        $this->client = new UltraMsg\WhatsAppApi($ultramsg_token, $instance_id);

        //get the JSON body from the instance
        $json = file_get_contents('php://input');
        $decoded = json_decode($json, true);
        //write parsed JSON to the 'requests.log' for debugging
        ob_start();
        var_dump($decoded);
        $input = ob_get_contents();
        ob_end_clean();
        file_put_contents('requests.log', $input . PHP_EOL, FILE_APPEND);

        ////////  Processing incoming messages
        if (isset($decoded['data'])) {
            $message = $decoded['data'];
            $text = $this->convert($message['body']);

            // message shouldn't be send from your WhatsApp number, because it calls recursion
            if (!$message['fromMe']) {
                $to = $message['from'];
                $val = mb_strtolower($text, 'UTF-8');
                switch ($val) {
                    case in_array($val, $ultramsgDictionary->welcomeIntent()): {
                            $randMsg = $ultramsgDictionary->welcomeResponses();
                            $this->client->sendChatMessage($to, $randMsg);
                            break;
                        }
                    case '1': {
                            $this->client->sendChatMessage($to, date('d.m.Y H:i:s'));
                            break;
                        }
                    case '2': {
                            $this->client->sendImageMessage($to, "image Caption", "https://file-example.s3-accelerate.amazonaws.com/images/test.jpg");
                            break;
                        }
                    case '3': {
                            $this->client->sendDocumentMessage($to, "cv.pdf", "https://file-example.s3-accelerate.amazonaws.com/documents/cv.pdf");
                            break;
                        }
                    case '4': {
                            $this->client->sendAudioMessage($to, "https://file-example.s3-accelerate.amazonaws.com/audio/2.mp3");
                            break;
                        }
                    case '5': {
                            $this->client->sendVoiceMessage($to, "https://file-example.s3-accelerate.amazonaws.com/voice/oog_example.ogg");
                            break;
                        }
                    case '6': {
                            $this->client->sendVideoMessage($to, "https://file-example.s3-accelerate.amazonaws.com/video/test.mp4");
                            break;
                        }
                    case '7': {
                            $this->client->sendContactMessage($to, "14000000001@c.us");
                            break;
                        }
                    case '8': {
                            $this->client->sendChatMessage($to, $ultramsgDictionary->generateRandomSentence());

                            break;
                        }

                    case '9': {
                            $this->client->sendChatMessage($to, $ultramsgDictionary->generateRandomJoke());
                            break;
                        }

                    case '10': {
                            $this->client->sendImageMessage($to, "Random Image", $ultramsgDictionary->generateRandomImage());
                            break;
                        }

                        // Incorrect command
                    default: {
                            $this->welcome($message['from'], true);
                            break;
                        }
                }
            }
        }
    }


    public function welcome($to, $noWelcome = false)
    {
        $welcomeStr = ($noWelcome) ? "```📢 Incorrect command 📢 ```\nPorfavor escoja uno de estos *commands*:\n" : "welcome to ultramsg bot Demo \n";
        $this->client->sendChatMessage(
            $to,
            $welcomeStr .
                "\n" .
                "1️⃣ : Show server time.\n" .
                "2️⃣ : Send Image.\n" .
                "3️⃣ : Send Document.\n" .
                "4️⃣ : Send Audio.\n" .
                "5️⃣ : Send Voice.\n" .
                "6️⃣ : Send Video.\n" .
                "7️⃣ : Send Contact.\n" .
                "8️⃣ : Send Random Sentence.\n" .
                "9️⃣ : Send Random Joke.\n" .
                "🔟 : Send Random Image.\n"

        );
    }

    //convert Arabic/Persian numbers to English 
    public function convert($string)
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١', '٠'];
        $num = range(0, 9);
        $convertedPersianNums = str_replace($persian, $num, $string);
        $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);
        return $englishNumbersOnly;
    }
}
