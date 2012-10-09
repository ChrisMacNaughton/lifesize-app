<?php

$codecs = array(
		'aac-lc'=>'Advanced Audio Coding - Low Complexity',
        'g.722.1c.24'=>'Polycom(R) Siren14(TM) @ 24kbps',
        'g.722.1c.32'=>'Polycom(R) Siren14(TM) @ 32kbps',
        'g.722.1c.48'=>'Polycom(R) Siren14(TM) @ 48kbps',
        'silk.24'=>'Silk 24',
        'silk.16'=>'Silk 16',
        'silk.12'=>'Silk 12',
        'silk.8'=>'Silk 8',
        'g.722'=>'G.722',
        'g.728'=>'G.728',
        'g.729'=>'G.729',
        'g.711.u'=>'G.711 mu-law',
        'g.711.a'=>'G.711 a-law'
	);

	foreach($codecs as $key=>$codec){
		print("'" . sha1($key) . "'=>'$codec',\n");
	}