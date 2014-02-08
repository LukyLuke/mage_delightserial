<?php
/**
 * DelightSerial Customisation by delight software gmbh for Magento
 *
 * DISCLAIMER
 *
 * Do not edit or add code to this file if you wish to upgrade this Module to newer
 * versions in the future.
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @copyright  Copyright (c) 2001-2011 delight software gmbh (http://www.delightsoftware.com/)
 */

/**
 * Adminhtml Serialnumber Groups Form-Container
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Block_Serialgroups_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

	public function __construct() {
		parent::__construct();
		$this->_blockGroup = 'delightserial';
		$this->_controller = 'serialgroups';
		$this->_mode = 'edit';
		$model = Mage::registry('current_group');
		$this->_removeButton('reset');
		$this->_updateButton('save', 'label', $this->__('Save Group'));
		$this->_updateButton('save', 'id', 'save_button');
		$this->_updateButton('delete', 'label', $this->__('Delete Group'));
		if (!$model->getId()) {
			$this->_removeButton('delete');
		}

		$this->_formInitScripts[] = '
            var itemType = function() {
                return {
                    updateAttributes: function() {
                        if ($("select_attribute_set").value != "" && $("select_itemtype").value != "")
                        {
                            var blocksCount = Element.select($("attributes_details"), "div[id^=gbase_attribute_]").length;
                            if (blocksCount > 0 && confirm("' . $this->__('Current Mapping will be reloaded. Continue?') . '") || blocksCount == 0)
                            {
                                var elements = [$("select_attribute_set"),$("select_itemtype")].flatten();
                                 $(\'save_button\').disabled = true;
                                new Ajax.Updater("attributes_details", "' . $this->getUrl('*/*/loadAttributes') . '", {parameters:Form.serializeElements(elements), evalScripts:true,  onComplete:function(){ $(\'save_button\').disabled = false; } });
                            }
                        }
                    }
                }
            }();

             Event.observe(window, \'load\', function(){
             	if ($("select_attribute_set")) {
             		Event.observe($("select_attribute_set"), \'change\', itemType.updateAttributes);
             	}
             	if ($("select_itemtype")) {
             		Event.observe($("select_itemtype"), \'change\', itemType.updateAttributes);
             	}
           });
        ';
	}

	public function getHeaderText() {
		if (!is_null(Mage::registry('current_group')->getId())) {
			return $this->__('Edit Group "%s"', $this->htmlEscape(Mage::registry('current_group')->getGroupName()));
		} else {
			return $this->__('New Serial-Group');
		}
	}

	public function getHeaderCssClass() {
		return 'icon-head head-customer-groups';
	}

}
