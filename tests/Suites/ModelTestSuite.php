<?php

namespace Tests\Suites;

use Tests\TestCase;

/**
 * Class ModelTestSuite
 * @package Tests\Suites
 */
abstract class ModelTestSuite extends TestCase
{
    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setModel();
    }

    /**
     * @return void
     */
    abstract public function setModel(): void;
}
