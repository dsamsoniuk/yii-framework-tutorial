# yii-framework-tutorial
Describe how make things better

* [Format date and time](/docs/dateFormat.md)
* [Add automatically data in model](/docs/modelFilledCustomData.md)
* [Helper array - examples](/docs/helperExamples.md)
* [Model list to simple array](/docs/modelListToArray.md)

```php
$model = new GradingScaleGroup();
$model->load(['id' => 3]);
$modelNotExists = $model;// model should be loaded becaus id gets but it doesn't exists

```
