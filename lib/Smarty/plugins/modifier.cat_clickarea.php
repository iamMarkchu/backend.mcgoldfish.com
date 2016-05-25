<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty cat clickarea for tracking modifier plugin
 *
 * Type:     modifier<br>
 * Name:     cat_clickarea<br>
 * Purpose:  urlencode url 
 * @link 
 * @author   JavionZheng
 * @param string
 * @param blockName
 * @param rank
 * @param clickarea
 * @return string
 */
function smarty_modifier_cat_clickarea($url, $pageName, $pageValue, $blockName, $rank, $clickarea)
{
// 	return $url.'#####'.$pageName.'#####'.$pageValue.'#####'.$blockName.'#####'.$rank.'#####'.$clickarea;
    //return urlencode($url .'|'.$pageName .'|'.$pageValue .'|'. base64_encode( "{$blockName}_{$rank}_{$clickarea}" ));
    return urlencode(urlencode($url) .'|'.base64_encode($pageName) .'|'. base64_encode($pageValue) .'|'. base64_encode( "{$blockName}_{$rank}_{$clickarea}" ));
}

?>
