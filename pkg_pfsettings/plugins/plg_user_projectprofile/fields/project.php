<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('radio');

/**
 * Provides input for TOS
 *
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 * @since       2.5.5
 */
class JFormFieldProject extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  2.5.5
	 */
	protected $type = 'Project';

	public function getOptions() 
	{
		$id = (int)$this->form->getValue('id');
		if (!empty($id)) {
			$user = JFactory::getUser($id);
		}else{
			return array();
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id as value, title as text')
			->from('#__pf_projects')
			->where('state = 1');
		
	        // Implement View Level Access.
        if (!$user->authorise('core.admin')) {
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $query->where('access IN (' . $groups . ')');
        }			
		$db->setQuery($query);
		
		$items =  $db->loadObjectList();
		
		foreach($items AS $item)
        {
            // Create a new option object based on the <option /> element.
            $opt = JHtml::_('select.option', (string) $item->value,
                JText::alt(trim((string) $item->text),
                preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)),
                'value',
                'text'
            );

            // Add the option object to the result set.
            $options[] = $opt;
        }

        reset($options);

        return $options;
	}	
}
