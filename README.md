# event-attributes
works for php8, pmmp4

## example
```php
class EventListener implements Listener {
    #[Priority(EventPriority::HIGH), HandleCancelled]
    public function onJoin(PlayerJoinEvent $event) {
        
    }
    
    #[NotHandler]
    public function onQuit(PlayerQuitEvent $event) {
        
    }
}
```

### register events
```php
EventAttributes::registerEvents(new EventListener(), $plugin);
```
