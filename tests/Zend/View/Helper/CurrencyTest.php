<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View\Helper;

use Zend\Cache\StorageFactory as CacheFactory;
use Zend\Cache\Storage\Adapter\AdapterInterface as CacheAdapter;
use Zend\Currency;
use Zend\View\Helper;

/**
 * Test class for Zend_View_Helper_Currency
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_Currency
     */
    public $helper;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $cache = CacheFactory::adapterFactory('memory', array('memory_limit' => 0));
        Currency\Currency::setCache($cache);

        $this->helper = new Helper\Currency('de_AT');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function testCurrencyObjectPassedToConstructor()
    {
        $curr = new Currency\Currency('de_AT');

        $helper = new Helper\Currency($curr);
        $this->assertEquals('€ 1.234,56', $helper->__invoke(1234.56));
        $this->assertEquals('€ 0,12', $helper->__invoke(0.123));
    }

    public function testLocalCurrencyObjectUsedWhenPresent()
    {
        $curr = new Currency\Currency('de_AT');

        $this->helper->setCurrency($curr);
        $this->assertEquals('€ 1.234,56', $this->helper->__invoke(1234.56));
        $this->assertEquals('€ 0,12', $this->helper->__invoke(0.123));
    }

    public function testPassingNonNullNonCurrencyObjectToConstructorThrowsException()
    {
        try {
            $helper = new Helper\Currency('something');
        } catch (\Exception $e) {
            if (substr($e->getMessage(), 0, 15) == 'No region found') {
                $this->assertContains('within the locale', $e->getMessage());
            } else {
                $this->assertContains('not found', $e->getMessage());
            }
        }
    }

    public function testPassingNonCurrencyObjectToSetCurrencyThrowsException()
    {
        try {
            $this->helper->setCurrency('something');
        } catch (\Exception $e) {
            if (substr($e->getMessage(), 0, 15) == 'No region found') {
                $this->assertContains('within the locale', $e->getMessage());
            } else {
                $this->assertContains('not found', $e->getMessage());
            }
        }
    }

    public function testCanOutputCurrencyWithOptions()
    {
        $curr = new Currency\Currency('de_AT');

        $this->helper->setCurrency($curr);
        $this->assertEquals("€ 1.234,56", $this->helper->__invoke(1234.56, "de_AT"));
    }

    public function testCurrencyObjectNullByDefault()
    {
        $this->assertNotNull($this->helper->getCurrency());
    }

    public function testHelperObjectReturnedWhenNoArgumentsPassed()
    {
        $helper = $this->helper->__invoke();
        $this->assertSame($this->helper, $helper);

        $currLoc = new Currency\Currency('de_AT');
        $this->helper->setCurrency($currLoc);
        $helper = $this->helper->__invoke();
        $this->assertSame($this->helper, $helper);
    }
}
