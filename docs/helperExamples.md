Helper array examples

```php
\yii\helpers\ArrayHelper::map(User::find()->select(['id', 'name'])->all(), 'optima_id', 'name'); // [1 => 'jan', 2 => 'pawel'...]
```
