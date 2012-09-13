<?php

class xmlView extends defaultView {
	public function render($data) {
		header("Status" . $data['code'], false, $data['code']);
		header("Content-Type: Application/xml");
		echo $this->xml_encode(array('results'=>$data));
	}
	private $plurals = array(
	    'status'
	);
	protected function xml_encode($mixed,$domElement=null,$DOMDocument=null){
	    $plurals = $this->plurals;
	    if(is_null($DOMDocument)){
	        $DOMDocument=new DOMDocument;
	        $DOMDocument->formatOutput=true;
	        $this->xml_encode($mixed,$DOMDocument,$DOMDocument);
	        echo $DOMDocument->saveXML();
	    }
	    else{
	        if(is_array($mixed)){
	            foreach($mixed as $index=>$mixedElement){
	                if(is_int($index)){
	                    if($index==0){
	                        $node=$domElement;
	                    }
	                    else{
	                        $node=$DOMDocument->createElement($domElement->tagName);
	                        $domElement->parentNode->appendChild($node);
	                    }
	                }
	                else{
	                    $plural=$DOMDocument->createElement($index);
	                    $domElement->appendChild($plural);
	                    $node=$plural;
	                    if(rtrim($index,'s')!==$index && array_search($index, $plurals)){
	                        $singular=$DOMDocument->createElement(rtrim($index,'s'));
	                        $plural->appendChild($singular);
	                        $node=$singular;
	                    }
	                }
	                $this->xml_encode($mixedElement,$node,$DOMDocument);
	            }
	        }
	        else{
	            $domElement->appendChild($DOMDocument->createTextNode($mixed));
	        }
	    }
	}
}