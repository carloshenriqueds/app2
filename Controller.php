<?php
require_once("vendor/autoload.php");
use App\GoogleService;
use App\Model\ReportModel;
session_start();
if(isset($_REQUEST['method'])){
    if($_REQUEST['method'] == "loginGoogle"){
        $client = new Google_Client();
        $client->setAuthConfig(__DIR__.'/cliente_secret.json');
        $client->setIncludeGrantedScopes(true);   // incremental auth
        $client->addScope(array("https://www.googleapis.com/auth/plus.login","https://www.googleapis.com/auth/plus.me","https://www.googleapis.com/auth/userinfo.email", "https://www.googleapis.com/auth/userinfo.profile", "https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.force-ssl"));
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            print_r($client->verifyIdToken());
        } else {
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/app2/src/oauth2callback.php';
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
    }
    if($_REQUEST['method'] == "loginok"){
        $client = new Google_Client();
        $client->setAuthConfig(__DIR__.'/cliente_secret.json');
        $client->setIncludeGrantedScopes(true);   // incremental auth
        $client->addScope(array("https://www.googleapis.com/auth/plus.login","https://www.googleapis.com/auth/plus.me","https://www.googleapis.com/auth/userinfo.email", "https://www.googleapis.com/auth/userinfo.profile", "https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.force-ssl"));
        $client->authenticate($_GET['code']);
        $token = $client->getAccessToken();
        $plus = new Google_Service_Plus($client);
        $me = $plus->people->get('me');
        $_SESSION['nameUser'] = $me->displayName;
        $_SESSION['emailUser'] = $me->emails[0]->value;
        $_SESSION['imgUrl'] = str_replace("?sz=50","",$me->image->url);
        $_SESSION['token'] = $token;
        header('Location: home.html');

    }

}else{
    $data=json_decode(file_get_contents("php://input"));

    if($data->method == "loginGooogle"){
        print json_encode($_SESSION); die();

    }

    if($data->method == "sendComment")
    {
        $googleService = new GoogleService();
        $token=$_SESSION['token']['access_token'];
        $chatId = $data->chatId;
        $message=$data->message;
        print json_encode($googleService->sendComment($token, $chatId, $message)); die();
    }
    
    if($data->method == "getLiveList")
    {
        $googleService = new GoogleService();
        print json_encode($googleService->getLiveList()); die();
    }
    
    if($data->method == "verifySession")
    {
        if(!isset($_SESSION['token'])){
            print "not found";
            die();
        }
        print json_encode($_SESSION); die();
    }
    
    
    if($data->method == "getLiveData")
    {
        $googleService = new GoogleService();
        $idVideo = $data->videoId;
        print json_encode($googleService->getLiveData($idVideo)); die();
    }
    
    if($data->method == "getChatList")
    {
        $googleService = new GoogleService();
        $chatId = $data->chatId;
        print json_encode($googleService->getLiveComments($chatId, $_SESSION['token']['access_token'])); die();
    }

    if($data->method == "getReportCounter")
    {
        $model = new ReportModel();
        print json_encode($model->selectCounters()); die();
    }
    
    if($data->method == "searchAuthor")
    {
        $model = new ReportModel();
        $author = $data->author;
        print json_encode($model->searchAuthor($author)); die();
    }
    
}



