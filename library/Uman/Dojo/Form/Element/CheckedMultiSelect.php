<?php
require_once 'Zend/Dojo/Form/Element/ComboBox.php';

class Uman_Dojo_Form_Element_CheckedMultiSelect extends Zend_Dojo_Form_Element_Dijit
{

    protected $_isArray = true;

    public $helper = 'CheckedMultiSelect';

//    protected $_registerInArrayValidator = true;

    public function getStoreInfo()
    {
        if (!$this->hasDijitParam('store')) {
            $this->dijitParams['store'] = array();
        }
        return $this->dijitParams['store'];
    }

    public function setStoreId($identifier)
    {
        $store = $this->getStoreInfo();
        $store['store'] = (string) $identifier;
        $this->setDijitParam('store', $store);
        return $this;
    }

    /**
     * Get datastore identifier
     *
     * @return string|null
     */
    public function getStoreId()
    {
        $store = $this->getStoreInfo();
        if (array_key_exists('store', $store)) {
            return $store['store'];
        }
        return null;
    }

    /**
     * Set datastore dijit type
     *
     * @param  string $dojoType
     * @return Zend_Dojo_Form_Element_ComboBox
     */
    public function setStoreType($dojoType)
    {
        $store = $this->getStoreInfo();
        $store['type'] = (string) $dojoType;
        $this->setDijitParam('store', $store);
        return $this;
    }

    /**
     * Get datastore dijit type
     *
     * @return string|null
     */
    public function getStoreType()
    {
        $store = $this->getStoreInfo();
        if (array_key_exists('type', $store)) {
            return $store['type'];
        }
        return null;
    }

    /**
     * Set datastore parameters
     *
     * @param  array $params
     * @return Zend_Dojo_Form_Element_ComboBox
     */
    public function setStoreParams(array $params)
    {
        $store = $this->getStoreInfo();
        $store['params'] = $params;
        $this->setDijitParam('store', $store);
        return $this;
    }

    /**
     * Get datastore params
     *
     * @return array
     */
    public function getStoreParams()
    {
        $store = $this->getStoreInfo();
        if (array_key_exists('params', $store)) {
            return $store['params'];
        }
        return array();
    }

    public function isValid($value, $context = null)
    {
/*        $storeInfo = $this->getStoreInfo();
        if (!empty($storeInfo)) {
            $this->setRegisterInArrayValidator(false);
        }
*/        return parent::isValid($value, $context);
    }

}
