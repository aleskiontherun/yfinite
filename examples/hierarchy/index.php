<?php

namespace yfinite\examples\hierarchy;

use yfinite\Transition;
use yfinite\TransitionEvent;
use yii\base\Event;

$loader = require __DIR__ . '/../../vendor/autoload.php';
$loader->add('yfinite', __DIR__ . '/../../src');
$loader->add('yfinite\examples\hierarchy', __DIR__ . '/../../..');
require_once __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';


$person       = new Person;
$stateMachine = PersonStates::createStateMachine($person);

Event::on(Transition::CLASS, TransitionEvent::BEFORE_TRANSITION, function (TransitionEvent $event)
{
	printf("Transitioned to '%s' state\n", $event->transition->to);
});

$stateMachine->apply(PersonStates::TRANSITION_GO_TO_SLEEP);
$stateMachine->apply(PersonStates::TRANSITION_START_DREAMING);
$stateMachine->apply(PersonStates::TRANSITION_WAKE_UP);
$stateMachine->apply(PersonStates::TRANSITION_GO_TO_WORK);
