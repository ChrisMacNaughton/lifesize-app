<?php

function apiAutoload($classname)
{
    if (preg_match('/[a-zA-Z]+Controller$/', $classname)) {
        include __DIR__ . '/controllers/' . $classname . '.php';
        return true;
    } elseif (preg_match('/[a-zA-Z]+Model$/', $classname)) {
        include __DIR__ . '/models/' . $classname . '.php';
        return true;
    } elseif (preg_match('/[a-zA-Z]+View$/', $classname)) {
        include __DIR__ . '/views/' . $classname . '.php';
        return true;
    } elseif (preg_match('/[a-zA-Z]$/', $classname)) {
        include __DIR__ . '/classes/' . $classname . '.php';
        return true;
    }
    include __DIR__ . '/classes/' . $classname . '.php';
}

function handle_exception($e) {
    global $request, $view;

    //header("Status: " . $e->getCode(), false, $e->getCode());
    if(isset($request['request']) && $request['request'] == 'status'){
        $view->render(array('code'=>$e->getCode(), 'result'=>array('Status'=>'Online')));
    } else {
        $view->render(array('code'=>$e->getCode(), 'result'=>array('Error'=>$e->getMessage()/*, 'request'=>$request*/)));
        
    }
	//$view->render();
}
