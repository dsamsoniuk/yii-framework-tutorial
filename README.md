# yii-framework-tutorial
Describe how make things better

* [Format date and time](/docs/dateFormat.md)
* [Add automatically data in model](/docs/modelFilledCustomData.md)
* [Helper array - examples](/docs/helperExamples.md)

```php
\yii\helpers\ArrayHelper::map(User::find()->select(['id', 'name'])->all(), 'optima_id', 'name'); // [1 => 'jan', 2 => 'pawel'...]
```
