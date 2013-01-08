<?php

/* dashboard.html.twig */
class __TwigTemplate_edbda88f985489b498e3c0bc1ea6ea3c extends Twig_Template
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
        echo "Dashboard";
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        echo "<div class=\"row\">
\t<div class=\"span6 dash-container\">
\t\t<div class=\"row title\">
\t\t\tDevice Inventory
\t\t</div>
\t\t<div class=\"row even\">
\t\t\t<div class=\"span5\">
\t\t\t\tAll Devices
\t\t\t</div>
\t\t\t<div class=\"span1\">
\t\t\t\t";
        // line 14
        echo twig_escape_filter($this->env, (isset($context["devices_count"]) ? $context["devices_count"] : null), "html", null, true);
        echo "
\t\t\t</div>
\t\t</div>
\t\t<div class=\"row odd\">
\t\t\t<div class=\"span5\">
\t\t\t\tVideo Devices
\t\t\t</div>
\t\t\t<div class=\"span1\">
\t\t\t\t";
        // line 22
        echo twig_escape_filter($this->env, (isset($context["video_count"]) ? $context["video_count"] : null), "html", null, true);
        echo "
\t\t\t</div>
\t\t</div>
\t\t<div class=\"row even\">
\t\t\t<div class=\"span5\">
\t\t\t\tDevices Participating in a call
\t\t\t</div>
\t\t\t<div class=\"span1\">
\t\t\t\t";
        // line 30
        echo twig_escape_filter($this->env, (isset($context["in_a_call"]) ? $context["in_a_call"] : null), "html", null, true);
        echo "
\t\t\t</div>
\t\t</div>
\t\t<div class=\"row odd\">
\t\t\t<div class=\"span5\">
\t\t\t\tDevices being updated
\t\t\t</div>
\t\t\t<div class=\"span1\">
\t\t\t\t";
        // line 38
        echo twig_escape_filter($this->env, (isset($context["updating"]) ? $context["updating"] : null), "html", null, true);
        echo "
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"span6\">
\t\t<div class=\"span6 dash-container\">
\t\t\t<div class=\"row title\">
\t\t\t\tCall History
\t\t\t</div>
\t\t\t<div class=\"row even\">
\t\t\t\t<div class=\"span5\">
\t\t\t\t\tTotal number of calls
\t\t\t\t</div>
\t\t\t\t<div class=\"span1\">
\t\t\t\t\t";
        // line 52
        echo twig_escape_filter($this->env, (isset($context["call_count"]) ? $context["call_count"] : null), "html", null, true);
        echo "
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"row odd\">
\t\t\t\t<div class=\"span5\">
\t\t\t\t\tDevices with no calls
\t\t\t\t</div>
\t\t\t\t<div class=\"span1\">
\t\t\t\t\t";
        // line 60
        echo twig_escape_filter($this->env, (isset($context["unused_devices"]) ? $context["unused_devices"] : null), "html", null, true);
        echo "
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"row even\">
\t\t\t\t<div class=\"span5\">
\t\t\t\t\tDevices used at least once
\t\t\t\t</div>
\t\t\t\t<div class=\"span1\">
\t\t\t\t\t";
        // line 68
        echo twig_escape_filter($this->env, (isset($context["devices_used"]) ? $context["devices_used"] : null), "html", null, true);
        echo "
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"row odd\">
\t\t\t\t<div class=\"span5\">
\t\t\t\t\tTotal duration of calls in ";
        // line 73
        echo twig_escape_filter($this->env, (isset($context["scale"]) ? $context["scale"] : null), "html", null, true);
        echo "
\t\t\t\t</div>
\t\t\t\t<div class=\"span1\">
\t\t\t\t\t";
        // line 76
        echo twig_escape_filter($this->env, (isset($context["call_time"]) ? $context["call_time"] : null), "html", null, true);
        echo "
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"span6 dash-container\">
\t\t\t<div class=\"row title\">
\t\t\t\tAlarms
\t\t\t</div>
\t\t\t<div class=\"row even\">
\t\t\t\t<div class=\"span5\">
\t\t\t\t\tMy Active Alarms
\t\t\t\t</div>
\t\t\t\t<div class=\"span1\">
\t\t\t\t\t";
        // line 89
        echo twig_escape_filter($this->env, (isset($context["my_alarms"]) ? $context["my_alarms"] : null), "html", null, true);
        echo "
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"row odd\">
\t\t\t\t<div class=\"span5\">
\t\t\t\t\tAll Alarms
\t\t\t\t</div>
\t\t\t\t<div class=\"span1\">
\t\t\t\t\t";
        // line 97
        echo twig_escape_filter($this->env, (isset($context["all_alarms"]) ? $context["all_alarms"] : null), "html", null, true);
        echo "
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
\t";
        // line 102
        if (($this->getAttribute((isset($context["alarm"]) ? $context["alarm"] : null), "ip") != null)) {
            // line 103
            echo "\t<div class=\"span12\">
\t\tLatest Alarm: ";
            // line 104
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["alarm"]) ? $context["alarm"] : null), "ip"), "html", null, true);
            echo " - ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["alarm"]) ? $context["alarm"] : null), "name"), "html", null, true);
            echo " - ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["alarm"]) ? $context["alarm"] : null), "description"), "html", null, true);
            echo "
\t</div>
\t";
        }
        // line 107
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "dashboard.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  184 => 107,  174 => 104,  171 => 103,  169 => 102,  161 => 97,  150 => 89,  134 => 76,  128 => 73,  120 => 68,  109 => 60,  98 => 52,  81 => 38,  70 => 30,  59 => 22,  48 => 14,  36 => 4,  33 => 3,  27 => 2,);
    }
}
