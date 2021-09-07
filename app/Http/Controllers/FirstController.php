<?php

namespace App\Http\Controllers;

use App\Http\Service\ACAdapter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class FirstController extends Controller
{
    protected $acAdapter;

    public function __construct(ACAdapter $acAdapter) {
        $this->acAdapter = $acAdapter;
    }

    /**
     * First web
     */
    public function index()
    {
        return 'hello Laravel!';
    }

    public function api(Request $request) {
        $client = new Client();
        $who = 27298;
        $dataId = 'Data-33180';
        $url = "http://127.0.0.1:8080/getSaved?who={$who}&dataId={$dataId}";
        $result = $client->get($url);
        return $result->getBody()->getContents();
    }

    public function login() {
      if (!@$_COOKIE['ssoTokenId']) {
        // 沒有登入cookie記錄，重新登入
        $resp = $this->acAdapter->login("benjamin.chang@104.com.tw", "123qwe");

        if ($resp['success'] == 'true') {
          // 成功登入
          setCookie("ssoTokenId", $resp['data']['ssoTokenId']);
          echo '<script>alert("登入成功！")</script>';
        } else {
          echo '<script>alert("登入失敗！")</script>';
        }

        echo json_encode($resp);
      } else {
        // 有登入cookie記錄，確認是否仍有效
        echo '<script>alert("已有cookie存在！")</script>';
        $this->acAdapter->checkLogin($_COOKIE['ssoTokenId']);

        echo $_COOKIE['ssoTokenId'];
      }
    }
}
