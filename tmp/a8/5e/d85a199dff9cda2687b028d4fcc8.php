<?php

/* devices/view.html.twig */
class __TwigTemplate_a85ed85a199dff9cda2687b028d4fcc8 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("base.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'afterbrand' => array($this, 'block_afterbrand'),
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
        if (($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "name") == null)) {
            echo "ID: ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "id"), "html", null, true);
        } else {
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "name"), "html", null, true);
        }
    }

    // line 3
    public function block_afterbrand($context, array $blocks = array())
    {
        // line 4
        echo "
";
    }

    // line 6
    public function block_name($context, array $blocks = array())
    {
        echo "Device <div style=\"display: inline-block; line-height: 0;\"><small>";
        if (($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "name") == null)) {
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "id"), "html", null, true);
        } else {
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "name"), "html", null, true);
        }
        echo "<br />";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "ip"), "html", null, true);
        echo "</small></div>
";
        // line 7
        if (($this->getAttribute((isset($context["user"]) ? $context["user"] : null), "planName") != "Basic")) {
            // line 8
            echo "    ";
            if ((($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "own") == 1) && ($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "verified") == 0))) {
                // line 9
                echo "    \t";
                if (((isset($context["perms"]) ? $context["perms"] : null) & $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "permissions"), "devices/edit", array(), "array"))) {
                    // line 10
                    echo "    \t\t<a class=\"btn btn-small btn-inverse noprint\" href=\"";
                    echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
                    echo "devices/verify/";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "id"), "html", null, true);
                    echo "\">Verify Device</a>
    \t";
                }
                // line 12
                echo "    ";
            }
            // line 13
            echo "    ";
            if ((($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "own") == 1) && ($this->getAttribute((isset($context["device"]) ? $context["device"] : null), "verified") == 1))) {
                // line 14
                echo "    \t";
                if (((isset($context["perms"]) ? $context["perms"] : null) & $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "permissions"), "devices/edit", array(), "array"))) {
                    // line 15
                    echo "    \t\t<a class=\"btn btn-small btn-inverse noprint\" href=\"";
                    echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
                    echo "devices/edit/";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "id"), "html", null, true);
                    echo "\">Edit Device</a>
    \t";
                }
                // line 17
                echo "    ";
            }
        }
    }

    // line 20
    public function block_body($context, array $blocks = array())
    {
        // line 21
        echo "<p class=\"muted\"><small>Updated: ";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "updated"), "M/d/y h:i:s a"), "html", null, true);
        echo "</small></p>
<h2>Packets Lost per Minute</h2>
<div class=\"row\">
\t<div id=\"graph_container\" class=\"span10 offset1\"></div>
</div>
<div class=\"accordion\" id=\"accordion2\">
<div class=\"accordion-group\">
<div class=\"accordion-heading\">
  <a class=\"accordion-toggle\" data-toggle=\"collapse\" data-parent=\"#accordion2\" href=\"#collapseOne\">
    Details
  </a>
</div>
<div id=\"collapseOne\" class=\"accordion-body collapse span12\">
  <div class=\"accordion-inner span12\">
\t<div class=\"row\">
        <div class=\"span4\">
            ";
        // line 37
        if ((isset($context["active_call"]) ? $context["active_call"] : null)) {
            // line 38
            echo "                <h4>Active Call<small> | Call Duration: ";
            echo twig_escape_filter($this->env, sprintf("%.2f", $this->getAttribute((isset($context["active_duration"]) ? $context["active_duration"] : null), "count")), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["active_duration"]) ? $context["active_duration"] : null), "scale"), "html", null, true);
            echo "</small></h4>
                    <h5>Recieve:</h5>
                    <div class=\"row\" style=\"margin-top: -15px;\">
                        <div class=\"span2\">
                            <h6>Video</h6>
                            Cumulative: ";
            // line 43
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["active_call"]) ? $context["active_call"] : null), "VRX_Pkts"), "html", null, true);
            echo "<br />
                            Percentage: ";
            // line 44
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["active_call"]) ? $context["active_call"] : null), "VRX_Pcnt"), "html", null, true);
            echo "
                        </div>
                        <div class=\"span2\">
                            <h6>Audio</h6>
                            Cumulative: ";
            // line 48
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["active_call"]) ? $context["active_call"] : null), "ARX_Pkts"), "html", null, true);
            echo "<br />
                            Percentage: ";
            // line 49
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["active_call"]) ? $context["active_call"] : null), "ARX_Pcnt"), "html", null, true);
            echo "
                        </div>
                    </div>
                    <h5>Transmit:</h5>
                    <div class=\"row\" style=\"margin-top: -15px;\">
                        <div class=\"span2\">
                            <h6>Video</h6>
                            Cumulative: ";
            // line 56
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["active_call"]) ? $context["active_call"] : null), "VTX_Pkts"), "html", null, true);
            echo "<br />
                            Percentage: ";
            // line 57
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["active_call"]) ? $context["active_call"] : null), "VTX_Pcnt"), "html", null, true);
            echo "
                        </div>
                        <div class=\"span2\">
                            <h6>Audio</h6>
                            Cumulative: ";
            // line 61
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["active_call"]) ? $context["active_call"] : null), "ATX_Pkts"), "html", null, true);
            echo "<br />
                            Percentage: ";
            // line 62
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["active_call"]) ? $context["active_call"] : null), "ATX_Pcnt"), "html", null, true);
            echo "
                        </div>
                    </div>
            ";
        } else {
            // line 66
            echo "                    <p>No calls active</p>
            ";
        }
        // line 68
        echo "        </div>
\t\t<div class=\"span4\">
\t\t\t<h4>Last Call<small> | Call Duration: ";
        // line 70
        echo twig_escape_filter($this->env, sprintf("%.2f", $this->getAttribute((isset($context["duration"]) ? $context["duration"] : null), "count")), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["duration"]) ? $context["duration"] : null), "scale"), "html", null, true);
        echo "</small></h4>
\t\t\t";
        // line 71
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss0"]) ? $context["loss0"] : null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["l"]) {
            // line 72
            echo "\t\t\t\t<span class=\"";
            if (($this->getAttribute((isset($context["l"]) ? $context["l"] : null), "loss") < $this->getAttribute((isset($context["average_loss"]) ? $context["average_loss"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"))) {
                echo "good";
            } else {
                echo "bad";
            }
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["packetnames"]) ? $context["packetnames"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"), "html", null, true);
            echo ": ";
            echo twig_escape_filter($this->env, sprintf("%.2f", $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "loss")), "html", null, true);
            echo "</span><br />
\t\t\t\t";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 74
            echo "\t\t\t\t<p>No calls within this period</p>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['l'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 76
        echo "\t\t</div>
        <div class=\"span4\">
            <h4>Averages</h4>
            Global | ";
        // line 79
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "model"), "html", null, true);
        echo "<br />
            ";
        // line 80
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss0"]) ? $context["loss0"] : null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["l"]) {
            // line 81
            echo "                ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["packetnames"]) ? $context["packetnames"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"), "html", null, true);
            echo ": ";
            echo twig_escape_filter($this->env, sprintf("%.2f", $this->getAttribute((isset($context["average_loss"]) ? $context["average_loss"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array")), "html", null, true);
            echo " | ";
            echo twig_escape_filter($this->env, sprintf("%.2f", $this->getAttribute((isset($context["device_averages"]) ? $context["device_averages"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array")), "html", null, true);
            echo "<br />
                ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 83
            echo "                <p>No calls within this period</p>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['l'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 85
        echo "        </div>
\t</div>
\t<div class=\"row\">
        <div class=\"span4\">
            <h4>Last 7 Days<small>:";
        // line 89
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["call_counts"]) ? $context["call_counts"] : null), 7, array(), "array"), "html", null, true);
        echo " Calls</small></h4>
            ";
        // line 90
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss7"]) ? $context["loss7"] : null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["l"]) {
            // line 91
            echo "                <span class=\"";
            if (($this->getAttribute((isset($context["l"]) ? $context["l"] : null), "loss") < $this->getAttribute((isset($context["average_loss"]) ? $context["average_loss"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"))) {
                echo "good";
            } else {
                echo "bad";
            }
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["packetnames"]) ? $context["packetnames"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"), "html", null, true);
            echo ": ";
            echo twig_escape_filter($this->env, sprintf("%.0f", $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "loss")), "html", null, true);
            echo "</span><br />
                ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 93
            echo "                <p>No calls within this period</p>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['l'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 95
        echo "        </div>
        <div class=\"span4\">
\t\t\t<h4>Last 30 Days<small>:";
        // line 97
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["call_counts"]) ? $context["call_counts"] : null), 30, array(), "array"), "html", null, true);
        echo " Calls</small></h4>
\t\t\t";
        // line 98
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss30"]) ? $context["loss30"] : null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["l"]) {
            // line 99
            echo "\t\t\t\t<span class=\"";
            if (($this->getAttribute((isset($context["l"]) ? $context["l"] : null), "loss") < $this->getAttribute((isset($context["average_loss"]) ? $context["average_loss"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"))) {
                echo "good";
            } else {
                echo "bad";
            }
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["packetnames"]) ? $context["packetnames"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"), "html", null, true);
            echo ": ";
            echo twig_escape_filter($this->env, sprintf("%.0f", $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "loss")), "html", null, true);
            echo "</span><br />
\t\t\t\t";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 101
            echo "\t\t\t\t<p>No calls within this period</p>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['l'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 103
        echo "\t\t</div>
\t\t<div class=\"span4\">
\t\t\t<h4>Last 60 Days<small>:";
        // line 105
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["call_counts"]) ? $context["call_counts"] : null), 60, array(), "array"), "html", null, true);
        echo " Calls</small></h4>
\t\t\t";
        // line 106
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss60"]) ? $context["loss60"] : null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["l"]) {
            // line 107
            echo "\t\t\t\t<span class=\"";
            if (($this->getAttribute((isset($context["l"]) ? $context["l"] : null), "loss") < $this->getAttribute((isset($context["average_loss"]) ? $context["average_loss"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"))) {
                echo "good";
            } else {
                echo "bad";
            }
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["packetnames"]) ? $context["packetnames"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"), "html", null, true);
            echo ": ";
            echo twig_escape_filter($this->env, sprintf("%.2f", $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "loss")), "html", null, true);
            echo "</span><br />
\t\t\t\t";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 109
            echo "\t\t\t\t<p>No calls within this period</p>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['l'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 111
        echo "\t\t</div>
\t</div>
\t<div class=\"row\">
\t\t<div class=\"span4\">
\t\t\t<h4>Last 90 Days<small>:";
        // line 115
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["call_counts"]) ? $context["call_counts"] : null), 90, array(), "array"), "html", null, true);
        echo " Calls</small></h4>
\t\t\t";
        // line 116
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss90"]) ? $context["loss90"] : null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["l"]) {
            // line 117
            echo "\t\t\t\t<span class=\"";
            if (($this->getAttribute((isset($context["l"]) ? $context["l"] : null), "loss") < $this->getAttribute((isset($context["average_loss"]) ? $context["average_loss"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"))) {
                echo "good";
            } else {
                echo "bad";
            }
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["packetnames"]) ? $context["packetnames"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"), "html", null, true);
            echo ": ";
            echo twig_escape_filter($this->env, sprintf("%.2f", $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "loss")), "html", null, true);
            echo "</span><br />
\t\t\t\t";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 119
            echo "\t\t\t\t<p>No calls within this period</p>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['l'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 121
        echo "\t\t</div>
\t\t<div class=\"span4\">
\t\t\t<h4>Last 120 Days<small>:";
        // line 123
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["call_counts"]) ? $context["call_counts"] : null), 120, array(), "array"), "html", null, true);
        echo " Calls</small></h4>
\t\t\t";
        // line 124
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss120"]) ? $context["loss120"] : null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["l"]) {
            // line 125
            echo "\t\t\t\t<span class=\"";
            if (($this->getAttribute((isset($context["l"]) ? $context["l"] : null), "loss") < $this->getAttribute((isset($context["average_loss"]) ? $context["average_loss"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"))) {
                echo "good";
            } else {
                echo "bad";
            }
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["packetnames"]) ? $context["packetnames"] : null), $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "name"), array(), "array"), "html", null, true);
            echo ": ";
            echo twig_escape_filter($this->env, sprintf("%.2f", $this->getAttribute((isset($context["l"]) ? $context["l"] : null), "loss")), "html", null, true);
            echo "</span><br />
\t\t\t\t";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 127
            echo "\t\t\t\t<p>No calls within this period</p>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['l'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 129
        echo "\t\t</div>
\t</div>

  </div>
</div>
</div>
";
    }

    // line 137
    public function block_javascripts($context, array $blocks = array())
    {
        // line 138
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "

<script type=\"text/javascript\" src=\"";
        // line 140
        echo twig_escape_filter($this->env, (isset($context["root"]) ? $context["root"] : null), "html", null, true);
        echo "assets/highcharts/js/highcharts.js\"></script>
<script type=\"text/javascript\">
\$(function () {
    var chart;
    \$(document).ready(function() {
        chart = new Highcharts.Chart({

            chart: {
                renderTo: 'graph_container',
                type: 'column'
            },

            title: {
                text: 'Packet Loss'
            },

            xAxis: {
                categories: [";
        // line 157
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss_names"]) ? $context["loss_names"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["name"]) {
            echo "'";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["packetnames"]) ? $context["packetnames"] : null), (isset($context["name"]) ? $context["name"] : null), array(), "array"), "html", null, true);
            echo "',";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['name'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "],
            },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Packets Lost per Minute'
                },

            },

            tooltip: {
            \tformatter: function() {
                var s = '<b>'+ this.x +'</b>';

                \$.each(this.points, function(i, point) {
                    s += '<br/>'+ point.series.name +': '+
                        Math.round(point.y * 1000) / 1000;
                });

                return s;
            },
            \tshared: true,
        \t},
    \t\tcredits: {
    \t\t\tenabled: false
    \t\t},
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },

            series: [{
                name: 'Average Loss',
                data: [";
        // line 193
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["average_loss"]) ? $context["average_loss"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["loss"]) {
            echo twig_escape_filter($this->env, (isset($context["loss"]) ? $context["loss"] : null), "html", null, true);
            echo ",";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['loss'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "],
            },
            {
                name: '";
        // line 196
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["device"]) ? $context["device"] : null), "model"), "html", null, true);
        echo " Average',
                data: [";
        // line 197
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["device_averages"]) ? $context["device_averages"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["loss"]) {
            echo twig_escape_filter($this->env, (isset($context["loss"]) ? $context["loss"] : null), "html", null, true);
            echo ",";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['loss'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "],
            },
            {
                name: 'Last 7 Days',
                data: [";
        // line 201
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss7"]) ? $context["loss7"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["loss"]) {
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["loss"]) ? $context["loss"] : null), "loss"), "html", null, true);
            echo ",";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['loss'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "],
            },
            {
                name: 'Last 30 Days',
                data: [";
        // line 205
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss30"]) ? $context["loss30"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["loss"]) {
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["loss"]) ? $context["loss"] : null), "loss"), "html", null, true);
            echo ",";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['loss'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "],
            },
            {
                name: 'Last 60 Days',
                data: [";
        // line 209
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss60"]) ? $context["loss60"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["loss"]) {
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["loss"]) ? $context["loss"] : null), "loss"), "html", null, true);
            echo ",";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['loss'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "],
            },
            {
                name: 'Last 90 Days',
                data: [";
        // line 213
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss90"]) ? $context["loss90"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["loss"]) {
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["loss"]) ? $context["loss"] : null), "loss"), "html", null, true);
            echo ",";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['loss'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "],
            },
            {
                name: 'Last 120 Days',
                data: [";
        // line 217
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["loss120"]) ? $context["loss120"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["loss"]) {
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["loss"]) ? $context["loss"] : null), "loss"), "html", null, true);
            echo ",";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['loss'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo "],
            }]
        });
    });
});
</script>
";
    }

    public function getTemplateName()
    {
        return "devices/view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  622 => 217,  607 => 213,  592 => 209,  577 => 205,  562 => 201,  547 => 197,  543 => 196,  529 => 193,  481 => 157,  461 => 140,  456 => 138,  453 => 137,  443 => 129,  436 => 127,  420 => 125,  415 => 124,  411 => 123,  407 => 121,  400 => 119,  384 => 117,  379 => 116,  375 => 115,  369 => 111,  362 => 109,  346 => 107,  341 => 106,  337 => 105,  333 => 103,  326 => 101,  310 => 99,  305 => 98,  301 => 97,  297 => 95,  290 => 93,  274 => 91,  269 => 90,  265 => 89,  259 => 85,  252 => 83,  240 => 81,  235 => 80,  231 => 79,  226 => 76,  219 => 74,  203 => 72,  198 => 71,  192 => 70,  188 => 68,  184 => 66,  177 => 62,  173 => 61,  166 => 57,  162 => 56,  152 => 49,  148 => 48,  141 => 44,  137 => 43,  126 => 38,  124 => 37,  104 => 21,  101 => 20,  95 => 17,  87 => 15,  84 => 14,  81 => 13,  78 => 12,  70 => 10,  67 => 9,  64 => 8,  62 => 7,  49 => 6,  44 => 4,  41 => 3,  30 => 2,);
    }
}
