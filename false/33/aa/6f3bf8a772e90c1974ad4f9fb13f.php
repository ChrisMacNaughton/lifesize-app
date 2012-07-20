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
        // line 9
        echo "</head>
<body>
<div class=\"container-fluid\">
";
        // line 12
        if (isset($context["errors"])) { $_errors_ = $context["errors"]; } else { $_errors_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_errors_);
        foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
            // line 13
            echo "\t<div class=\"span8 offset2 error\">
\t\t";
            // line 14
            if (isset($context["error"])) { $_error_ = $context["error"]; } else { $_error_ = null; }
            echo twig_escape_filter($this->env, $_error_, "html", null, true);
            echo "
\t</div>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 17
        $this->displayBlock('body', $context, $blocks);
        // line 20
        echo "</div>
";
        // line 21
        $this->displayBlock('javascripts', $context, $blocks);
        // line 27
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

    // line 17
    public function block_body($context, array $blocks = array())
    {
        // line 18
        echo "
";
    }

    // line 21
    public function block_javascripts($context, array $blocks = array())
    {
        // line 22
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
        return array (  93 => 22,  90 => 21,  85 => 18,  82 => 17,  75 => 5,  72 => 4,  66 => 3,  61 => 27,  59 => 21,  56 => 20,  54 => 17,  44 => 14,  41 => 13,  36 => 12,  31 => 9,  29 => 4,  25 => 3,  21 => 1,);
    }
}
