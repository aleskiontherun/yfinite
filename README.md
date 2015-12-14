yFinite
-------

yFinite is a Simple State Machine, written in PHP explicitly for Yii2 framework.
It can manage any Stateful object by defining states and transitions between these states.

### Installation (via composer)
```js
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/dizeee/yfinite"
        }
    ],
    "require": {
        "yiisoft/yii2": ">=2.0.6",
        "dizeee/yfinite": "~0.0"
    }
}
```

### Basic usage

```php
use yfinite\StatefulInterface;
use yfinite\StateMachine;

class Monkey implements StatefulInterface
{
    public $state;
    
    public function getFiniteState()
    {
        return $this->state;
    }

    public function setFiniteState($state)
    {
        $this->state = $state;
    }
}

$monkey = new Monkey;

$machine = new StateMachine($monkey, [
    'defaultStateName' => 'sleeping',
    'states' => [
        'sleeping' => [],
        'eating'   => [],
        'pooping'  => ['properties' => ['smells' => 'bad']],
    ],
    'transitions' => [
        'makeBreakfast' => [
            'from'      => ['sleeping'],
            'stateName' => 'eating',
        ],
        'scareToDeath' => [
            'from'      => ['sleeping', 'eating'],
            'stateName' => 'pooping',
       ],
    ],
]);

$machine->apply('makeBreakfast');

```
