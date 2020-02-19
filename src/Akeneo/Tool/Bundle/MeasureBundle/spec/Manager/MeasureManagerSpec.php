<?php

namespace spec\Akeneo\Tool\Bundle\MeasureBundle\Manager;

use Akeneo\Tool\Bundle\MeasureBundle\Family\WeightFamilyInterface;
use Akeneo\Tool\Bundle\MeasureBundle\Provider\LegacyMeasurementProvider;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Yaml\Yaml;

/**
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MeasureManagerSpec extends ObjectBehavior
{
    function let()
    {
        $yaml = <<<YAML
measures_config:
    Length:
        standard: METER
        units:
            CENTIMETER:
                convert: [{'div': 0.01}]
                format: cm
            METER:
                convert: [{'test': 1}]
                format: m
    Weight:
        standard: GRAM
        units:
            MILLIGRAM:
                convert: [{'mul': 0.001}]
                symbol: mg
            GRAM:
                convert: [{'mul': 1}]
                symbol: g
            KILOGRAM:
                convert: [{'mul': 1000}]
                symbol: kg

YAML;

        $config = Yaml::parse($yaml);

        $this->setMeasureConfig($config['measures_config']);
    }

    public function it_throws_an_exception_when_try_to_get_symbols_of_unknown_family()
    {
        $this
            ->shouldThrow(
                new \InvalidArgumentException('Undefined measure family "foo"')
            )
            ->during('getUnitSymbolsForFamily', ['foo']);

        $this
            ->shouldThrow(
                new \InvalidArgumentException('Undefined measure family "foo"')
            )
            ->during('getUnitCodesForFamily', ['foo']);
    }

    public function it_returns_unit_symbols_list_from_a_family()
    {
        $this
            ->getUnitSymbolsForFamily(WeightFamilyInterface::FAMILY)
            ->shouldReturn(
                [
                    'MILLIGRAM' => 'mg',
                    'GRAM'      => 'g',
                    'KILOGRAM'  => 'kg'
                ]
            );
    }

    public function it_indicates_wether_a_unit_symbol_exists_for_a_family()
    {
        $this
            ->unitSymbolExistsInFamily('mg', WeightFamilyInterface::FAMILY)
            ->shouldReturn(true);

        $this
            ->unitSymbolExistsInFamily('foo', WeightFamilyInterface::FAMILY)
            ->shouldReturn(false);
    }

    public function it_returns_standard_unit_for_a_family()
    {
        $this
            ->getStandardUnitForFamily(WeightFamilyInterface::FAMILY)
            ->shouldReturn(WeightFamilyInterface::GRAM);
    }

    public function it_returns_unit_codes_for_a_family()
    {
        $this
            ->getUnitCodesForFamily(WeightFamilyInterface::FAMILY)
            ->shouldReturn(['MILLIGRAM', 'GRAM', 'KILOGRAM']);
    }

    public function it_indicates_wether_a_unit_code_exists_for_a_family()
    {
        $this
            ->unitCodeExistsInFamily(WeightFamilyInterface::GRAM, WeightFamilyInterface::FAMILY)
            ->shouldReturn(true);

        $this
            ->unitCodeExistsInFamily('FOO', WeightFamilyInterface::FAMILY)
            ->shouldReturn(false);
    }
}
