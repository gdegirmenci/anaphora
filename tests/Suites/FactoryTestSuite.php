<?php

namespace Tests\Suites;

use Tests\TestCase;

/**
 * Class FactoryTestSuite
 * @package Tests\Suites
 */
abstract class FactoryTestSuite extends TestCase
{
    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setFactory();
    }

    /**
     * @return void
     */
    abstract public function setFactory(): void;
}
