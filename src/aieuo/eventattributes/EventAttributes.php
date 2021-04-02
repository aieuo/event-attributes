<?php

namespace aieuo\eventattributes;

use aieuo\eventattributes\attributes\HandleCancelled;
use aieuo\eventattributes\attributes\NotHandler;
use aieuo\eventattributes\attributes\Priority;
use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\Server;
use pocketmine\utils\AssumptionFailedError;

class EventAttributes extends PluginBase {

    public static function registerEvents(Listener $listener, Plugin $plugin): void {
        if(!$plugin->isEnabled()){
            throw new PluginException("Plugin attempted to register " . get_class($listener) . " while not enabled");
        }
        $pluginManager = Server::getInstance()->getPluginManager();

        $reflection = new \ReflectionClass(get_class($listener));
        foreach($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method){
            if(!$method->isStatic() and $method->getDeclaringClass()->implementsInterface(Listener::class)){
                $attributes = $method->getAttributes();

                $notHandler = false;
                $priority = EventPriority::NORMAL;
                $handleCancelled = false;

                foreach ($attributes as $attribute) {
                    switch ($attribute->getName()) {
                        case NotHandler::class:
                            $notHandler = true;
                            break;
                        case Priority::class:
                            $priority = $attribute->getArguments()[0];
                            break;
                        case HandleCancelled::class:
                            $handleCancelled = $attribute->getArguments()[0];
                            break;
                    }
                }

                if ($notHandler) {
                    continue;
                }

                $parameters = $method->getParameters();
                if(count($parameters) !== 1){
                    continue;
                }

                $paramType = $parameters[0]->getType();
                //isBuiltin() returns false for builtin classes ..................
                if($paramType instanceof \ReflectionNamedType && !$paramType->isBuiltin()){
                    /** @phpstan-var class-string $paramClass */
                    $paramClass = $paramType->getName();
                    $eventClass = new \ReflectionClass($paramClass);
                    if(!$eventClass->isSubclassOf(Event::class)){
                        continue;
                    }
                }else{
                    continue;
                }

                $handlerClosure = $method->getClosure($listener);
                if($handlerClosure === null) throw new AssumptionFailedError("This should never happen");

                /** @phpstan-var \ReflectionClass<Event> $eventClass */
                $pluginManager->registerEvent($eventClass->getName(), $handlerClosure, $priority, $plugin, $handleCancelled);
            }
        }
    }
}
