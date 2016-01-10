<?php

require_once 'Zend/View/Helper/FormElement.php';

class Uman_Dojo_View_Helper_CheckedMultiSelect extends Zend_Dojo_View_Helper_Dijit
{
    protected $_dijit  = 'uman.form.CheckedMultiSelect';
    protected $_module = 'uman.form.CheckedMultiSelect';

//    protected $_dijit  = 'dojox.form.CheckedMultiSelect';
//    protected $_module = 'dojox.form.CheckedMultiSelect';

    protected $_isArray = true;

/*    public function checkedMultiSelect($id, $value = null, array $params = array(), array $attribs = array(), array $options = null)
    {
        return $this->comboBox($id, $value, $params, $attribs, $options);
    }
*/
    public function checkedMultiSelect($id, $value = null, array $params = array(), array $attribs = array(), array $options = null)
    {
      $html = '';

      if (substr($id, -2) != '[]')
			  $id = $id . '[]';

			if (!array_key_exists('id', $attribs)) {
            $attribs['id'] = $id;
        }
        if (array_key_exists('store', $params) && is_array($params['store'])) {
            // using dojo.data datastore
            if (false !== ($store = $this->_renderStore($params['store']))) {
                $params['store'] = $params['store']['store'];
                if (is_string($store)) {
                    $html .= $store;
                }
                $html .= $this->_createFormElement($id, $value, $params, $attribs);
                return $html;
            }
            unset($params['store']);
        } elseif (array_key_exists('store', $params)) {
            if (array_key_exists('storeType', $params)) {
                $storeParams = array(
                    'store' => $params['store'],
                    'type'  => $params['storeType'],
                );
                unset($params['storeType']);
                if (array_key_exists('storeParams', $params)) {
                    $storeParams['params'] = $params['storeParams'];
                    unset($params['storeParams']);
                }
                if (false !== ($store = $this->_renderStore($storeParams))) {
                    if (is_string($store)) {
                        $html .= $store;
                    }
                }
            }
            $html .= $this->_createFormElement($id, $value, $params, $attribs);
            return $html;
        }

        // do as normal select
        $attribs = $this->_prepareDijit($attribs, $params, 'element');
        return $this->_createFormElement($id, $value, $params, $attribs);
//        return $this->view->formSelect($id, $value, $attribs, $options);
    }

    /**
     * Render data store element
     *
     * Renders to dojo view helper
     *
     * @param  array $params
     * @return string|false
     */
    protected function _renderStore(array $params)
    {
        if (!array_key_exists('store', $params) || !array_key_exists('type', $params)) {
            return false;
        }

        $this->dojo->requireModule($params['type']);

        $extraParams = array();
        $storeParams = array(
            'dojoType' => $params['type'],
            'jsId'     => $params['store'],
        );

        if (array_key_exists('params', $params)) {
            $storeParams = array_merge($storeParams, $params['params']);
            $extraParams = $params['params'];
        }

        if ($this->_useProgrammatic()) {
            if (!$this->_useProgrammaticNoScript()) {
                $this->dojo->addJavascript('var ' . $storeParams['jsId'] . ';');
                require_once 'Zend/Json.php';
                $js = "function() {\n"
                    . '    ' . $storeParams['jsId'] . ' = '
                    . 'new ' . $storeParams['dojoType'] . '('
                    .         Zend_Json::encode($extraParams)
                    . ");\n}";
                $this->dojo->addOnLoad($js);
            }
            return true;
        }

        return '<div' . $this->_htmlAttribs($storeParams) . '></div>';
    }

}