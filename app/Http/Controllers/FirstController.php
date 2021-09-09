<?php

namespace App\Http\Controllers;

use App\Http\Service\ACAdapter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FirstController extends Controller {
  protected $acAdapter;

  public function __construct() {
    $this->acAdapter = new ACAdapter();
  }

  public function login(Request $request) {
    $resp = $this->acAdapter->login($request->email, $request->passwd);

    if ($resp['success'] == 'true') {
      setCookie("ssoTokenId", $resp['data']['ssoTokenId']);
      return redirect('/watch');
    } else {
      return redirect('/login');
    }
  }

  public function watch() {
    if (!@$_COOKIE['ssoTokenId']) {
      // 沒有登入cookie記錄，重新登入
      return redirect('/login');
    } else {
      // 有登入cookie記錄，確認是否仍有效
      $resp = $this->acAdapter->checkLogin($_COOKIE['ssoTokenId']);

      if ($resp['success'] == 'true') {
        $pid = $resp['data']['Pid'];

        return $this->acAdapter->getACInfo($pid);
      } else {
        return redirect('/login');
      }
    }
  }
}
