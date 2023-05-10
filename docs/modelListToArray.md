# Create simple array from model list

```php
        $items = User::find()
            ->select(['first_name'])
            ->indexBy('id')
            ->column();
            
        // $items result:
            // 1:"Jagoda"
            // 2:"Kamil"
            // 7:"John"
            // 8:"Pan"
            // 9:"Pan"
```
