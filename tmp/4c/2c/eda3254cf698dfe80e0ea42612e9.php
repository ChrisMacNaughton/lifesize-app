<?php

/* base.html.twig */
class __TwigTemplate_4c2ceda3254cf698dfe80e0ea42612e9 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
            'afterbrand' => array($this, 'block_afterbrand'),
            'name' => array($this, 'block_name'),
            'offset' => array($this, 'block_offset'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
<title>
\t";
        // line 5
        $this->displayBlock('title', $context, $blocks);
        echo " - ControlVC
</title>
";
        // line 7
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 12
        echo "<style>
";
        // line 13
        if (((isset($context["headercolor"]) ? $context["headercolor"] : null) == null)) {
            // line 14
            echo "\t";
            $context["headercolor"] = "99ccff";
        }
        // line 16
        echo "html {
\t-webkit-print-color-adjust: exact;
}
\t.jumbotron{

\t\tbackground: -moz-linear-gradient(45deg,  #2B1B17 0%, #";
        // line 21
        echo twig_escape_filter($this->env, (isset($context["headercolor"]) ? $context["headercolor"] : null), "html", null, true);
        echo " 100%); /* FF3.6+ */
\t\tbackground: -webkit-gradient(linear, left bottom, right top, color-stop(0%,#2B1B17), color-stop(100%,#";
        // line 22
        echo twig_escape_filter($this->env, (isset($context["headercolor"]) ? $context["headercolor"] : null), "html", null, true);
        echo ")); /* Chrome,Safari4+ */
\t\tbackground: -webkit-linear-gradient(45deg,  #2B1B17 0%,#";
        // line 23
        echo twig_escape_filter($this->env, (isset($context["headercolor"]) ? $context["headercolor"] : null), "html", null, true);
        echo " 100%); /* Chrome10+,Safari5.1+ */
\t\tbackground: -o-linear-gradient(45deg,  #2B1B17 0%,#";
        // line 24
        echo twig_escape_filter($this->env, (isset($context["headercolor"]) ? $context["headercolor"] : null), "html", null, true);
        echo " 100%); /* Opera 11.10+ */
\t\tbackground: -ms-linear-gradient(45deg,  #2B1B17 0%,#";
        // line 25
        echo twig_escape_filter($this->env, (isset($context["headercolor"]) ? $context["headercolor"] : null), "html", null, true);
        echo " 100%); /* IE10+ */
\t\tbackground: linear-gradient(45deg,  #2B1B17 0%,#";
        // line 26
        echo twig_escape_filter($this->env, (isset($context["headercolor"]) ? $context["headercolor"] : null), "html", null, true);
        echo " 100%); /* W3C */
\t\tfilter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#2B1B17', endColorstr='#";
        // line 27
        echo twig_escape_filter($this->env, (isset($context["headercolor"]) ? $context["headercolor"] : null), "html", null, true);
        echo "',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
\t\t-webkit-box-shadow: inset 0 3px 7px rgba(0,0,0,.2), inset 0 -3px 7px rgba(0,0,0,.2);
\t\t-moz-box-shadow: inset 0 3px 7px rgba(0,0,0,.2), inset 0 -3px 7px rgba(0,0,0,.2);
\t\tbox-shadow: inset 0 3px 7px rgba(0,0,0,.2), inset 0 -3px 7px rgba(0,0,0,.2);
\t}
\t</style>
\t";
        // line 33
        if (($this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "user"), "level") == 0)) {
            // line 34
            echo "<style>
\t.adminBar{
\t\tmin-height: 30px;
\t\tbackground: #ccc;
\t\tmargin-left: 5px;
\t\tmargin-right: 5px;
\t\tpadding-top: 10px;
\t\tpadding-bottom: 10px;
\t\tpadding-left: 5px;
\t\tborder: 1px solid #000;
\t}
</style>
";
        }
        // line 47
        if (((((isset($context["active"]) ? $context["active"] : null) != "login") && ((isset($context["active"]) ? $context["active"] : null) != "register")) && ((isset($context["active"]) ? $context["active"] : null) != "admin"))) {
            // line 48
            echo "<!-- start Mixpanel -->
<script type=\"text/javascript\">
(function(c,a){window.mixpanel=a;var b,d,h,e;b=c.createElement(\"script\");b.type=\"text/javascript\";b.async=!0;b.src=(\"https:\"===c.location.protocol?\"https:\":\"http:\")+'//cdn.mxpnl.com/libs/mixpanel-2.1.min.js';d=c.getElementsByTagName(\"script\")[0];d.parentNode.insertBefore(b,d);a._i=[];a.init=function(b,c,f){function d(a,b){var c=b.split(\".\");2==c.length&&(a=a[c[0]],b=c[1]);a[b]=function(){a.push([b].concat(Array.prototype.slice.call(arguments,0)))}}var g=a;\"undefined\"!==typeof f? g=a[f]=[]:f=\"mixpanel\";g.people=g.people||[];h=\"disable track track_pageview track_links track_forms register register_once unregister identify name_tag set_config people.identify people.set people.increment\".split(\" \");for(e=0;e<h.length;e++)d(g,h[e]);a._i.push([b,c,f])};a.__SV=1.1})(document,window.mixpanel||[]);
mixpanel.init(\"eded07b033c690bd0e255bdc354007fb\");

mixpanel.people.identify(\"";
            // line 53
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "id"), "html", null, true);
            echo "\");
mixpanel.people.set({
    \"\$email\": \"";
            // line 55
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "email"), "html", null, true);
            echo "\",
    \"\$created\":\"";
            // line 56
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "created"), "Y-m-d H:i:s", "GMT"), "html", null, true);
            echo "\",
    \"\$last_login\":\"";
            // line 57
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "last_login"), "Y-m-d H:i:s", "GMT"), "html", null, true);
            echo "\",
    \"\$name\":\"";
            // line 58
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "name"), "html", null, true);
            echo "\",
    \"company\": \"";
            // line 59
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "owned"), "html", null, true);
            echo "\",
    \"plan\":\"";
            // line 60
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "plan"), "html", null, true);
            echo "\",
    \"registered\":";
            // line 61
            if (($this->getAttribute((isset($context["user"]) ? $context["user"] : null), "registered") == 1)) {
                echo "true";
            } else {
                echo "false";
            }
            echo ",
    \"\$last_seen\":new Date()
});
mixpanel.name_tag('";
            // line 64
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "email"), "html", null, true);
            echo "');
mixpanel.track('Visited: ";
            // line 65
            echo twig_escape_filter($this->env, (isset($context["active"]) ? $context["active"] : null), "html", null, true);
            echo "');
</script>
<!-- end Mixpanel -->
";
        }
        // line 69
        echo "</head>
<body>";
        // line 70
        if (($this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "user"), "level") == 0)) {
            // line 71
            echo "\t<div id=\"headerBar\"></div>
\t";
        }
        // line 73
        echo "<div class=\"navbar navbar-inverse navbar-fixed-top noprint\">
\t<div class=\"navbar-inner\">
\t\t";
        // line 75
        if (((isset($context["perms"]) ? $context["perms"] : null) & (($this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "permissions"), "admin/index", array(), "array") && ((isset($context["active"]) ? $context["active"] : null) != "login")) && ((isset($context["active"]) ? $context["active"] : null) != "register")))) {
            // line 76
            echo "\t\t\t<a href=\"";
            echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
            echo "admin\" class=\"pull-right btn btn-info\">Admin</a>
\t\t";
        }
        // line 78
        echo "\t\t<div class=\"container\">
\t\t\t";
        // line 79
        $this->displayBlock('afterbrand', $context, $blocks);
        // line 80
        echo "\t\t\t<a class=\"brand\" href=\"http://control.vc\">ControlVC</a>
\t\t\t";
        // line 81
        if ((((isset($context["active"]) ? $context["active"] : null) != "login") && ((isset($context["active"]) ? $context["active"] : null) != "register"))) {
            // line 82
            echo "\t\t\t<div class=\"nav-collapse collapse\">
\t\t\t\t<ul class=\"nav\">
\t\t\t\t\t<li class=\"";
            // line 84
            if (((isset($context["active"]) ? $context["active"] : null) == "dashboard")) {
                echo "active";
            }
            echo "\">
\t\t\t\t\t\t<a href=\"";
            // line 85
            echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
            echo "\">Dashboard</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li class=\"";
            // line 87
            if (((isset($context["active"]) ? $context["active"] : null) == "devices")) {
                echo "active";
            }
            echo "\">
\t\t\t\t\t\t<a href=\"";
            // line 88
            echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
            echo "devices\">Devices</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li class=\"";
            // line 90
            if (((isset($context["active"]) ? $context["active"] : null) == "alarms")) {
                echo "active";
            }
            echo "\">
\t\t\t\t\t\t<a href=\"";
            // line 91
            echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
            echo "alarms\">Alarms</a>
\t\t\t\t\t</li>
\t\t\t\t\t<li class=\"";
            // line 93
            if (((isset($context["active"]) ? $context["active"] : null) == "me")) {
                echo "active";
            }
            echo "\">
\t\t\t\t\t\t<a href=\"";
            // line 94
            echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
            echo "me\">Profile</a>
\t\t\t\t\t</li>
\t\t\t\t\t";
            // line 96
            if (((isset($context["perms"]) ? $context["perms"] : null) & $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "permissions"), "company/index", array(), "array"))) {
                // line 97
                echo "\t\t\t\t\t<li class=\"";
                if (((isset($context["active"]) ? $context["active"] : null) == "company")) {
                    echo "active";
                }
                echo "\">
\t\t\t\t\t\t<a href=\"";
                // line 98
                echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
                echo "company\">Company</a>
\t\t\t\t\t</li>
\t\t\t\t\t";
                // line 100
                if (((isset($context["perms"]) ? $context["perms"] : null) & $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "permissions"), "users/index", array(), "array"))) {
                    // line 101
                    echo "\t\t\t\t\t<li class=\"";
                    if (((isset($context["active"]) ? $context["active"] : null) == "users")) {
                        echo "active";
                    }
                    echo "\">
\t\t\t\t\t\t<a href=\"";
                    // line 102
                    echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
                    echo "users\">Users</a>
\t\t\t\t\t</li>
\t\t\t\t\t";
                }
                // line 105
                echo "\t\t\t\t\t";
            }
            // line 106
            echo "
\t\t\t\t\t<li>
\t\t\t\t\t\t<a href=\"";
            // line 108
            echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
            echo "logout\">Logout</a>
\t\t\t\t\t</li>
\t\t\t\t</ul>
\t\t\t</div>
\t\t\t";
        }
        // line 113
        echo "\t\t</div>
\t</div>
</div>

<div class=\"jumbotron subhead\" id=\"overview\">
\t<div class=\"container\">
\t\t<h1>
\t\t\t";
        // line 120
        $this->displayBlock('name', $context, $blocks);
        // line 122
        echo "\t\t</h1>
\t\t<div class=\"span2 offset1\" style=\"float: right\">
\t\t\t";
        // line 124
        $this->displayBlock('offset', $context, $blocks);
        // line 125
        echo "\t\t</div>
\t</div>
</div>
<div class=\"container\">
\t";
        // line 129
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["errors"]) ? $context["errors"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["err"]) {
            // line 130
            echo "\t\t<div class=\"span10 offset1\">
\t\t\t<div class=\"error\">
\t\t\t\t<p>";
            // line 132
            echo twig_escape_filter($this->env, (isset($context["err"]) ? $context["err"] : null), "html", null, true);
            echo "</p>
\t\t\t</div>
\t\t</div>
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['err'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 136
        echo "\t";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["flash"]) ? $context["flash"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["err"]) {
            // line 137
            echo "\t\t<div class=\"span10 offset1\">
\t\t\t<div class=\"flash\">
\t\t\t\t<p>";
            // line 139
            echo twig_escape_filter($this->env, (isset($context["err"]) ? $context["err"] : null), "html", null, true);
            echo "</p>
\t\t\t</div>
\t\t</div>
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['err'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 143
        echo "</div>
<div class=\"container\" id=\"body\">
";
        // line 145
        $this->displayBlock('body', $context, $blocks);
        // line 148
        echo "
</div>
";
        // line 150
        $this->displayBlock('javascripts', $context, $blocks);
        // line 155
        if (((isset($context["perms"]) ? $context["perms"] : null) & $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "permissions"), "admin/index", array(), "array"))) {
            // line 156
            echo " <script src=\"https://dlewcy1lx1kqi.cloudfront.net/js/jquery.hotkey.js\"></script>
<script type=\"text/javascript\">
var adminVisible = 0;
\$(document).bind('keydown', function(e){
    var code = (e.keyCode ? e.keyCode : e.which);
    //console.log(code);
    if (code == 115) {
\t\tif (adminVisible == 1) {
\t\t\t//console.log('remove');
\t\t\t\$('#headerBar').html('');
\t\t\t//\$(\".adminBar\").css('display','none');
\t\t\tadminVisible = 0;
\t\t} else {
\t\t\t\$('#headerBar').html('<div class=\"adminBar navbar-fixed-bottom\"><span style=\"font-weight: bold;font-size: 1.1em;\">DB Time:</span> ";
            // line 169
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["db_data"]) ? $context["db_data"] : null), "meta"), "time"), "html", null, true);
            echo " | <span style=\"font-weight: bold;font-size: 1.1em;\">DB Queries:</span> ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["db_data"]) ? $context["db_data"] : null), "meta"), "count"), "html", null, true);
            echo " | <span style=\"font-weight: bold;font-size: 1.1em;\">Load Time:</span> ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["app"]) ? $context["app"] : null), "run_time"), "html", null, true);
            echo " secs</div>');
\t\t\t//console.log(\"add\");
\t\t\t//\$(\".adminBar\").css('display','block');
\t\t\tadminVisible=1;
\t\t}
\t}
});
</script>
";
        }
        // line 178
        if (((((isset($context["active"]) ? $context["active"] : null) != "login") && ((isset($context["active"]) ? $context["active"] : null) != "register")) && ((isset($context["active"]) ? $context["active"] : null) != "admin"))) {
            // line 179
            echo "<script type=\"text/javascript\">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-35809328-1']);
  _gaq.push(['_setDomainName', 'control.vc']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<script type=\"text/javascript\" src=\"//assets.zendesk.com/external/zenbox/v2.5/zenbox.js\"></script>
<style type=\"text/css\" media=\"screen, projection\">
  @import url(//assets.zendesk.com/external/zenbox/v2.5/zenbox.css);
</style>
<script type=\"text/javascript\">
  if (typeof(Zenbox) !== \"undefined\") {
    Zenbox.init({
      dropboxID:   \"20099322\",
      url:         \"https://controlvc.zendesk.com\",
      tabID:       \"Feedback\",
      tabColor:    \"grey\",
      tabPosition: \"Right\",
      requester_name:\"";
            // line 206
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "name"), "html", null, true);
            echo "\",
      requester_email:\"";
            // line 207
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "email"), "html", null, true);
            echo "\"
    });
  }
</script>
";
        }
        // line 217
        echo "</body>
</html>";
    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
    }

    // line 7
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 8
        echo "\t<link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
        echo "assets/css/jquery-ui.css\" />
\t<link href=\"";
        // line 9
        echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
        echo "assets/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\">
\t<link href=\"";
        // line 10
        echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
        echo "assets/css/main.css\" rel=\"stylesheet\">
";
    }

    // line 79
    public function block_afterbrand($context, array $blocks = array())
    {
    }

    // line 120
    public function block_name($context, array $blocks = array())
    {
        echo "Dashboard
";
    }

    // line 124
    public function block_offset($context, array $blocks = array())
    {
    }

    // line 145
    public function block_body($context, array $blocks = array())
    {
        // line 146
        echo "Hello World!<br/>You haven't overridden the body block!
";
    }

    // line 150
    public function block_javascripts($context, array $blocks = array())
    {
        // line 151
        echo "\t<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js\"></script>
\t<script src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js\"></script>
\t<script src=\"";
        // line 153
        echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
        echo "assets/bootstrap/js/bootstrap.min.js\"></script>
";
    }

    public function getTemplateName()
    {
        return "base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  493 => 153,  489 => 151,  486 => 150,  481 => 146,  478 => 145,  473 => 124,  466 => 120,  461 => 79,  455 => 10,  451 => 9,  446 => 8,  443 => 7,  438 => 5,  433 => 217,  425 => 207,  421 => 206,  392 => 179,  390 => 178,  374 => 169,  359 => 156,  357 => 155,  355 => 150,  351 => 148,  349 => 145,  345 => 143,  335 => 139,  331 => 137,  326 => 136,  316 => 132,  312 => 130,  308 => 129,  302 => 125,  300 => 124,  296 => 122,  294 => 120,  285 => 113,  277 => 108,  273 => 106,  270 => 105,  264 => 102,  257 => 101,  255 => 100,  250 => 98,  243 => 97,  241 => 96,  236 => 94,  230 => 93,  225 => 91,  219 => 90,  214 => 88,  208 => 87,  203 => 85,  197 => 84,  193 => 82,  188 => 80,  186 => 79,  177 => 76,  175 => 75,  171 => 73,  167 => 71,  165 => 70,  162 => 69,  151 => 64,  141 => 61,  137 => 60,  129 => 58,  125 => 57,  121 => 56,  112 => 53,  105 => 48,  103 => 47,  88 => 34,  86 => 33,  77 => 27,  73 => 26,  69 => 25,  65 => 24,  61 => 23,  57 => 22,  53 => 21,  46 => 16,  42 => 14,  40 => 13,  37 => 12,  30 => 5,  24 => 1,  227 => 85,  218 => 81,  211 => 78,  207 => 76,  201 => 74,  199 => 73,  195 => 71,  191 => 81,  185 => 67,  183 => 78,  179 => 64,  176 => 63,  169 => 62,  155 => 65,  153 => 59,  147 => 56,  143 => 54,  139 => 52,  133 => 59,  131 => 49,  127 => 47,  123 => 45,  117 => 55,  115 => 42,  101 => 39,  97 => 37,  92 => 36,  67 => 13,  64 => 12,  58 => 11,  52 => 10,  44 => 7,  41 => 6,  35 => 7,  32 => 3,  29 => 2,);
    }
}
