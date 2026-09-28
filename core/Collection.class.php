<?php
namespace Core;

use ArrayObject;
Use Closure;

/**
 * public __construct(array|object $array = [], int $flags = 0, string $iteratorClass = ArrayIterator::class)
 * public append(mixed $value): void
 * public asort(int $flags = SORT_REGULAR): true
 * public count(): int
 * public exchangeArray(array|object $array): array
 * public getArrayCopy(): array
 * public getFlags(): int
 * public getIterator(): Iterator
 * public getIteratorClass(): string
 * public ksort(int $flags = SORT_REGULAR): true
 * public natcasesort(): true
 * public natsort(): true
 * public offsetExists(mixed $key): bool
 * public offsetGet(mixed $key): mixed
 * public offsetSet(mixed $key, mixed $value): void
 * public offsetUnset(mixed $key): void
 * public serialize(): string
 * public setFlags(int $flags): void
 * public setIteratorClass(string $iteratorClass): void
 * public uasort(callable $callback): true
 * public uksort(callable $callback): true
 * public unserialize(string $data): void
 */

final class Collection extends ArrayObject {
    /**
     * Array Items
     *
     * @version 1.0
     */
    private $items = [];
    /**
     * Create collection
     *
     * @version 1.0
     * @param mixed $items
     */
    public function __construct($items){
        parent::__construct($items);
        $this->items = $items;
    }
    /**
     * Collect Statically
     *
     * @version 1.0
     * @param array $items
     * @return void
     */
    public static function with($items){
        return new self($items);
    }
    /**
     * Return Items
     *
     * @version 1.0
     * @return void
     */
    public function all(){
        return $this->items;
    }
    /**
     * Limit Array Range
     *
     * @version 1.0
     * @param int $limit
     * @return void
     */
    public function limit(int $limit){
        return array_slice($this->items, 1, $limit);
    }
    /**
     * Return a range
     *
     * @version 1.0
     * @param int $from
     * @param int $to
     * @return void
     */
    public function range(int $from, int $to){
        return array_slice($this->items, $from, $to);
    }
    /**
     * Set value
     *
     * @version 1.0
     * @param [type] $key
     * @param [type] $value
     * @return void
     */
    public function set(mixed $key, mixed $value){
        $this->offsetSet($key, $value);
        return $this;
    }
    /**
     * Get value
     *
     * @version 1.0
     * @param mixed $key
     * @return void
     */
    public function get(mixed $key){
        if($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }
        return null;
    }
    /**
     * Remove Key
     *
     * @version 1.0
     * @param mixed $key
     * @return void
     */
    public function remove(mixed $key){
        $this->offsetUnset($key);
        return $this;
    }
    /**
     * Map Collection
     *
     * @version 1.0
     * @param \Closure $fn
     * @return void
     */
    public function map(Closure $fn){
        $result = [];
        foreach ($this->items as $item) {
          $result[] = $fn($item);
        }
        return $result;
    }
    /**
     * Flatten Array
     *
     * @version 1.0
     * @return void
     */
    public function flatten(){
        $items = [];
        foreach($this->items as $key => $item){
            $items[$key] = $item instanceof \Core\Support\ORM ? $item->asArray() : $item;
        }
        return $items;
    }

    /**
     * Convert Array / Object to JSON
     *
     * @version 1.0
     * @param mixed $data
     * @return void
     */
    public function toJson(){
        return json_encode($this->items);
    }
    /**
     * Split array into chunks
     *
     * @version 1.0
     * @param integer $num
     * @return void
     */
    public function chunk(int $num){
        return array_chunk($this->items, $items, true);
    }
    /**
     * Collapse array into a single array
     *
     * @version 1.0
     * @return void
     */
    public function collapse(){
        return call_user_func_array('array_merge', $this->items);
    }
}