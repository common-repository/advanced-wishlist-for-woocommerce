<?php

namespace AlgolWishlist\Settings\SettingsFramework;

use AlgolWishlist\Settings\SettingsFramework\Exceptions\OptionValueFilterFailed;
use AlgolWishlist\Settings\SettingsFramework\Varieties\Option\BooleanOption;
use AlgolWishlist\Settings\SettingsFramework\Varieties\Option\IntegerNumberOption;
use AlgolWishlist\Settings\SettingsFramework\Varieties\Option\ShortTextOption;
use AlgolWishlist\Settings\SettingsFramework\Varieties\SelectiveOption\SelectiveOption;
use AlgolWishlist\Settings\SettingsFramework\Varieties\SelectiveOption\SelectiveOptionWithCallback;

class OptionBuilder
{
    /**
     * @param string $id
     * @param string $default
     * @param null $title
     *
     * @return ShortTextOption|null
     */
    public static function shortText($id, $default = "", $title = null, $customizeUrl = null)
    {
        if ( ! $id) {
            return null;
        }

        $option = new ShortTextOption($id);
        $option->setDefault($default);
        $option->setTitle($title);
        $option->setCustomizeUrl($customizeUrl);

        return $option;
    }

    /**
     * @param string $id
     * @param string $default
     * @param null $title
     *
     * @return BooleanOption|null
     */
    public static function boolean($id, $default = "", $title = null, $customizeUrl = null)
    {
        if ( ! $id) {
            return null;
        }

        $option = new BooleanOption($id);
        $option->setDefault($default);
        $option->setTitle($title);

        return $option;
    }

    /**
     * @param string $id
     * @param string $default
     * @param null $title
     *
     * @return IntegerNumberOption|null
     */
    public static function integer($id, $default = "", $title = null, $customizeUrl = null)
    {
        if ( ! $id) {
            return null;
        }

        $option = new IntegerNumberOption($id);
        $option->setDefault($default);
        $option->setTitle($title);

        return $option;
    }

    /**
     * @param string $id
     * @param string $default
     * @param null $title
     *
     * @return ShortTextOption|null
     */
    public static function htmlText($id, $default = "", $title = null, $customizeUrl = null)
    {
        if ( ! $id) {
            return null;
        }

        $option = new ShortTextOption($id);
        $option->setSanitizeCallback(function ($value) {
            $value = stripslashes($value);
            $value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if ($value === false) {
                throw new OptionValueFilterFailed();
            }

            return (string)$value;
        });

        $option->setDefault($default);
        $option->setTitle($title);

        return $option;
    }

    /**
     * @param string $id
     * @param string $title
     * @param mixed[] $selections
     * @param mixed $default
     *
     * @return SelectiveOption
     */
    public static function selective($id, $title, $selections, $default = null, $customizeUrl = null)
    {
        if ( ! $id || ! $title || ! $selections) {
            return null;
        }

        $option = new SelectiveOption($id);
        $option->setTitle($title);

        foreach ($selections as $value => $selectionTitle) {
            $option->addSelection($value, $selectionTitle);
        }

        if (isset($default)) {
            $option->setDefault($default);
        }

        return $option;
    }

    /**
     * @param string $id
     * @param string $title
     * @param callable $selectionsCallback
     * @param mixed $default
     *
     * @return SelectiveOptionWithCallback
     */
    public static function selectiveWithCallback(
        $id,
        $title,
        $selectionsCallback,
        $default = null,
        $customizeUrl = null
    ) {
        if (!$id || !$title || !$selectionsCallback) {
            return null;
        }

        $option = new SelectiveOptionWithCallback($id);
        $option->setTitle($title);
        $option->addSelectionCallback($selectionsCallback);

        if (isset($default)) {
            $option->setDefault($default);
        }

        return $option;
    }

}
