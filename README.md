# Pipeline

Pipeline is series of steps where output of previous is input of next step. 
Pipeline pattern is useful for breaking down complex logic by encapsulating each step as a stage, or the minimum work that needs to be done by a function.
Each stage operates on common input known as state.
Essentially, a state object is mutated by a series of stage objects. The end result, the end state, is the outcome.

Pipelines are easy to test since it's sufficient to mutate the state and adjust it before passing it to the stage.

## Installation

`composer require simplemehanizm/pipeline`

## How to use

```php
use SimpleMehanizm\Pipeline;

class SetIDStage
{
    public function handle(object $state): object
    {
        $state->id = 1; // change state, set the arbitrary ID value to 1
    
        return $state;
    }    
}

class SetTitleStage
{
    public function handle(object $state): object
    {
        $state->title = 'This is the title';
        
        return $state;
    }
}

$state = new class {
    public function __construct(
        public int $id = 0,
        public string $title = ''
    ){}
}

$stages = [
    SetIDStage::class,
    SetTitleStage::class
];

$pipeline = new Pipeline();

$result = $pipeline->send($state)->through($stages)->then(function(object $state) {
    return [
        'id' => $stage->id,
        'title' => $stage->title 
    ]
});
```