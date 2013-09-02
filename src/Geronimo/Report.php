<?php
namespace Geronimo;

interface Report{
    public function bindData(array $data);
    public function generateReport();
}