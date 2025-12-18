<?php
class OSBlocks {
	private const disallowedSpecialPages = ['BlockList', 'AutoblockList'];
	
	public static function onGetUserPermissionsErrors( $title, $user, $action, &$out_result ) {
		if (self::isDisallowedSpecialPage($title) && !$user->isAllowed('block')) {
			$out_result = [
				'badaccess'
			];
			
			return false;
		}
		
		return true;
	}
	
	public static function onApiCheckCanExecute($module, $user, &$message) {
		if (!$user->isAllowed('block') && self::isBlockListApi($module) || self::isBlockInfoApi($module)) {
			$message = 'badaccess';
		
			return false;
		}
		
		return true;
	}
	
	private static function isBlockListApi($module) {
		$params = $module->extractRequestParams();
				
		return isset($params['list']) && in_array('blocks', $params['list']);
	}

	private static function isBlockInfoApi($module) {
		$params = $module->extractRequestParams();
				
		return isset($params['usprop']) && in_array('blockinfo', $params['usprop']);
	}
	
	private static function isDisallowedSpecialPage($title) {
		return !empty(
			array_filter(
				self::disallowedSpecialPages, 
				function ($disallowedTitle) use($title) {
					return $title->isSpecial($disallowedTitle); 
				}
			)
		);
	}
}
