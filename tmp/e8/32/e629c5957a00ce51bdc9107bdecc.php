<?php

/* admin/companies.html.twig */
class __TwigTemplate_e832e629c5957a00ce51bdc9107bdecc extends Twig_Template
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
        echo "Companies";
    }

    // line 5
    public function block_adminContent($context, array $blocks = array())
    {
        // line 6
        echo "<ul>
  ";
        // line 7
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["companies"]) ? $context["companies"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["comp"]) {
            // line 8
            echo "    <li><a href=\"";
            echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
            echo "admin/companies/";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["comp"]) ? $context["comp"] : null), "id"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["comp"]) ? $context["comp"] : null), "name"), "html", null, true);
            echo "</a> - ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["comp"]) ? $context["comp"] : null), "planName"), "html", null, true);
            echo "</li>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['comp'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 10
        echo "</ul>
";
    }

    public function getTemplateName()
    {
        return "admin/companies.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  58 => 10,  43 => 8,  39 => 7,  36 => 6,  33 => 5,  27 => 3,);
    }
}
