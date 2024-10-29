<?php

namespace AlgolWishlist\Settings\SettingsFramework\Varieties\SelectiveOption;

use AlgolWishlist\Settings\SettingsFramework\Abstracts\OriginOption;
use AlgolWishlist\Settings\SettingsFramework\Exceptions\OptionValueFilterFailed;
use AlgolWishlist\Settings\SettingsFramework\Varieties\SelectiveOption\Interfaces\SelectiveOptionInterface;


class SelectiveOptionWithCallback extends OriginOption implements SelectiveOptionInterface
{
    /**
     * @var OptionSelection[]|null
     */
    protected $selections = null;

    /**
     * @var callable|null
     */
    protected $callback = null;

    /**
     * SelectiveOption constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        parent::__construct($id);
    }

    /**
     * @param callable $callback
     *
     * @return bool
     */
    public function addSelectionCallback(callable $callback): bool
    {
        $this->callback = $callback;

        return true;
    }

    protected function loadSelections()
    {
        $this->selections = [];

        if ($this->callback) {
            $maybeOptions = ($this->callback)();
            $maybeOptions = is_array($maybeOptions) ? $maybeOptions : [];
            foreach ($maybeOptions as $value => $title) {
                $this->selections[] = new OptionSelection($value, $title);
            }
        }

        if ( count($this->selections) > 0 ) {
            $this->default = reset($this->selections)->getValue();
        }
    }

    /**
     * @return OptionSelection[]
     */
    public function getSelections(): array
    {
        if ($this->selections === null) {
            $this->loadSelections();
        }

        return $this->selections;
    }
}
