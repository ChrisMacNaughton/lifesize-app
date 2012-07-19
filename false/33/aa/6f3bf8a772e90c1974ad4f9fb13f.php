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
        // line 8
        echo "</head>
<body>
<div class=\"container-fluid\">
";
        // line 11
        $this->displayBlock('body', $context, $blocks);
        // line 14
        echo "</div>
";
        // line 15
        $this->displayBlock('javascripts', $context, $blocks);
        // line 21
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
\t";
    }

    // line 11
    public function block_body($context, array $blocks = array())
    {
        // line 12
        echo "
";
    }

    // line 15
    public function block_javascripts($context, array $blocks = array())
    {
        // line 16
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

    public function getDebugInfo()
    {
        return array (  74 => 16,  71 => 15,  66 => 12,  63 => 11,  57 => 5,  54 => 4,  48 => 3,  43 => 21,  41 => 15,  38 => 14,  36 => 11,  31 => 8,  29 => 4,  25 => 3,  21 => 1,);
    }
}
