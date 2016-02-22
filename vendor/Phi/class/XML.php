<?php

namespace Phi;

class XML extends \SimpleXMLElement
{


    public function removeChild(\SimpleXMLElement $old) {
        $tmp = dom_import_simplexml($this);
        $old = dom_import_simplexml($old);
        if ($old->ownerDocument !== $tmp->ownerDocument) {
            throw new DOMException('The reference node does not come from the same document as the context node', DOM_WRONG_DOCUMENT_ERR);
        }

        $node = $tmp->removeChild($old);
        return simplexml_import_dom($node, get_class($this));
    }


    public function createElement($name, $content) {
        $dom = dom_import_simplexml($this);
        $node=$dom->ownerDocument->createElement($name, $content);
        return simplexml_import_dom($node);

    }



    public function append($node) {


        $dom=dom_import_simplexml($this);
        $node=dom_import_simplexml($node);
        $node=$dom->ownerDocument->importNode($node, true);
        $dom->appendChild($node);
    }

    public function prepend($node) {
        $dom = dom_import_simplexml($this);
        $node=dom_import_simplexml($node);
        $node=$dom->ownerDocument->importNode($node, true);

        $new = $dom->insertBefore(
            $node,
            $dom->firstChild
        );

        return simplexml_import_dom($new, get_class($this));
    }


    public function replace(SimpleXMLElement $element) {
        $dom     = dom_import_simplexml($this);
        $import  = $dom->ownerDocument->importNode(
            dom_import_simplexml($element),
            true
        );
        $dom->parentNode->replaceChild($import, $dom);
    }



    public function cloneNode($node) {
        $dom_thing = dom_import_simplexml($this);
        $dom_node  = dom_import_simplexml($node);
        $dom_new   = $dom_thing->appendChild($dom_node->cloneNode(true));
        $new_node  = simplexml_import_dom($dom_new);
        return simplexml_import_dom($node, get_class($this));
    }







    public function setValue($value) {
        $dom=dom_import_simplexml($this);

        $dom->nodeValue=htmlspecialchars($value);
        //$simplexml=simplexml_import_dom($dom, get_class($this));
        //return $simplexml;
    }


    public function asHTML() {
        $node=dom_import_simplexml($this);
        $document=new \DomDocument();
        $document->loadXML('<?xml version="1.0" encoding="utf-8"?><PREMIUM_container></PREMIUM_container>');


        $newNode=$document->importNode($node, true);
        $document->documentElement->appendChild($newNode);


        $buffer=$document->saveHTML();

        //pour gérer bug php qui ne ferme pas les balise link
        $buffer=preg_replace('`(<link.*?)>`', '$1/>', $buffer);

        return preg_replace('`</?PREMIUM_container>`','', $buffer);
    }

    /**
     * @param $xml string Your XML
     * @param $old string Name of the old tag
     * @param $new string Name of the new tag
     * @return string New XML
     */
    function renameTags($old, $new)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($this->asXML());

        $nodes = $dom->getElementsByTagName($old);
        $toRemove = array();
        foreach ($nodes as $node)
        {
            $newNode = $dom->createElement($new);
            foreach ($node->attributes as $attribute)
            {
                $newNode->setAttribute($attribute->name, $attribute->value);
            }

            foreach ($node->childNodes as $child)
            {
                $newNode->appendChild($node->removeChild($child));
            }

            $node->parentNode->appendChild($newNode);
            $toRemove[] = $node;
        }

        foreach ($toRemove as $node)
        {
            $node->parentNode->removeChild($node);
        }


        //return simplexml_import_dom($dom);

        $xml=$dom->saveXML();
        return $xml;


        $this->__construct($xml);

        /*
        return simplexml_import_dom($node, get_class($this));
        echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
        echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
        print_r($this->asXML());
        echo '</pre>';
        die('EXIT '.__FILE__.'@'.__LINE__);
        */


    }






}

