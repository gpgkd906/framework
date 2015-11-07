<?php
/** 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     MarketplaceWebServiceProducts
 *  @copyright   Copyright 2008-2013 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2011-10-01
 */
 
/******************************************************************************* 
 * 
 *  Marketplace Web Service Products PHP5 Library
 *  Generated: Wed Sep 25 16:54:47 GMT 2013
 * 
 */

/**
 *  @see MarketplaceWebServiceProducts_Model
 */

require_once (dirname(__FILE__) . '/../Model.php');


/**
 * MarketplaceWebServiceProducts_Model_GetCompetitivePricingForSKUResponse
 * 
 * Properties:
 * <ul>
 * 
 * <li>GetCompetitivePricingForSKUResult: array</li>
 * <li>ResponseMetadata: MarketplaceWebServiceProducts_Model_ResponseMetadata</li>
 * <li>ResponseHeaderMetadata: MarketplaceWebServiceProducts_Model_ResponseHeaderMetadata</li>
 *
 * </ul>
 */

 class MarketplaceWebServiceProducts_Model_GetCompetitivePricingForSKUResponse extends MarketplaceWebServiceProducts_Model {

    public function __construct($data = null)
    {
        $this->_fields = array (
            'GetCompetitivePricingForSKUResult' => array('FieldValue' => array(), 'FieldType' => array('MarketplaceWebServiceProducts_Model_GetCompetitivePricingForSKUResult')),
            'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'MarketplaceWebServiceProducts_Model_ResponseMetadata'),
            'ResponseHeaderMetadata' => array('FieldValue' => null, 'FieldType' => 'MarketplaceWebServiceProducts_Model_ResponseHeaderMetadata'),
        );
	    parent::__construct($data);
    }

    /**
     * Get the value of the GetCompetitivePricingForSKUResult property.
     *
     * @return List<GetCompetitivePricingForSKUResult> GetCompetitivePricingForSKUResult.
     */
    public function getGetCompetitivePricingForSKUResult()
	{
        if ($this->_fields['GetCompetitivePricingForSKUResult']['FieldValue'] == null)
		{
            $this->_fields['GetCompetitivePricingForSKUResult']['FieldValue'] = array();
        }
	    return $this->_fields['GetCompetitivePricingForSKUResult']['FieldValue'];
    }

    /**
     * Set the value of the GetCompetitivePricingForSKUResult property.
     *
     * @param array getCompetitivePricingForSKUResult
     * @return this instance
     */
    public function setGetCompetitivePricingForSKUResult($value)
	{
        if (!$this->_isNumericArray($value)) {
            $value = array ($value);
        }
	    $this->_fields['GetCompetitivePricingForSKUResult']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Clear GetCompetitivePricingForSKUResult.
     */
    public function unsetGetCompetitivePricingForSKUResult()
	{
        $this->_fields['GetCompetitivePricingForSKUResult']['FieldValue'] = array();
    }

    /**
     * Check to see if GetCompetitivePricingForSKUResult is set.
     *
     * @return true if GetCompetitivePricingForSKUResult is set.
     */
    public function isSetGetCompetitivePricingForSKUResult()
	{
	            return !empty($this->_fields['GetCompetitivePricingForSKUResult']['FieldValue']);
		    }

    /**
     * Add values for GetCompetitivePricingForSKUResult, return this.
     *
     * @param getCompetitivePricingForSKUResult
     *             New values to add.
     *
     * @return This instance.
     */
    public function withGetCompetitivePricingForSKUResult()
	{
        foreach (func_get_args() as $GetCompetitivePricingForSKUResult)
		{
            $this->_fields['GetCompetitivePricingForSKUResult']['FieldValue'][] = $GetCompetitivePricingForSKUResult;
        }
        return $this;
    }

    /**
     * Get the value of the ResponseMetadata property.
     *
     * @return ResponseMetadata ResponseMetadata.
     */
    public function getResponseMetadata()
	{
	    return $this->_fields['ResponseMetadata']['FieldValue'];
    }

    /**
     * Set the value of the ResponseMetadata property.
     *
     * @param MarketplaceWebServiceProducts_Model_ResponseMetadata responseMetadata
     * @return this instance
     */
    public function setResponseMetadata($value)
	{
	    $this->_fields['ResponseMetadata']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Check to see if ResponseMetadata is set.
     *
     * @return true if ResponseMetadata is set.
     */
    public function isSetResponseMetadata()
	{
	            return !is_null($this->_fields['ResponseMetadata']['FieldValue']);
		    }

    /**
     * Set the value of ResponseMetadata, return this.
     *
     * @param responseMetadata
     *             The new value to set.
     *
     * @return This instance.
     */
    public function withResponseMetadata($value)
	{
        $this->setResponseMetadata($value);
        return $this;
    }

    /**
     * Get the value of the ResponseHeaderMetadata property.
     *
     * @return ResponseHeaderMetadata ResponseHeaderMetadata.
     */
    public function getResponseHeaderMetadata()
	{
	    return $this->_fields['ResponseHeaderMetadata']['FieldValue'];
    }

    /**
     * Set the value of the ResponseHeaderMetadata property.
     *
     * @param MarketplaceWebServiceProducts_Model_ResponseHeaderMetadata responseHeaderMetadata
     * @return this instance
     */
    public function setResponseHeaderMetadata($value)
	{
	    $this->_fields['ResponseHeaderMetadata']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Check to see if ResponseHeaderMetadata is set.
     *
     * @return true if ResponseHeaderMetadata is set.
     */
    public function isSetResponseHeaderMetadata()
	{
	            return !is_null($this->_fields['ResponseHeaderMetadata']['FieldValue']);
		    }

    /**
     * Set the value of ResponseHeaderMetadata, return this.
     *
     * @param responseHeaderMetadata
     *             The new value to set.
     *
     * @return This instance.
     */
    public function withResponseHeaderMetadata($value)
	{
        $this->setResponseHeaderMetadata($value);
        return $this;
    }
    /**
     * Construct MarketplaceWebServiceProducts_Model_GetCompetitivePricingForSKUResponse from XML string
     * 
     * @param $xml
     *        XML string to construct from
     *
     * @return MarketplaceWebServiceProducts_Model_GetCompetitivePricingForSKUResponse 
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $response = $xpath->query("//*[local-name()='GetCompetitivePricingForSKUResponse']");
        if ($response->length == 1) {
            return new MarketplaceWebServiceProducts_Model_GetCompetitivePricingForSKUResponse(($response->item(0))); 
        } else {
            throw new Exception ("Unable to construct MarketplaceWebServiceProducts_Model_GetCompetitivePricingForSKUResponse from provided XML. 
                                  Make sure that GetCompetitivePricingForSKUResponse is a root element");
        }
    }
    /**
     * XML Representation for this object
     * 
     * @return string XML for this object
     */
    public function toXML() 
    {
        $xml = "";
        $xml .= "<GetCompetitivePricingForSKUResponse xmlns=\"http://mws.amazonservices.com/schema/Products/2011-10-01\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</GetCompetitivePricingForSKUResponse>";
        return $xml;
    }

}
