<?php

/* admin/index.html.twig */
class __TwigTemplate_1087fa7e944c4911acbd32f1cb98f754 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("admin/adminBase.html.twig");

        $this->blocks = array(
            'subtitle' => array($this, 'block_subtitle'),
            'adminContent' => array($this, 'block_adminContent'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "admin/adminBase.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_subtitle($context, array $blocks = array())
    {
        echo "Overview";
    }

    // line 5
    public function block_adminContent($context, array $blocks = array())
    {
        // line 6
        echo "Something needs to go here!
";
    }

    public function getTemplateName()
    {
        return "admin/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  36 => 6,  33 => 5,  27 => 3,);
    }
}
