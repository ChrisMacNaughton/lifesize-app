<?php
class MetricsTracker {
    public $token;
    public $host = 'http://api.mixpanel.com/';
    public function __construct($token_string) {
        $this->token = $token_string;
    }
    function track($event, $properties=array()) {
        $params = array(
            'event' => $event,
            'properties' => $properties
            );

        if (!isset($params['properties']['token'])){
            $params['properties']['token'] = $this->token;
        }
        $url = $this->host . 'track/?data=' . base64_encode(json_encode($params));
        //you still need to run as a background process
        exec("curl '" . $url . "' >/dev/null 2>&1 &"); 
    }
}
?>