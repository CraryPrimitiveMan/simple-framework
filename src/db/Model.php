<?php
namespace sf\db;

use PDO;
use Sf;

/**
 * Model is the base class for data models.
 * @author Harry Sun <sunguangjun@126.com>
 */
class Model implements ModelInterface
{
    /**
     * @var $pdo PDO instance
     */
    public static $pdo;

    /**
     * Get pdo instance
     * @return PDO
     */
    public static function getDb()
    {
        if (empty(static::$pdo)) {
            static::$pdo = Sf::createObject('db')->getDb();
            static::$pdo->exec("set names 'utf8'");
        }

        return static::$pdo;
    }

    /**
     * Declares the name of the database table associated with this Model class.
     * @return string the table name
     */
    public static function tableName()
    {
        return get_called_class();
    }

    /**
     * Returns the primary key **name(s)** for this Model class.
     * @return string[] the primary key name(s) for this Model class.
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * Build a sql where part
     * @param mixed $condition a set of column values
     * @return string
     */
    public static function buildWhere($condition, $params = null)
    {
        if (is_null($params)) {
            $params = [];
        }

        $where = '';
        if (!empty($condition)) {
            $where .= ' where ';
            $keys = [];
            foreach ($condition as $key => $value) {
                array_push($keys, "$key = ?");
                array_push($params, $value);
            }
            $where .= implode(' and ', $keys);
        }
        return [$where, $params];
    }

    /**
     * Convert array to model
     * @param  mixed $row the row data from database
     */
    public static function arr2Model($row)
    {
        $model = new static();
        foreach ($row as $rowKey => $rowValue) {
            $model->$rowKey = $rowValue;
        }
        return $model;
    }

    /**
     * Returns a single model instance by a primary key or an array of column values.
     *
     * ```php
     * // find the first customer whose age is 30 and whose status is 1
     * $customer = Customer::findOne(['age' => 30, 'status' => 1]);
     * ```
     *
     * @param mixed $condition a set of column values
     * @return static|null Model instance matching the condition, or null if nothing matches.
     */
    public static function findOne($condition = null)
    {
        list($where, $params) = static::buildWhere($condition);
        $sql = 'select * from ' . static::tableName() . $where;

        $stmt = static::getDb()->prepare($sql);
        $rs = $stmt->execute($params);

        if ($rs) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!empty($row)) {
                return static::arr2Model($row);
            }
        }

        return null;
    }

    /**
     * Returns a list of models that match the specified primary key value(s) or a set of column values.
     *
     *  ```php
     * // find customers whose age is 30 and whose status is 1
     * $customers = Customer::findAll(['age' => 30, 'status' => 1]);
     * ```
     *
     * @param mixed $condition a set of column values
     * @return array an array of Model instance, or an empty array if nothing matches.
     */
    public static function findAll($condition = null)
    {
        list($where, $params) = static::buildWhere($condition);
        $sql = 'select * from ' . static::tableName() . $where;

        $stmt = static::getDb()->prepare($sql);
        $rs = $stmt->execute($params);
        $models = [];

        if ($rs) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (!empty($row)) {
                    $model = static::arr2Model($row);
                    array_push($models, $model);
                }
            }
        }

        return $models;
    }

    /**
     * Updates models using the provided attribute values and conditions.
     * For example, to change the status to be 2 for all customers whose status is 1:
     *
     * ~~~
     * Customer::updateAll(['status' => 1], ['status' => '2']);
     * ~~~
     *
     * @param array $attributes attribute values (name-value pairs) to be saved for the model.
     * @param array $condition the condition that matches the models that should get updated.
     * An empty condition will match all models.
     * @return integer the number of rows updated
     */
    public static function updateAll($condition, $attributes)
    {
        $sql = 'update ' . static::tableName();
        $params = [];

        if (!empty($attributes)) {
            $sql .= ' set ';
            $params = array_values($attributes);
            $keys = [];
            foreach ($attributes as $key => $value) {
                array_push($keys, "$key = ?");
            }
            $sql .= implode(' , ', $keys);
        }

        list($where, $params) = static::buildWhere($condition, $params);
        $sql .= $where;

        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute($params);
        if ($execResult) {
            $execResult = $stmt->rowCount();
        }
        return $execResult;
    }

    /**
     * Deletes models using the provided conditions.
     * WARNING: If you do not specify any condition, this method will delete ALL rows in the table.
     *
     * For example, to delete all customers whose status is 3:
     *
     * ~~~
     * Customer::deleteAll([status = 3]);
     * ~~~
     *
     * @param array $condition the condition that matches the models that should get deleted.
     * An empty condition will match all models.
     * @return integer the number of rows deleted
     */
    public static function deleteAll($condition)
    {
        list($where, $params) = static::buildWhere($condition);
        $sql = 'delete from ' . static::tableName() . $where;

        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute($params);
        if ($execResult) {
            $execResult = $stmt->rowCount();
        }
        return $execResult;
    }

    /**
     * Inserts the model into the database using the attribute values of this record.
     *
     * Usage example:
     *
     * ```php
     * $customer = new Customer;
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->insert();
     * ```
     *
     * @return boolean whether the model is inserted successfully.
     */
    public function insert()
    {
        $sql = 'insert into ' . static::tableName();
        $params = [];
        $keys = [];
        foreach ($this as $key => $value) {
            array_push($keys, $key);
            array_push($params, $value);
        }
        $holders = array_fill(0, count($keys), '?');
        $sql .= ' (' . implode(' , ', $keys) . ') values ( ' . implode(' , ', $holders) . ')';

        $stmt = static::getDb()->prepare($sql);
        $execResult = $stmt->execute($params);
        $primaryKeys = static::primaryKey();
        foreach ($primaryKeys as $name) {
            // Get the primary key
            $lastId = static::getDb()->lastInsertId($name);
            $this->$name = (int) $lastId;
        }
        return $execResult;
    }

    /**
     * Saves the changes to this model into the database.
     *
     * Usage example:
     *
     * ```php
     * $customer = Customer::findOne(['id' => $id]);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->update();
     * ```
     *
     * @return integer|boolean the number of rows affected.
     * Note that it is possible that the number of rows affected is 0, even though the
     * update execution is successful.
     */
    public function update()
    {
        $primaryKeys = static::primaryKey();
        $condition = [];
        foreach ($primaryKeys as $name) {
            $condition[$name] = isset($this->$name) ? $this->$name : null;
        }

        $attributes = [];
        foreach ($this as $key => $value) {
            if (!in_array($key, $primaryKeys, true)) {
                $attributes[$key] = $value;
            }
        }

        return static::updateAll($condition, $attributes) !== false;
    }

    /**
     * Deletes the model from the database.
     *
     * @return integer|boolean the number of rows deleted.
     * Note that it is possible that the number of rows deleted is 0, even though the deletion execution is successful.
     */
    public function delete()
    {
        $primaryKeys = static::primaryKey();
        $condition = [];
        foreach ($primaryKeys as $name) {
            $condition[$name] = isset($this->$name) ? $this->$name : null;
        }

        return static::deleteAll($condition) !== false;
    }
}
