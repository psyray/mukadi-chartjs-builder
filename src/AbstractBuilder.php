<?php
/**
 * This file is part of the mukadi/chartjs-builder
 * (c) 2018 Genius Conception
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Mukadi\Chart;
/**
 * Class AbstractBuilder.
 * 
 * @author Olivier M. Mukadi <olivier.m@geniusconception.com>
 */
abstract class AbstractBuilder  
{
    /**
     * @var array
     */
    protected $labels;
    /**
     * @var array
     */
    protected $datasets;
    /**
     * @var array
     */
    protected $options;
    /**
     * @var boolean
     */
    protected $hasLabels;

    public function __construct(){
        $this->labels = null;
        $this->datasets = array();
        $this->options = array();
        $this->hasLabels = false;
    }
    /**
     * @return array
     */
    abstract protected function getData();

    /**
     * @param string $query
     * @return string
     * @return self
     */
    abstract public function query($query);

    /**
     * @param string $key
     * @param mixed $value
     * @return string
     * @return self
     */
    abstract public function setParameter($key, $value);

    /**
     * @param string $key
     * @param string $label
     * @param array $options
     * @return self
     */
    public function addDataset($key, $label = "", $options = []) {
        $this->datasets[$key] = [
            "label" => $label,
        ];
        $this->options[$key] = $options;
        return $this;
    }

    /**
     * @param array|string $labels
     */
    public function labels($labels) {
        $this->labels = $labels;
        if(is_array($labels)) {
            $this->hasLabels = true;
        }else{
            $this->hasLabels = false;
        }
    }

    protected function computeData() {
        $data = $this->getData();
        $keys = array_keys($this->datasets);
        $labels = $this->hasLabels? $this->labels: [];
        foreach ($data as $input) {
            if (!$this->hasLabels) {
                $labels[] = $input[$this->labels];
            }
            foreach($keys as $k) {
                $this->datasets[$k]['data'][] = $input[$k];
            }
        }
        foreach($keys as $k) {
            $this->datasets[$k] = array_merge($this->datasets[$k],$this->options[$k]);
        }
        $this->labels = $labels;
        $this->hasLabels = true;
    }

    /**
     * @param string $id
     * @param null|string $type
     * @param null|array $options
     * @return Chart
     */
    public function buildChart($id, $type = null,$options=null) {
        $this->computeData();
        $c = new Chart($id);
        $c->setLabels($this->labels);
        $c->setDatasets(array_values($this->datasets));
        if(!is_null($type)) $c->setType($type);
        if(is_array($options)) $c->pushOptions($options);
        return $c;
    }
}
