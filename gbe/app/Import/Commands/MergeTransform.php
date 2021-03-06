<?php

/*
 *
 "specification": {
        "trigger":"/(\\d{4})-(\\d{2})-(\\d{2})-(\\d{3})/",
        "lines_before":1,
        "lines_after":0,
        "output":[1,0],
        "separator":","
      },

 */

namespace DemocracyApps\GB\Import\Commands;


class MergeTransform extends DPCommand {
    private $specification = null;
    private $trigger = null;
    private $before = null, $after = null;
    private $select = null;
    private $separator = "";
    private $transforms = null;

    public function __construct($specification)
    {
        $this->specification = $specification;
        $this->trigger = $specification['trigger'];
        $this->before = $specification['before'];
        $this->after = $specification['after'];
        if (array_key_exists('select', $specification)) {
            $this->select = $specification['select'];
        }
        if (array_key_exists('separator', $specification)) {
            $this->separator = $specification['separator'];
        }
        $this->transforms = $specification['transforms'];
    }

    /**
     * @param \string[] $input
     * @return \string[] $output
     */
    public function process($input)
    {
        $output = array();
        for ($i=0; $i<sizeof($input); ++$i) {
            $line = $input[$i];
            if (preg_match($this->trigger, $line)) {
                $matched = array();
                if ($this->before != null && $this->before > 0) {
                    for ($j=0; $j<$this->before; ++$j) {
                        $matched[] = $input[$i-$this->before + $j];
                    }
                }
                $matched[] = $line;
                if ($this->after != null && $this->after > 0) {
                    for ($j=0; $j<$this->after; ++$j) {
                        $matched[] = $input[$i+1+$j];
                    }
                }
                /*
                 * Now output
                 */
                $outline = "";
                $glue = "";
                if ($this->select == null) { // Just concatenate
                    for ($j=0; $j<sizeof($matched); ++$j) {
                        $spec = $this->transforms[$j];
                        $match = $matched[$j];
                        if (array_key_exists('limit',$spec)) {
                            $outline .= $glue . preg_replace($spec['pattern'], $spec['replacement'], $match, $spec['limit']);
                        }
                        else {
                            $outline .= $glue . preg_replace($spec['pattern'], $spec['replacement'], $match);
                        }
                        $glue = $this->separator;
                    }
                }
                else {
                    foreach ($this->select as $which) {
                        $spec = $this->transforms[$which];
                        $match = $matched[$which];
                        if (array_key_exists('limit',$spec)) {
                            $outline .= $glue . preg_replace($spec['pattern'], $spec['replacement'], $match, $spec['limit']);
                        }
                        else {
                            $outline .= $glue . preg_replace($spec['pattern'], $spec['replacement'], $match);
                        }
                        $glue = $this->separator;
                    }
                }
                $output[] = $outline;
            }

        }

        return $output;
    }

}