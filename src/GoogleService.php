<?php

namespace App;
use App\Model\SettingsModel;
use App\Model\CommentModel;

class GoogleService{

    /**
     * @var string
     */
    const KEY = 'YOUR_GOOGLE_KEY';

    /**
     * GoogleService constructor.
     */
    public function __construct(){}

    /**
     * Get data from live
     * @return array $videoArray
     */
    public function getLiveList()
    {
        $urlGet = "https://www.googleapis.com/youtube/v3/search?part=snippet&eventType=live&type=video&videoCategoryId=20&videoEmbeddable=true&regionCode=US&maxResults=12&key=".Self::KEY;
        $searchResponse = $this->curlGet($urlGet);
        $videoArray = [];
        $cont = 0;
        foreach ($searchResponse->items as $searchResult) {
            $videoArray[$cont]['videoId'] = $searchResult->id->videoId;
            $videoArray[$cont]['channelId'] = $searchResult->snippet->channelId;
            $videoArray[$cont]['title'] = $this->resumeString(preg_replace('/[^\p{L}\p{N}\s]/u', '', $searchResult->snippet->title));
            $videoArray[$cont]['description'] = $searchResult->snippet->description;
            $videoArray[$cont]['thumbnails'] = $searchResult->snippet->thumbnails;
            $videoArray[$cont]['imgurl'] = $searchResult->snippet->thumbnails->high->url;
            $cont++;
        }
        return $videoArray;
    }

    
    /**
     * Get data from live
     * @param string $videoId
     * @return array $arrayChat
     */
    public function getLiveData($videoId)
    {
        $urlGet = "https://www.googleapis.com/youtube/v3/videos/?part=liveStreamingDetails&id=$videoId&key=".Self::KEY;
        $searchResponse = $this->curlGet($urlGet);
        $chatId = $searchResponse->items[0]->liveStreamingDetails->activeLiveChatId;
        $arrayChat['chatId'] = $chatId;
        $arrayChat['urlLive'] = "https://youtube.com/embed/$videoId?autoplay=1&mute=1";
        return $arrayChat;
    }

    /**
     * Service constructor.
     * @param string $liveChatId
     * @return array $array
     */   
    public function getLiveComments($liveChatId, $userSessionId)
    {
        $commentModel = new CommentModel();
        $settings = new SettingsModel();
        $dataLastComment = $settings->getLastCommentDate($liveChatId)['lastDateComment'];
        $data = "liveStreamingDetails";
        $urlGet = "https://www.googleapis.com/youtube/v3/liveChat/messages/?part=snippet,authorDetails&liveChatId=$liveChatId&maxResult=10&key=".Self::KEY;
        $searchResponse = $this->curlGet($urlGet);
        $chatArray = [];
        $cont = 0;
        foreach ($searchResponse->items as $searchResult) {
            $chatArray[$cont]['id'] = $searchResult->id;
            $chatArray[$cont]['data'] = $searchResult->snippet->publishedAt;
            $chatArray[$cont]['message'] = $searchResult->snippet->textMessageDetails->messageText;
            $chatArray[$cont]['authorName'] = $searchResult->authorDetails->displayName;
            $chatArray[$cont]['profileImg'] = $searchResult->authorDetails->profileImageUrl;
            if(!$this->compareData($dataLastComment, $searchResult->snippet->publishedAt)){
                $settings->updateSettings($liveChatId, $chatArray[$cont]['data']);
                $commentModel->updateComment($chatArray[$cont]['message'],$chatArray[$cont]['authorName'], $chatArray[$cont]['profileImg'],$chatArray[$cont]['id'],$chatArray[$cont]['data'],$userSessionId);
            }
            $cont++;
        }

        return $chatArray;
    }

    /**
     * Send comment to live.
     * @param string $token
     * @param string $chatId
     * @param string $message
     * @return array $array
     */
    public function sendComment($token, $chatId, $message)
    {
        $url="https://www.googleapis.com/youtube/v3/liveChat/messages/?part=snippet";
        $json = [
            "snippet" => [
                "liveChatId" => $chatId,
                "type" => "textMessageEvent",
                "textMessageDetails" => [
                    "messageText" => $message
                    ]
                    ]
            ];
                
        return $this->curlPost($url, $token, $json);

    }

    /**
     * Get user profile
     * @param string $token
     * @return array $array
     */
    public function getProfile($token){
        $url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=$token";
        return $this->curlGet($url);
    }

    /**
     * Compare if data 1 < data 2
     * @param string $dataDb
     * @param string $dataComment
     * @return bool $boolean
     */
    private function compareData($dataDb, $dataComment){
        if(strtotime($dataDb) < strtotime($dataComment)){
            return false;
        }
        return true;
    }

    /**
     * Resume from string
     * @param string $var
     * @return string $var
     */
    private function resumeString($var){
        if (strlen($var) > 45)	{
            $var = substr($var, 0, 45);
            $var = trim($var) . "...";	
        }
        return $var;
    }
        
    /**
     * CurlGet
     */
    private function curlGet($url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url,
        ));
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        return $response;
    }

    /**
     * CurlPost
     */
    private function curlPost($url, $token, $json){
        $authorization = "Authorization: Bearer $token";
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($json) );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}