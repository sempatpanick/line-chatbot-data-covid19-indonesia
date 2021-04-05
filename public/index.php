<?php
require __DIR__ . '/../vendor/autoload.php';
 
 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
 
 
use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use \LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use \LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;
 
 
$pass_signature = true;

 
// set LINE channel_access_token and channel_secret
$channel_access_token = "***************"; //YOUR_CHANNEL_ACCESS_TOKEN
$channel_secret = "***************"; //YOUR_CHANNEL_SECRET
 
 
// inisiasi objek bot
$httpClient = new CurlHTTPClient($channel_access_token);
$bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);
 
$app = AppFactory::create();
$app->setBasePath("/public");
 
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello World!");
    return $response;
});
 
 
// buat route untuk webhook
$app->post('/webhook', function (Request $request, Response $response) use ($channel_secret, $bot, $httpClient, $pass_signature) {
    // get request body and line signature header
    $body = $request->getBody();
    $signature = $request->getHeaderLine('HTTP_X_LINE_SIGNATURE');
 
 
    // log body and signature
    file_put_contents('php://stderr', 'Body: ' . $body);
 
 
    if ($pass_signature === false) {
        // is LINE_SIGNATURE exists in request header?
        if (empty($signature)) {
            return $response->withStatus(400, 'Signature not set');
        }
 
 
        // is this request comes from LINE?
        if (!SignatureValidator::validateSignature($body, $channel_secret, $signature)) {
            return $response->withStatus(400, 'Invalid signature');
        }
    }
 
 
    $data = json_decode($body, true);
    if(is_array($data['events'])){
        foreach ($data['events'] as $event)
        {
            if ($event['type'] == 'message') {
                if ($event['message']['type'] == 'text') {
                    if ($event['message']['text'] == '!covid') {
                        $flexTemplate = file_get_contents("../flex_menu.json"); // template flex message
                        $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                            'replyToken' => $event['replyToken'],
                            'messages'   => [
                                [
                                    'type'     => 'flex',
                                    'altText'  => 'Test Flex Message',
                                    'contents' => json_decode($flexTemplate)
                                ]
                            ],
                        ]);
                    } else {
                        $textMessageBuilder1 = new TextMessageBuilder('Silahkan ketik !covid untuk menampilkan menu');
                        $stickerMessageBuilder = new StickerMessageBuilder(1, 106);

                        $multiMessageBuilder = new MultiMessageBuilder();
                        $multiMessageBuilder->add($textMessageBuilder1);
                        $multiMessageBuilder->add($stickerMessageBuilder);
                        $result = $bot->replyMessage($event['replyToken'], $multiMessageBuilder);
                    }
                }
            } else if ($event['type'] == 'postback') {
                if ($event['postback']['data'] == 'data=indonesia') {
                    $data = [
                        'nama' => 'Indonesia',
                        'type' => 'negara',
                        'lokasi' => 'indonesia'
                    ];
                    $data_covid = data_covid($data);
                } else if ($event['postback']['data'] == 'data=provinsi') {
                    $data = [
                        'nama' => 'Provinsi',
                        'type' => 'menu'
                    ];
                    $flexTemplate = file_get_contents("../flex_menu_provinsi.json"); // template flex message
                } else if ($event['postback']['data'] == 'data=kabupatenkota') {
                    $data = [
                        'nama' => 'Kabupaten',
                        'type' => 'menu'
                    ];
                    $flexTemplate = file_get_contents("../flex_menu_kabkota.json"); // template flex message
                } else if ($event['postback']['data'] == 'data=kab_kudus') {
                    $data = [
                        'nama' => 'Kabupaten Kudus',
                        'type' => 'kabkota',
                        'lokasi' => 'kudus'
                    ];
                    $data_covid = data_covid($data);
                } else if ($event['postback']['data'] == 'data=kab_pati') {
                    $data = [
                        'nama' => 'Kabupaten Pati',
                        'type' => 'kabkota',
                        'lokasi' => 'pati'
                    ];
                    $data_covid = data_covid($data);
                } else if ($event['postback']['data'] == 'data=prov_banten') {
                    $data = [
                        'nama' => 'Banten',
                        'type' => 'provinsi',
                        'lokasi' => "prov_banten"
                    ];
                    $data_covid = data_covid($data);
                } else if ($event['postback']['data'] == 'data=prov_diy') {
                    $data = [
                        'nama' => 'Daerah Istimewa Yogyakarta',
                        'type' => 'provinsi',
                        'lokasi' => "prov_diy"
                    ];
                    $data_covid = data_covid($data);
                } else if ($event['postback']['data'] == 'data=prov_dki_jakarta') {
                    $data = [
                        'nama' => 'DKI Jakarta',
                        'type' => 'provinsi',
                        'lokasi' => "prov_dki_jakarta"
                    ];
                    $data_covid = data_covid($data);
                } else if ($event['postback']['data'] == 'data=prov_jawa_barat') {
                    $data = [
                        'nama' => 'Jawa Barat',
                        'type' => 'provinsi',
                        'lokasi' => "prov_jawa_barat"
                    ];
                    $data_covid = data_covid($data);
                } else if ($event['postback']['data'] == 'data=prov_jawa_tengah') {
                    $data = [
                        'nama' => 'Jawa Tengah',
                        'type' => 'provinsi',
                        'lokasi' => "prov_jawa_tengah"
                    ];
                    $data_covid = data_covid($data);
                } else if ($event['postback']['data'] == 'data=prov_jawa_timur') {
                    $data = [
                        'nama' => 'Jawa Timur',
                        'type' => 'provinsi',
                        'lokasi' => "prov_jawa_timur"
                    ];
                    $data_covid = data_covid($data);
                }
                if($data['type'] == "menu"){
                    $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                        'replyToken' => $event['replyToken'],
                        'messages'   => [
                            [
                                'type'     => 'flex',
                                'altText'  => 'Menu '.$data['nama'],
                                'contents' => json_decode($flexTemplate)
                            ]
                        ],
                    ]);
                } else {
                    $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                        'replyToken' => $event['replyToken'],
                        'messages'   => [
                            [
                                'type'     => 'flex',
                                'altText'  => 'Data Covid-19 '.$data['nama'],
                                'contents' => $data_covid
                            ]
                        ],
                    ]);
                }
            }
            $response->getBody()->write(json_encode($result->getJSONDecodedBody()));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($result->getHTTPStatus());
        }
        return $response->withStatus(200, 'for Webhook!'); //buat ngasih response 200 ke pas verify webhook
    }
    return $response->withStatus(400, 'No event sent!');
});
$app->run();

function data_covid($data)
{
    $url = 'https://api.ddg.my.id/covid19/indonesia.php';
    $ch = curl_init();
    $timeout = 5;

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    // Get URL content
    $json_data = curl_exec($ch);
    // close handle to release resources
    curl_close($ch);
    $json_de_data = json_decode($json_data);

    if($json_de_data[0]->provinsi){
        $jumlah_provinsi = count($json_de_data[0]->provinsi);
    } else {
        $jumlah_provinsi = 0;
    }

    if ($data['lokasi'] == 'indonesia') {
        $nama = $data['nama'];
        $positif = $json_de_data[0]->negara[0]->indonesia->positif;
        $sembuh = $json_de_data[0]->negara[0]->indonesia->sembuh;
        $meninggal = $json_de_data[0]->negara[0]->indonesia->meninggal;
        $dirawat = $json_de_data[0]->negara[0]->indonesia->dirawat;
        
        include "../flex_data_kabkota.php";
        
        return $flex_template;
    } else if ($data['lokasi'] == 'kudus') {
        $nama = $data['nama'];
        $positif = $json_de_data[0]->kabupaten[0]->positif;
        $sembuh = $json_de_data[0]->kabupaten[0]->sembuh;
        $meninggal = $json_de_data[0]->kabupaten[0]->meninggal;
        $dirawat = $json_de_data[0]->kabupaten[0]->dirawat;
        
        include "../flex_data_kabkota.php";
        
        return $flex_template;
    } else if ($data['lokasi'] == 'pati') {
        $nama = $data['nama'];
        $positif = $json_de_data[0]->kabupaten[1]->positif;
        $sembuh = $json_de_data[0]->kabupaten[1]->sembuh;
        $meninggal = $json_de_data[0]->kabupaten[1]->meninggal;
        $dirawat = $json_de_data[0]->kabupaten[1]->dirawat;
        
        include "../flex_data_kabkota.php";
        
        return $flex_template;
    } else if ($data['lokasi'] == 'prov_banten') {
        for($i=0;$i<$jumlah_provinsi;$i++){
            if($json_de_data[0]->provinsi[$i]->kode == 36){
                $nama = $json_de_data[0]->provinsi[$i]->nama_provinsi;
                $positif = $json_de_data[0]->provinsi[$i]->positif;
                $sembuh = $json_de_data[0]->provinsi[$i]->sembuh;
                $meninggal = $json_de_data[0]->provinsi[$i]->meninggal;
            }
        }
        
        include "../flex_data_provinsi.php";
        
        return $flex_template;
    } else if ($data['lokasi'] == 'prov_diy') {
        for($i=0;$i<$jumlah_provinsi;$i++){
            if($json_de_data[0]->provinsi[$i]->kode == 34){
                $nama = $json_de_data[0]->provinsi[$i]->nama_provinsi;
                $positif = $json_de_data[0]->provinsi[$i]->positif;
                $sembuh = $json_de_data[0]->provinsi[$i]->sembuh;
                $meninggal = $json_de_data[0]->provinsi[$i]->meninggal;
            }
        }
        
        include "../flex_data_provinsi.php";
        
        return $flex_template;
    } else if ($data['lokasi'] == 'prov_dki_jakarta') {
        for($i=0;$i<$jumlah_provinsi;$i++){
            if($json_de_data[0]->provinsi[$i]->kode == 31){
                $nama = $json_de_data[0]->provinsi[$i]->nama_provinsi;
                $positif = $json_de_data[0]->provinsi[$i]->positif;
                $sembuh = $json_de_data[0]->provinsi[$i]->sembuh;
                $meninggal = $json_de_data[0]->provinsi[$i]->meninggal;
            }
        }
        
        include "../flex_data_provinsi.php";
        
        return $flex_template;
    } else if ($data['lokasi'] == 'prov_jawa_barat') {
        for($i=0;$i<$jumlah_provinsi;$i++){
            if($json_de_data[0]->provinsi[$i]->kode == 32){
                $nama = $json_de_data[0]->provinsi[$i]->nama_provinsi;
                $positif = $json_de_data[0]->provinsi[$i]->positif;
                $sembuh = $json_de_data[0]->provinsi[$i]->sembuh;
                $meninggal = $json_de_data[0]->provinsi[$i]->meninggal;
            }
        }
        
        include "../flex_data_provinsi.php";
        
        return $flex_template;
    } else if ($data['lokasi'] == 'prov_jawa_tengah') {
        for($i=0;$i<$jumlah_provinsi;$i++){
            if($json_de_data[0]->provinsi[$i]->kode == 33){
                $nama = $json_de_data[0]->provinsi[$i]->nama_provinsi;
                $positif = $json_de_data[0]->provinsi[$i]->positif;
                $sembuh = $json_de_data[0]->provinsi[$i]->sembuh;
                $meninggal = $json_de_data[0]->provinsi[$i]->meninggal;
            }
        }
        
        include "../flex_data_provinsi.php";
        
        return $flex_template;
    } else if ($data['lokasi'] == 'prov_jawa_timur') {
        for($i=0;$i<$jumlah_provinsi;$i++){
            if($json_de_data[0]->provinsi[$i]->kode == 35){
                $nama = $json_de_data[0]->provinsi[$i]->nama_provinsi;
                $positif = $json_de_data[0]->provinsi[$i]->positif;
                $sembuh = $json_de_data[0]->provinsi[$i]->sembuh;
                $meninggal = $json_de_data[0]->provinsi[$i]->meninggal;
            }
        }
        
        include "../flex_data_provinsi.php";
        
        return $flex_template;
    }
}
