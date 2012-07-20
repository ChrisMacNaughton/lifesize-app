<?php

/* base.html.twig */
class __TwigTemplate_33aa6f3bf8a772e90c1974ad4f9fb13f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
            'afternav' => array($this, 'block_afternav'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<html>
<head>
\t<title>";
        // line 3
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
\t";
        // line 4
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 9
        echo "</head>
<body>
<div class=\"container-fluid\">
";
        // line 12
        if (isset($context["user"])) { $_user_ = $context["user"]; } else { $_user_ = null; }
        if (($this->getAttribute($_user_, "id") != 0)) {
            // line 13
            echo "<div class=\"row-fluid\">
<div class=\"span10\">
\t<ul class=\"nav nav-pills\">
\t\t<li";
            // line 16
            if (isset($context["page"])) { $_page_ = $context["page"]; } else { $_page_ = null; }
            if ((($_page_ == "home") || ($_page_ == ""))) {
                echo " class=\"active\"";
            }
            echo "><a href=\"/home\">Home</a></li>
\t\t<li";
            // line 17
            if (isset($context["page"])) { $_page_ = $context["page"]; } else { $_page_ = null; }
            if (($_page_ == "devices")) {
                echo " class=\"active\"";
            }
            echo "><a href=\"/devices\">Devices</a></li>
\t\t<li";
            // line 18
            if (isset($context["page"])) { $_page_ = $context["page"]; } else { $_page_ = null; }
            if (($_page_ == "users")) {
                echo " class=\"active\"";
            }
            echo "><a href=\"/users\">Users</a></li>
\t\t<li><a href=\"logout\">Logout</a></li>
\t</ul>
</div>
\t<div class=\"span2\" style=\"padding-top: 5px;\">
\t";
            // line 23
            $this->displayBlock('afternav', $context, $blocks);
            // line 25
            echo "\t</div>
</div>
";
        }
        // line 28
        echo "<div class=\"row\">
";
        // line 29
        if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_errors_);
        foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
            // line 30
            echo "\t<div class=\" span8 offset2 error\">
\t\t";
            // line 31
            if (isset($context["error"])) { $_error_ = $context["error"]; } else { $_error_ = null; }
            echo twig_escape_filter($this->env, $_error_, "html", null, true);
            echo "
\t</div>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 34
        echo "</div>
";
        // line 35
        $this->displayBlock('body', $context, $blocks);
        // line 38
        echo "</div>
";
        // line 39
        $this->displayBlock('javascripts', $context, $blocks);
        // line 45
        echo "</body>
</html>";
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo " - Videoconferencing Control";
    }

    // line 4
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 5
        echo "\t<link href=\"/assets/css/bootstrap.min.css\" rel=\"stylesheet\">
    <link href=\"/assets/css/bootstrap-responsive.min.css\" rel=\"stylesheet\">
\t<link href=\"/assets/css/main.css\" rel=\"stylesheet\">
\t";
    }

    // line 23
    public function block_afternav($context, array $blocks = array())
    {
        // line 24
        echo "\t";
    }

    // line 35
    public function block_body($context, array $blocks = array())
    {
        // line 36
        echo "
";
    }

    // line 39
    public function block_javascripts($context, array $blocks = array())
    {
        // line 40
        echo "    <script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js\"></script>
\t<script src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js\"></script>
    <script src=\"/assets/js/bootstrap.min.js\"></script>
    <script src=\"/assets/js/main.js\"></script>
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
        return array (  147 => 40,  144 => 39,  139 => 36,  136 => 35,  132 => 24,  129 => 23,  122 => 5,  119 => 4,  113 => 3,  108 => 45,  106 => 39,  103 => 38,  101 => 35,  98 => 34,  88 => 31,  85 => 30,  80 => 29,  77 => 28,  72 => 25,  70 => 23,  59 => 18,  52 => 17,  45 => 16,  40 => 13,  37 => 12,  32 => 9,  30 => 4,  26 => 3,  22 => 1,);
    }
}
