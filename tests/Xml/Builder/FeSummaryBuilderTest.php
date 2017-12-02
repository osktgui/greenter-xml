<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 19/07/2017
 * Time: 10:55 AM
 */

namespace Tests\Greenter\Xml\Builder;

use Greenter\Model\Sale\Document;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Model\Summary\SummaryPerception;

/**
 * Class FeSummaryBuilderTest
 * @package tests\Greenter\Xml\Builder
 */
class FeSummaryBuilderTest extends \PHPUnit_Framework_TestCase
{
    use FeBuilderTrait;

    public function testCreateXmlSummary()
    {
        $summary = $this->getSummary();

        $xml = $this->build($summary);

        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $success = $doc->schemaValidate(__DIR__ . '/../../Resources/xsd/maindoc/UBLPE-SummaryDocuments-1.0.xsd');
        $this->assertTrue($success);
    }

    public function testSummaryFilename()
    {
        $summary = $this->getSummary();
        $filename = $summary->getName();

        $this->assertEquals($this->getFilename($summary), $filename);
    }

    private function getFileName(Summary $summary)
    {
        $parts = [
            $summary->getCompany()->getRuc(),
            'RC',
            $summary->getFecResumen()->format('Ymd'),
            $summary->getCorrelativo(),
        ];

        return join('-', $parts);
    }
    private function getSummary()
    {
        $detiail1 = new SummaryDetail();
        $detiail1->setTipoDoc('07')
            ->setSerieNro('B001-12')
            ->setClienteTipo('1')
            ->setClienteNro('44556677')
            ->setPercepcion((new SummaryPerception())
                ->setCodReg('01')
                ->setTasa(2)
                ->setMtoBase(100)
                ->setMto(2)
                ->setMtoTotal(102))
            ->setEstado('1')
            ->setDocReferencia((new Document())
                ->setTipoDoc('03')
                ->setNroDoc('B001-1'))
            ->setTotal(100)
            ->setMtoOperGravadas(20.555)
            ->setMtoOperInafectas(24.4)
            ->setMtoOperExoneradas(50)
            ->setMtoOtrosTributos(12.32)
            ->setMtoIGV(3.6)
            ->setMtoISC(0);

        $detiail2 = new SummaryDetail();
        $detiail2->setTipoDoc('07')
            ->setSerieNro('B001-22')
            ->setClienteTipo('1')
            ->setClienteNro('55667733')
            ->setEstado('1')
            ->setTotal(200)
            ->setMtoOtrosCargos(1)
            ->setMtoOperGravadas(40)
            ->setMtoOperExoneradas(30)
            ->setMtoOperInafectas(120)
            ->setMtoOperGratuitas(10)
            ->setMtoIGV(7.2)
            ->setMtoISC(2.8);

        $sum = new Summary();
        $sum->setFecGeneracion(new \DateTime())
            ->setFecResumen(new \DateTime())
            ->setCompany($this->getCompany())
            ->setCorrelativo('001')
            ->setDetails([$detiail1, $detiail2]);

        return $sum;
    }
}