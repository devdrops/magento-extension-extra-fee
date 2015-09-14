<?php
/**
 * Magento Extra Fee Extension
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright (c) 2015 by Yaroslav Voronoy (y.voronoy@gmail.com)
 * @license   http://www.gnu.org/licenses/
 */

class Voronoy_ExtraFee_Model_Observer
{
    /**
     * Process Sales Rule Model Before Save
     *
     * @param $observer
     */
    public function beforeSaveSalesRuleModel($observer)
    {
        if (Mage::app()->getRequest()->isPost()) {
            $postData = Mage::app()->getRequest()->getPost();
            if (isset($postData['extra_fee_amount'])) {
                $salesRuleModel = $observer->getEvent()->getDataObject();
                $salesRuleModel->setExtraFeeAmount($postData['extra_fee_amount']);
            }
        }
    }

    /**
     * Prepare Form for Sales Rule
     *
     * @param $observer
     */
    public function prepareFormSalesRuleEdit($observer)
    {
        $model = Mage::registry('current_promo_quote_rule');
        if (!$model) {
            return $this;
        }
        /** @var Varien_Data_Form $form */
        $form = $observer->getEvent()->getForm();
        $fieldset = $form->getElement('action_fieldset');
        $fieldset->addField('extra_fee_amount', 'text', array(
            'name' => 'extra_fee_amount',
            'class' => 'validate-not-negative-number',
            'label' => Mage::helper('salesrule')->__('Extra Fee Amount'),
        ), 'discount_amount');
        $model->setExtraFeeAmount($model->getExtraFeeAmount()*1);

        Mage::app()->getLayout()->getBlock('promo_quote_edit_tab_actions')
            ->setChild('form_after', Mage::app()->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap('rule_extra_fee_amount', 'extra_fee_amount')
            ->addFieldMap('rule_simple_action', 'simple_action')
            ->addFieldDependence('extra_fee_amount', 'simple_action', array('by_percent', 'by_fixed'))
        );
    }
}