# yii-framework-tutorial
Describe how make things better

* [Format date and time](/docs/dateFormat.md)
* [Add automatically data in model](/docs/modelFilledCustomData.md)
* [Helper array - examples](/docs/helperExamples.md)

```php
//1
Example:
        $items = User::find()
            ->select(['first_name'])
            ->indexBy('id')
            ->column();
        // $items return:
            // 1:"Jagoda"
            // 2:"Kamil"
            // 7:"John"
            // 8:"Pan"
            // 9:"Pan"
// 2
$model = new GradingScaleGroup();
$model->load(['id' => 3]);
$modelNotExists = $model;// model should be loaded becaus id gets but it doesn't exists

```
