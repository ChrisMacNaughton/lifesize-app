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
</body>
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

    public function getTemplateName()
    {
        return "base.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  61 => 12,  58 => 11,  52 => 5,  49 => 4,  43 => 3,  35 => 11,  30 => 8,  28 => 4,  24 => 3,  20 => 1,  37 => 14,  34 => 3,  27 => 2,);
    }
}
