<?php
use NGS\Converter\XmlConverter;

/**
 * Test constructors
 */
class XmlTest extends PHPUnit_Framework_TestCase
{
    public static function providerInvalid()
    {
        return array(
            array(
                '<some invalid xml<<<'
            )
        );
    }

    public static function providerValid()
    {
        $xmls = array(
            array(
                '<ChildrenWithSameName>'.
                    '<Param key="name">Mirko</Param>'.
                    '<Param key="phone">123</Param>'.
                '</ChildrenWithSameName>'
            ),
            array(
                '<ChildrenWithSameName>'.
                    '<Param>'.
                        '<Param>1</Param>'.
                        '<Param>1</Param>'.
                        '<Param>2</Param>'.
                    '</Param>'.
                    '<Param key="phone">123</Param>'.
                '</ChildrenWithSameName>'
            ),
            array(
                "<singleRoot>text</singleRoot>",
            ),
            array(
                "<singleEmptyRoot></singleEmptyRoot>",
                "<singleEmptyRoot/>",
            ),
        );
        foreach($xmls as &$val) {
            $val[0] = "<?xml version=\"1.0\"?>\n".$val[0]."\n";
            if(!isset($val[1]))
                $val[1] = $val[0];
            else
                $val[1] =  "<?xml version=\"1.0\"?>\n".$val[1]."\n";
        }
        return $xmls;
    }

    // Fixes bug produced by convrertin json xml array:
    // SimpleXMLElement::addAttribute(): Attribute already exists
    public function testArrayConversionsChildrenWithSameName()
    {
        // $xml = XmlConverter::toXml($source);

        $jsonXmlArray = array(
            'User' => array(
                'Param' => array(
                    array(
                        '@key'  => 'name',
                        '#text' => 'Mirko'
                    ),
                    array(
                        '@key'  => 'phone',
                        '#text' => '123'
                    ),
                )
            )
        );

        $xmlElem = XmlConverter::toXml($jsonXmlArray);

        $expectedXml = "<?xml version=\"1.0\"?>\n<User><Param key=\"name\">Mirko</Param><Param key=\"phone\">123</Param></User>\n";

        $this->assertSame($expectedXml, $xmlElem->asXml());

        $this->assertSame($jsonXmlArray, XmlConverter::toArray($xmlElem));
    }

    /**
     * @dataProvider providerValid
     */
    public function testToFromArray($source, $expected)
    {
        $xml = XmlConverter::toXml($source);

        $this->assertSame($expected, $xml->asXml());

        $arr = XmlConverter::toArray($xml);

        $xmlFromArr = XmlConverter::toXml($arr);

        $this->assertSame($expected, $xmlFromArr->asXml());
    }
}
