<?php

/* admin/adminBase.html.twig */
class __TwigTemplate_123b8d54e9d432767f1694c4b1bf27cd extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("base.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'name' => array($this, 'block_name'),
            'subtitle' => array($this, 'block_subtitle'),
            'body' => array($this, 'block_body'),
            'adminContent' => array($this, 'block_adminContent'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_title($context, array $blocks = array())
    {
        echo "Admin";
    }

    // line 3
    public function block_name($context, array $blocks = array())
    {
        echo "Admin<small>";
        $this->displayBlock('subtitle', $context, $blocks);
        echo "</small>";
    }

    public function block_subtitle($context, array $blocks = array())
    {
    }

    // line 5
    public function block_body($context, array $blocks = array())
    {
        // line 6
        echo "<div class=\"container\">
  <div class=\"row\">
    <div class=\"span3\">
      <ul class=\"nav nav-tabs nav-stacked\">
        <!--<li class=\"header\"><h3>Overview</h3></li>-->
        <li class=\"";
        // line 11
        if (((isset($context["adminPage"]) ? $context["adminPage"] : null) == "overview")) {
            echo "active";
        }
        echo "\"><a href=\"";
        echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
        echo "admin\">Overview</a></li>
        <li class=\"header\"><h3>Companies</h3></li>
        <li class=\"";
        // line 13
        if (((isset($context["adminPage"]) ? $context["adminPage"] : null) == "company")) {
            echo "active";
        }
        echo "\"><a href=\"";
        echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
        echo "admin/companies\">List</a></li>
      </ul>
    </div>
    <div class=\"span9\">
    ";
        // line 17
        $this->displayBlock('adminContent', $context, $blocks);
        // line 18
        echo "    </div>
  </div>
</div>
";
    }

    // line 17
    public function block_adminContent($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "admin/adminBase.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  87 => 17,  80 => 18,  78 => 17,  67 => 13,  58 => 11,  51 => 6,  48 => 5,  30 => 2,  36 => 3,  33 => 5,  27 => 3,);
    }
}
