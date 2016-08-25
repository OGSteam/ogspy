<?php
/**
 * Created by PhpStorm.
 * User: Itori
 * Date: 19/08/2016
 * Time: 20:25
 */

namespace Ogsteam\Ogspy\Views;


class ViewHelper
{
    /**
     * @param string $selectedValue valeur sélectionnée
     * @param string $value string Valeur de l'option
     * @param string $text Texte à afficher
     * @return string Tag Html correspondant à l'option, avec prise en compte de la sélection
     */
    public static function get_option($selectedValue, $value, $text)
    {
        $return = '<option value="' . $value . '" ';
        if($value == $selectedValue)
            $return .= 'selected="selected"';
        $return .= '>' . $text . '</option>\n';

        return $return;
    }
}