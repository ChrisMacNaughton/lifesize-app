<?php

class apiController extends Controller {
  public function new_deviceAction(){
    $api_key = $_GET['api_key'];

    $cipher = MCRYPT_RIJNDAEL_128;
    $mode=MCRYPT_MODE_CBC;
    $key = 'dfhdrt5dpsei76yngb69wybnoihngnvstg0e67gerfo87s34gorlgbz78y4uh6';
    $secret=hash('sha256',$key, true);
    $data = json_encode(array(
      'id'=>'dev-0w489h7tp',
      'ip'=>'4.26.212.49',
      'password'=>'sunbeltLs123',
    ));
    /*
    $data = json_encode(array(
      'Title'=>"Device",
      'data'=>"something random"
    ));*/
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $crypttext = mcrypt_encrypt($cipher, $secret, $iv.'~~~~~~~~'.$data.'~~~~~~~~', $mode, $iv);
    echo base64_encode($crypttext);
  }
  public function update_DeviceAction(){
    error_reporting(E_ALL);
    $api_key = $_GET['api_key'];
    $data = json_decode($_POST['data'], true);

    //print_r($data);

    $device_id = $data['id'];
    $data = base64_decode($data['device']);
    $cipher = MCRYPT_RIJNDAEL_128;
    $mode=MCRYPT_MODE_CBC;
    $stmt = $this->db->prepare("SELECT secret_key FROM api_keys WHERE api_key = :key");
    $stmt->execute(array(':key'=>$_GET['api_key']));
    $res=$stmt->fetch(PDO::FETCH_ASSOC);
    $key = $res['secret_key'];
    unset($res);
    $secret=hash('sha256',$key, true);//hash('sha256','dfhdrt5dpsei76yngb69wybnoihngnvstg0e67g'.$_GET['api_key'], true);

    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $data = mcrypt_decrypt($cipher, $secret, $data, $mode, $iv);
    //print("\nDevice:\n\n");
    $start = strpos($data, "{");
    $length = strpos($data, "}") - $start;

    $device = json_decode(substr($data, $start, $length+1), true);
    $device['device_id'] = $device_id;
    //print_r($device);
    if(count($device) > 1){
      header("HTTP/1.0 200 OK");
      print("OK");
    } else {
      header("HTTP/1.0 500 Not Found");
      print("There was a problem");
    }
  }
}