<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Nosto
 * @package     Nosto_Tagging
 * @copyright   Copyright (c) 2013-2015 Nosto Solutions Ltd (http://www.nosto.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class for common operations.
 *
 * @category    Nosto
 * @package     Nosto_Tagging
 * @author      Nosto Solutions Ltd
 */
class Nosto_Tagging_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_INSTALLATION_ID = 'nosto_tagging/installation/id';

    /**
     * Builds a tagging string of the given category including all its parent categories.
     * The categories are sorted by their position in the category tree path.
     *
     * @param Mage_Catalog_Model_Category $category
     *
     * @return string
     */
    public function buildCategoryString($category)
    {
        $data = array();

        if ($category instanceof Mage_Catalog_Model_Category) {
            /** @var $categories Mage_Catalog_Model_Category[] */
            $categories = $category->getParentCategories();
            $path = $category->getPathInStore();
            $ids = array_reverse(explode(',', $path));
            foreach ($ids as $id) {
                if (isset($categories[$id]) && $categories[$id]->getName()) {
                    $data[] = $categories[$id]->getName();
                }
            }
        }
        if (!empty($data)) {
            return DS . implode(DS, $data);
        } else {
            return '';
        }
    }

    /**
     * Formats date into Nosto format, i.e. Y-m-d.
     *
     * @param string $date
     *
     * @return string
     */
    public function getFormattedDate($date)
    {
        return date('Y-m-d', strtotime($date));
    }

	/**
	 * Returns a unique ID that identifies this Magento installation.
	 * This ID is sent to the Nosto account config iframe and used to link all Nosto accounts used on this installation.
	 *
	 * @return string the ID.
	 */
	public function getInstallationId()
	{
		$installationId = Mage::getStoreConfig(self::XML_PATH_INSTALLATION_ID);
		if (empty($installationId)) {
			// Running bin2hex() will make the ID string length 64 characters.
			$installationId = bin2hex(NostoCryptRandom::getRandomString(32));
			/** @var Mage_Core_Model_Config $config */
			$config = Mage::getModel('core/config');
			$config->saveConfig(self::XML_PATH_INSTALLATION_ID, $installationId, 'default', 0);
			Mage::app()->getCacheInstance()->cleanType('config');
		}
		return $installationId;
	}
}
