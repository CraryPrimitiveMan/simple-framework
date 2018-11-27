<?php

namespace app\models;

use sf\db\Model;

/**
 * User model.
 *
 * @property int $id
 * @property string $name
 * @property int $age
 */
class User extends Model
{
    public static function tableName()
    {
        return 'user';
    }
}
