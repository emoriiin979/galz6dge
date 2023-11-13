<?php

namespace App\Libs;

use SimpleXmlElement;

class HatenaXml
{
    /**
     * XML content.
     *
     * @var SimpleXmlElement
     */
    protected $xml;

    /**
     * Constructor.
     *
     * @param string $data
     */
    public function __construct($data = '')
    {
        $this->xml = $this->parseXml($data);
    }

    /**
     * 文字列をXMLオブジェクトに変換
     *
     * @param string $data
     * @return SimpleXmlElement
     */
    protected function parseXml($data)
    {
        if (empty($data)) {
            $data = <<<EOT
            <?xml version="1.0" encoding="utf-8"?>
            <entry xmlns="http://www.w3.org/2005/Atom"
                xmlns:app="http://www.w3.org/2007/app">
            </entry>
            EOT;
        }

        return new SimpleXmlElement($data);
    }

    /**
     * 登録用XMLを取得
     *
     * @return string 
     */
    public function getEntryXml()
    {
        return strval($this->xml->asXML());
    }
}
