<?php

/* user/login.html.twig */
class __TwigTemplate_21d422dce7926cfd7a2cbf565cbd800e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("base.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
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
        echo "Login";
        $this->displayParentBlock("title", $context, $blocks);
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        echo "<div class=\"span4 offset4\">
<form action=\"#\" method=\"post\" class=\"well\">
\t<label>E-Mail</label><input type=\"text\" name=\"email\" placeholder=\"E-Mail\"  style=\"height: 24px;\"/>
\t<label>Company ID</label><input type=\"text\" name=\"companyid\" placeholder=\"Company Id\"  style=\"height: 24px;\"/>
\t<label>Password</label><input type=\"password\" name=\"password\" placeholder=\"Password\"  style=\"height: 24px;\"/>
\t<input type=\"hidden\" name=\"action\" value=\"login\" />
\t<label>
\t\tRemember Me: <input type=\"checkbox\" name=\"remember_me\">
\t</label>
\t<input type=\"submit\" value=\"Submit\">
</form>
</div>
";
    }

    public function getTemplateName()
    {
        return "user/login.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 4,  34 => 3,  27 => 2,);
    }
}
