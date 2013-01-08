<?php

/* users/profile.html.twig */
class __TwigTemplate_a6b63646f4ce96ac1541700bed839b5f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("base.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'name' => array($this, 'block_name'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
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
        echo "Profile";
    }

    // line 3
    public function block_name($context, array $blocks = array())
    {
        echo "Profile";
    }

    // line 5
    public function block_body($context, array $blocks = array())
    {
        // line 6
        echo "<div class=\"row\">
\t<div class=\"span12\">
\t\t<form method=\"post\" class=\"form form-inline\">
\t\t\t<input type=\"hidden\" name=\"update\" value=\"password\">
\t\t\t<input type=\"password\" name=\"old_pass\" placeholder=\"Old Password\">
\t\t\t<input type=\"password\" name=\"password\" placeholder=\"Password\">
\t\t\t<input type=\"password\" name=\"password2\" placeholder=\"Confirm\">
\t\t\t<input type=\"submit\" value=\"Save\">
\t\t</form>
\t</div>
\t";
        // line 16
        if ((twig_length_filter($this->env, (isset($context["companies"]) ? $context["companies"] : null)) > 1)) {
            // line 17
            echo "\t<div class=\"span12\">
\t\t<form method=\"post\" class=\"form form-inline\">
\t\t\t<input type=\"hidden\" name=\"update\" value=\"as\">
\t\t\t<select name=\"as\" id=\"asSelect\">
\t\t\t\t";
            // line 21
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["companies"]) ? $context["companies"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["comp"]) {
                // line 22
                echo "\t\t\t\t\t<option";
                if (($this->getAttribute((isset($context["comp"]) ? $context["comp"] : null), "id") == $this->getAttribute((isset($context["me"]) ? $context["me"] : null), "as"))) {
                    echo " selected=\"selected\"";
                }
                echo " value=\"";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["comp"]) ? $context["comp"] : null), "id"), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["comp"]) ? $context["comp"] : null), "name"), "html", null, true);
                echo "</option>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['comp'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 24
            echo "\t\t\t</select>
\t\t\t<input type=\"submit\" value=\"Update\" id=\"updateButton\"/>
\t\t</form>
\t</div>
\t";
        }
        // line 29
        echo "\t
</div>
";
    }

    // line 32
    public function block_javascripts($context, array $blocks = array())
    {
        // line 33
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "
";
        // line 34
        if ((twig_length_filter($this->env, (isset($context["companies"]) ? $context["companies"] : null)) > 1)) {
            // line 35
            echo "<script type=\"text/javascript\">
\$(document).ready(function(){
\t\$(\"#updateButton\").click(function() {
\t\tvar newCompany = \$(\"#asSelect\").val();
   \t\t// This sends us an event every time a user clicks the button
\t    mixpanel.track(\"changingCompany\", {
\t    \t'oldCompany':'";
            // line 41
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "as"), "html", null, true);
            echo "',
\t    \t'newCompany':newCompany
\t    }); 

\t});
});
</script>
";
        }
    }

    public function getTemplateName()
    {
        return "users/profile.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  113 => 41,  105 => 35,  103 => 34,  99 => 33,  96 => 32,  90 => 29,  83 => 24,  68 => 22,  64 => 21,  58 => 17,  56 => 16,  44 => 6,  41 => 5,  35 => 3,  29 => 2,);
    }
}
