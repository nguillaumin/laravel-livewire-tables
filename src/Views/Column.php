<?php

namespace Rappasoft\LaravelLivewireTables\Views;

use Illuminate\Support\Str;

/**
 * Class Column.
 */
class Column
{
    /**
     * @var string|null
     */
    public ?string $column = null;

    /**
     * @var string|null
     */
    public ?string $text = null;

    /**
     * @var bool
     */
    public bool $sortable = false;

    /**
     * @var string|null
     */
    public ?string $class = null;

    /**
     * @var bool
     */
    public bool $blank = false;

    /**
     * @var
     */
    public $formatCallback;

    /**
     * @var bool
     */
    public bool $asHtml = false;

    /**
     * Column constructor.
     *
     * @param string|null $column
     * @param string|null $text
     */
    public function __construct(string $text = null, string $column = null)
    {
        $this->text = $text;

        if (! $column && $text) {
            $this->column = Str::snake($text);
        } else {
            $this->column = $column;
        }

        if (! $this->column && ! $this->text) {
            $this->blank = true;
        }
    }

    /**
     * @param string|null $column
     * @param string|null $text
     *
     * @return Column
     */
    public static function make(string $text = null, string $column = null): Column
    {
        return new static($text, $column);
    }

    /**
     * @return Column
     */
    public static function blank(): Column
    {
        return new static(null, null);
    }

    /**
     * @return bool
     */
    public function isSortable(): bool
    {
        return $this->sortable === true;
    }

    /**
     * @return bool
     */
    public function isBlank(): bool
    {
        return $this->blank === true;
    }

    /**
     * @return $this
     */
    public function sortable(): self
    {
        $this->sortable = true;

        return $this;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function addClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return Column
     */
    public function asHtml(): Column
    {
        $this->asHtml = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function class(): ?string
    {
        return $this->class;
    }

    /**
     * @return string|null
     */
    public function column(): ?string
    {
        return $this->column;
    }

    /**
     * @return string|null
     */
    public function text(): ?string
    {
        return $this->text;
    }

    /**
     * @param  callable  $callable
     *
     * @return $this
     */
    public function format(callable $callable): Column
    {
        $this->formatCallback = $callable;

        return $this;
    }

    /**
     * @param $row
     * @param  null  $column
     *
     * @return array|mixed|null
     */
    public function formatted($row, $column = null)
    {
        if ($column instanceof self) {
            $columnName = $column->column();
        } elseif (is_string($column)) {
            $columnName = $column;
        } else {
            $columnName = $this->column();
        }

        $value = data_get($row, $columnName);

        if ($this->formatCallback) {
            return app()->call($this->formatCallback, ['value' => $value, 'column' => $column, 'row' => $row]);
        }

        return $value;
    }
}
