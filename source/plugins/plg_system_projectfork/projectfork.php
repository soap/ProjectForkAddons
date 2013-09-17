<?php
defined('_JEXEC') or die;

/**
 * ProjectFork System Setting
 *
 * @package		ProjectFork
 * @subpackage	plg_system_projectfork
 */
class plgSystemProjectFork extends JPlugin
{
	function onAfterRoute()
	{
		jimport('projectfork.framework');
		jimport('projectfork.application.helper');
		
		$app = JFactory::getApplication();

		// Nothing for admin
		if ($app->isAdmin()) {
			return true;
		}

		$jinput = JFactory::getApplication()->input;
		$component = $jinput->get('option', null, 'cmd');
		if (empty($component)) {
			return true;
		}
		
		$user = JFactory::getUser(); 
		$userId = $user->get('id');
		
		$authorised_groups = $user->getAuthorisedGroups();
		
		// Check user group
        $restrict_usergroups = (int)$this->params->get('restrict_usergroups', 0);
	    
        if(!empty($restrict_usergroups))
        {
            $restricted_usergroups = array_map('intval', (array)$this->params->get('restricted_usergroups'));
			if (!count(array_intersect($restricted_usergroups, $authorised_groups))) {
				return true;
			}
        }
        
		if (PFApplicationHelper::exists($component)) {
			if (!PFApplicationHelper::getActiveProjectId() && $user->get('id')) {
				$db = JFactory::getDbo();
				$db->setQuery(
					'SELECT profile_value FROM #__user_profiles' .
					' WHERE user_id = '.(int) $userId." AND profile_key = ".$db->quote('profile.default_project_id') .
					' ORDER BY ordering'
				);
				
        		$default_project_id = $db->loadObjectList();
        		if ($default_project_id)
					PFApplicationHelper::setActiveProject($default_project_id);	
			}	
		}
	}

}