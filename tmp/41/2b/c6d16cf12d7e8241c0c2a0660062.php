<?php

/* devices/index.html.twig */
class __TwigTemplate_412bc6d16cf12d7e8241c0c2a0660062 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("base.html.twig");

        $this->blocks = array(
            'afterbrand' => array($this, 'block_afterbrand'),
            'title' => array($this, 'block_title'),
            'name' => array($this, 'block_name'),
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
    public function block_afterbrand($context, array $blocks = array())
    {
        // line 3
        echo "\t";
        if (((isset($context["perms"]) ? $context["perms"] : null) & $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "permissions"), "devices/delete", array(), "array"))) {
            // line 4
            echo "\t\t<a class=\"right btn btn-small btn-danger\" href=\"";
            echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
            echo "devices/delete\">Delete</a>
\t";
        }
        // line 6
        echo "\t";
        if (((isset($context["perms"]) ? $context["perms"] : null) & $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "permissions"), "devices/add", array(), "array"))) {
            // line 7
            echo "\t\t<a class=\"right btn btn-small btn-success\" href=\"";
            echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
            echo "devices/add\">New</a>
\t";
        }
    }

    // line 10
    public function block_title($context, array $blocks = array())
    {
        echo "Devices ";
    }

    // line 11
    public function block_name($context, array $blocks = array())
    {
        echo "Devices";
    }

    // line 12
    public function block_body($context, array $blocks = array())
    {
        // line 13
        echo "<div class=\"row\">
\t<div class=\"row span12\">
\t\t<div class=\"status span1\">
\t\t\t<h4>Status</h4>
\t\t</div>
\t\t<div class=\"make span1\">
\t\t\t<h4>Make</h4>
\t\t</div>
\t\t<div class=\"model span2\">
\t\t\t<h4>Model</h4>
\t\t</div><div class=\"ip span2\">
\t\t\t<h4>IP Address</h4>
\t\t</div>
\t\t<div class=\"name span2\">
\t\t\t<h4>Name</h4>
\t\t</div>
\t\t<div class=\"version span2\">
\t\t\t<h4>Software Version</h4>
\t\t</div>
\t\t<div class=\"type span1\">
\t\t\t<h4>Type</h4>
\t\t</div>
\t</div>
\t";
        // line 36
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["devices"]) ? $context["devices"] : null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["device"]) {
            // line 37
            echo "\t<div class=\"row span12\">
\t\t<div class=\"status span1\">
\t\t\t<img class=\"noprint\" src=\"assets/img/devices/";
            // line 39
            if (($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "online") == 1)) {
                if (($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "in_call") == 1)) {
                    echo "calling";
                } else {
                    echo "Online";
                }
            } else {
                echo "Offline";
            }
            echo ".png\" />
\t\t</div>
\t\t<div class=\"make span1\">
\t\t\t";
            // line 42
            if (($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "make") != null)) {
                // line 43
                echo "\t\t\t";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "make"), "html", null, true);
                echo "
\t\t\t";
            } else {
                // line 45
                echo "\t\t\tUnknown
\t\t\t";
            }
            // line 47
            echo "\t\t</div>
\t\t<div class=\"model span2\">
\t\t\t";
            // line 49
            if (($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "model") != null)) {
                // line 50
                echo "\t\t\t";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "model"), "html", null, true);
                echo "
\t\t\t";
            } else {
                // line 52
                echo "\t\t\tUnknown
\t\t\t";
            }
            // line 54
            echo "\t\t</div>
\t\t<div class=\"ip span2\">
\t\t\t";
            // line 56
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "ip"), "html", null, true);
            echo "
\t\t</div>
\t\t<div class=\"name span2\">
\t\t\t";
            // line 59
            if (($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "serial") != "New Device")) {
                // line 60
                echo "\t\t\t\t<a href=\"";
                echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
                echo "devices/view/";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "id"), "html", null, true);
                echo "\">";
                if (($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "name") == null)) {
                    echo "Unknown";
                } else {
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "name"), "html", null, true);
                }
                echo "</a>
\t\t\t";
            } else {
                // line 62
                echo "\t\t\t\t";
                if (($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "name") == null)) {
                    echo "Unknown";
                } else {
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "name"), "html", null, true);
                }
                // line 63
                echo "\t\t\t";
            }
            // line 64
            echo "\t\t</div>
\t\t<div class=\"version span2\">
\t\t\t";
            // line 66
            if (($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "version") != null)) {
                // line 67
                echo "\t\t\t";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "version"), "html", null, true);
                echo "
\t\t\t";
            } else {
                // line 69
                echo "\t\t\tUnknown
\t\t\t";
            }
            // line 71
            echo "\t\t</div>
\t\t<div class=\"type span1\">
\t\t\t";
            // line 73
            if (($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "type") != null)) {
                // line 74
                echo "\t\t\t<img class=\"noprint\" src=\"assets/img/devices/";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "type"), "html", null, true);
                echo ".png\" />
\t\t\t";
            } else {
                // line 76
                echo "\t\t\t?
\t\t\t";
            }
            // line 78
            echo "\t\t</div>
\t</div>
\t";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 81
            echo "\t<div class=\"row span12\">
\t\t<p>No devices found</p>
\t</div>
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['device'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 85
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "devices/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  227 => 85,  218 => 81,  211 => 78,  207 => 76,  201 => 74,  199 => 73,  195 => 71,  191 => 69,  185 => 67,  183 => 66,  179 => 64,  176 => 63,  169 => 62,  155 => 60,  153 => 59,  147 => 56,  143 => 54,  139 => 52,  133 => 50,  131 => 49,  127 => 47,  123 => 45,  117 => 43,  115 => 42,  101 => 39,  97 => 37,  92 => 36,  67 => 13,  64 => 12,  58 => 11,  52 => 10,  44 => 7,  41 => 6,  35 => 4,  32 => 3,  29 => 2,);
    }
}
