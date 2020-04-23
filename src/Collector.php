<?php

namespace Turbo124\Collector;

use Illuminate\Support\Facades\Cache;
use Turbo124\Collector\Collector\Generator;
use Turbo124\Collector\Jobs\CreateMetric;

class Collector
{

    public $metric;

    public function __construct()
    {
    }

    public function create($metric)
    {
    	$this->metric = $metric;
    	$this->metric->datetime = date("Y-m-d H:i:s");
    	$this->metric->api_key = config('collector.api_key');

        return $this;
    }

    public function increment()
    {
    	$this->metric->counter++;

    	return $this;
    }

    public function decrement()
    {
    	$this->metric->counter--;

    	return $this;
    }

    public function setCount($count)
    {
    	$this->metric->counter = $count;

    	return $this;
    }

    public function send()
    {
        $generator = new Generator();
    	$generator->fire($this->metric);
    }

    public function queue()
    {
        CreateMetric::dispatch($this->metric);
    }

    public function batch()
    {
        Cache::tags(['collector'])->put(config('collector.api_key'), $this->metric);
    }
}
