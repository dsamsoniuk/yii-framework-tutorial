# Set automatically data model

Custom ActiveRecord class

```php
<?php
namespace app\components\model;

use Yii;
use yii\base\Model;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

class ActiveRecord extends \yii\db\ActiveRecord {

    public function behaviors() {

        $propTimeInsert = $this->filterAvailableProperties(['created_at', 'updated_at']);
        $propTimeUpdate = $this->filterAvailableProperties(['updated_at']);
        $propUserInsert = $this->filterAvailableProperties(['created_by', 'updated_by']);
        $propUserUpdate = $this->filterAvailableProperties(['updated_by']);

        return [
            'blameable' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => $propUserInsert,
                    ActiveRecord::EVENT_BEFORE_UPDATE => $propUserUpdate,
                ],
                'value'=>\Yii::$app->user->id
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => $propTimeInsert,
                    ActiveRecord::EVENT_BEFORE_UPDATE => $propTimeUpdate,
                ],
                'value' => time(),
            ],
        ];
    }
    /**
     * @param array $properies
     * 
     * @return array
     */
    private function filterAvailableProperties(array $properies = []): array{
        foreach ($properies as $i => $prop) {
            if (!$this->hasProperty($prop)) {
                unset($properies[$i]);
            }
        }
        return $properies;
    }
}
```
