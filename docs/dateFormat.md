# Formating date 

```php
$formatter = Yii::$app->formatter;

$formatter->asDatetime('2011-11-01 14:18', 'Y-MM-d\THH:i') // 2011-11-01T14:18

$formatter->asDate('2011-11-01 14:18') // 2011-11-01

$formatter->asTime('2011-11-01 14:18') // 14:18

$formatter->asTimestamp('2011-11-01 14:18') // 1320157080

$formatter->asDate(1320157080) // 2011-11-01
```
